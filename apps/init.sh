#!/bin/bash
set -e

echo "----------------------------------------------------"
echo "Starting Smart Home Microservices Infrastructure..."
echo "----------------------------------------------------"

# 1. Подготовка инфраструктуры
mkdir -p mosquitto/config mosquitto/data mosquitto/log

if [ ! -f mosquitto/config/mosquitto.conf ]; then
    echo "Creating mosquitto.conf..."
    cat <<EOT > mosquitto/config/mosquitto.conf
persistence true
persistence_location /mosquitto/data/
log_dest stdout
listener 1883
allow_anonymous true
EOT
fi

# 2. Запуск контейнеров
echo "Building and starting containers..."
docker-compose up --build -d

# 3. Ожидание PostgreSQL
echo "Waiting for PostgreSQL to be ready..." 
MAX_RETRIES=30
COUNT=0

until docker exec smarthome-postgres pg_isready -U postgres -d smarthome > /dev/null 2>&1 || [ $COUNT -eq $MAX_RETRIES ]; do
  echo "Waiting for DB... ($((COUNT+1))/$MAX_RETRIES)"
  sleep 2
  COUNT=$((COUNT+1))
done

if [ $COUNT -eq $MAX_RETRIES ]; then
  echo "Error: PostgreSQL timeout."
  exit 1
fi

echo "PostgreSQL is ready!" 

# 4. Инициализация БД (Принудительное применение обоих файлов)
echo "Applying Monolith DB schemas (init.sql)..."
if [ -f smart_home/init.sql ]; then
    docker exec -i smarthome-postgres psql -U postgres -d smarthome < smart_home/init.sql
fi

if [ -f init-security.sql ]; then
    echo "Applying Security DB schemas (init-security.sql)..."
    docker exec -i smarthome-postgres psql -U postgres -d smarthome < init-security.sql
fi

echo "===================================================="
echo "Infrastructure is UP and schemas applied!"
echo "Legacy Monolith: http://localhost:8080"
echo "Identity API:    http://localhost:8082"
echo "WS Stream:       ws://localhost:8084"
echo "===================================================="