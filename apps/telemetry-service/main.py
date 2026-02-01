import os
import json
import requests
import paho.mqtt.client as mqtt

# Настройки из переменных окружения (заданы в docker-compose)
MQTT_HOST = os.getenv('MQTT_HOST', 'mqtt-broker')
MQTT_PORT = int(os.getenv('MQTT_PORT', 1883))
MONOLITH_URL = os.getenv('MONOLITH_URL', 'http://app:8080')

# Топик, который слушают датчики
TOPIC = "device/telemetry"

def on_connect(client, userdata, flags, rc):
    print(f"Connected to MQTT Broker with result code {rc}")
    # Подписываемся на топик телеметрии
    client.subscribe(TOPIC)

def on_message(client, userdata, msg):
    try:
        # Парсим входящее JSON сообщение
        payload = json.loads(msg.payload.decode())
        device_id = payload.get('device_id')
        value = payload.get('value')
        auth_key = payload.get('auth_key') # Ключ из таблицы device_ownership

        print(f"Received telemetry: Device {device_id}, Value {value}")

        if device_id is not None and value is not None:
            # Отправляем данные в Legacy Monolith
            # Эндпоинт взят из Postman коллекции 
            url = f"{MONOLITH_URL}/api/v1/sensors/{device_id}/value"
            data = {
                "value": value,
                "status": "active"
            }
            
            response = requests.patch(url, json=data)
            
            if response.status_code == 200:
                print(f"Successfully synced Device {device_id} with Monolith")
            else:
                print(f"Failed to sync with Monolith: {response.status_code}")

    except Exception as e:
        print(f"Error processing message: {e}")

# Инициализация MQTT клиента
client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

print(f"Starting Telemetry Service, connecting to {MQTT_HOST}...")
client.connect(MQTT_HOST, MQTT_PORT, 60)

# Вечный цикл ожидания сообщений
client.loop_forever()