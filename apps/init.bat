@echo off
setlocal enabledelayedexpansion

echo ----------------------------------------------------
echo Starting Smart Home Microservices (Windows Mode)
echo ----------------------------------------------------

:: 1. Создание папок
if not exist "mosquitto\config" mkdir "mosquitto\config"
if not exist "mosquitto\data" mkdir "mosquitto\data"
if not exist "mosquitto\log" mkdir "mosquitto\log"

:: 2. Создание конфига Mosquitto
if not exist "mosquitto\config\mosquitto.conf" (
    echo Creating mosquitto.conf...
    (
    echo persistence true
    echo persistence_location /mosquitto/data/
    echo log_dest stdout
    echo listener 1883
    echo allow_anonymous true
    ) > mosquitto\config\mosquitto.conf
)

:: 3. Запуск Docker
echo Building and starting containers...
docker-compose up --build -d

:: 4. Ожидание PostgreSQL
echo Waiting for PostgreSQL to initialize... 
timeout /t 7 /nobreak >nul

set /a attempt=1
:check_pg
docker exec smarthome-postgres pg_isready -U postgres -d smarthome >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] PostgreSQL is ready! [cite: 3]
    goto :init_db
)

if %attempt% geq 20 (
    echo [ERROR] PostgreSQL timeout.
    pause
    exit /b 1
)

set /a attempt+=1
echo [%attempt%/20] DB not ready yet, retrying...
timeout /t 3 /nobreak >nul
goto :check_pg

:: 5. Применение схем
:init_db
echo Applying Monolith Schema (init.sql)...
if exist "smart_home\init.sql" (
    type smart_home\init.sql | docker exec -i smarthome-postgres psql -U postgres -d smarthome
) else (
    echo [WARNING] smart_home\init.sql not found!
)

echo Applying Security Schema (init-security.sql)...
if exist "init-security.sql" (
    type init-security.sql | docker exec -i smarthome-postgres psql -U postgres -d smarthome
    echo Schemas applied successfully.
)

echo.
echo ==================================================== [cite: 4]
echo SUCCESS: All schemas applied and services are running.
echo Monolith: http://localhost:8080
echo Identity: http://localhost:8082
echo ====================================================
pause