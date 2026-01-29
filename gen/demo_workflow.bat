@echo off
setlocal enabledelayedexpansion

echo =====================================================
echo    SMART HOME MVP: ROBUST WORKFLOW
echo =====================================================

:: --- ЧИСТКА ПРЕДЫДУЩИХ ФАЙЛОВ ---
if exist house.json del house.json
if exist user.json del user.json

:: --- ШАГ 0: ГАРАНТИЯ ДАТЧИКА В МОНОЛИТЕ ---
echo [0/6] Ensuring Legacy Sensor exists...
docker exec -i smarthome-postgres psql -U postgres -d smarthome -c "INSERT INTO sensors (id, name, type, location, unit, status) VALUES (1, 'Sensor-001', 'temp', 'Kitchen', 'C', 'active') ON CONFLICT (id) DO NOTHING;" >nul 2>&1

:: --- ШАГ 1: ВХОД АДМИНА ---
echo.
echo [1/6] Admin Login...
curl -s -X POST http://localhost:8082/auth/login ^
    -H "Content-Type: application/json" ^
    -d "{\"login\": \"admin\", \"password\": \"admin123\"}"
echo.

:: --- ШАГ 2: СОЗДАНИЕ ДОМА ---
echo.
echo [2/6] Registering House...
:: 1. Сохраняем ответ в файл
curl -s -X POST http://localhost:8082/api/v1/houses ^
    -H "Content-Type: application/json" ^
    -d "{\"name\": \"My Country House\", \"city\": \"Moscow\", \"address\": \"Lenina 1\"}" > house.json

:: 2. Читаем ID из файла через PowerShell
for /f "usebackq tokens=*" %%i in (`powershell -Command "(Get-Content house.json | ConvertFrom-Json).id"`) do set HOUSE_ID=%%i
echo    House Created: !HOUSE_ID!

:: ПРОВЕРКА НА ОШИБКУ
if "!HOUSE_ID!"=="" (
    echo [ERROR] Failed to create House. Check identity-service logs.
    type house.json
    pause
    exit /b
)

:: --- ШАГ 3: РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ ---
echo.
echo [3/6] Registering Client User...
curl -s -X POST http://localhost:8082/api/v1/users ^
    -H "Content-Type: application/json" ^
    -d "{\"login\": \"client_user_%RANDOM%\", \"password\": \"pass123\", \"role\": \"USER\"}" > user.json

for /f "usebackq tokens=*" %%i in (`powershell -Command "(Get-Content user.json | ConvertFrom-Json).id"`) do set USER_ID=%%i
echo    User Created: !USER_ID!

if "!USER_ID!"=="" (
    echo [ERROR] Failed to create User. Check logs.
    type user.json
    pause
    exit /b
)

:: --- ШАГ 4: СВЯЗКА (АДМИН ДАЕТ ДОСТУП) ---
echo.
echo [4/6] Assigning User to House...
curl -s -X POST http://localhost:8082/api/v1/houses/!HOUSE_ID!/assign-user ^
    -H "Content-Type: application/json" ^
    -d "{\"user_id\": \"!USER_ID!\"}"
echo.

:: --- ШАГ 5: ПОЛЬЗОВАТЕЛЬ ПРИВЯЗЫВАЕТ ДАТЧИК ---
echo.
echo [5/6] User links Legacy Sensor #1 to the House...
curl -s -X POST http://localhost:8082/api/v1/houses/!HOUSE_ID!/devices ^
    -H "Content-Type: application/json" ^
    -d "{\"deviceId\": 1, \"authKey\": \"secret_key_777\"}"
echo.

:: --- ШАГ 6: ТЕЛЕМЕТРИЯ ---
echo.
echo [6/6] Emulating Sensor Data (Value: 25.5)...
:: Меняем значение на 25.5, чтобы убедиться, что это НОВОЕ измерение
docker exec -it mqtt-broker mosquitto_pub -t "device/telemetry" -m "{\"device_id\": 1, \"value\": 25.5, \"auth_key\": \"secret_key_777\"}"

echo.
echo =====================================================
echo    RESULT VERIFICATION (CHECKING MONOLITH DB)
echo =====================================================
:: Ждем 1 секунду, чтобы данные успели дойти
timeout /t 1 >nul
curl -s http://localhost:8080/api/v1/sensors/1
echo.
echo.

:: Чистим за собой
del house.json user.json
pause