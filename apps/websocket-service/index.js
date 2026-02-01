const WebSocket = require('ws');
const mqtt = require('mqtt');

// Настройки из переменных окружения
const MQTT_HOST = process.env.MQTT_HOST || 'mqtt-broker';
const MQTT_PORT = process.env.MQTT_PORT || 1883;
const WS_PORT = process.env.PORT || 8084;

// 1. Инициализация WebSocket сервера
const wss = new WebSocket.Server({ port: WS_PORT }, () => {
    console.log(`WebSocket Server started on port ${WS_PORT}`);
});

// 2. Подключение к MQTT брокеру
const mqttClient = mqtt.connect(`mqtt://${MQTT_HOST}:${MQTT_PORT}`);

mqttClient.on('connect', () => {
    console.log('Connected to MQTT Broker');
    // Подписываемся на тот же топик, что и Telemetry Service
    mqttClient.subscribe('device/telemetry');
});

// 3. Обработка входящих сообщений от MQTT
mqttClient.on('message', (topic, message) => {
    try {
        const payload = JSON.parse(message.toString());
        console.log(`Forwarding telemetry: Device ${payload.device_id}`);

        // Рассылаем данные всем подключенным WebSocket клиентам
        wss.clients.forEach((client) => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(JSON.stringify({
                    event: 'telemetry_update',
                    data: payload,
                    timestamp: new Date().toISOString()
                }));
            }
        });
    } catch (err) {
        console.error('Error parsing MQTT message:', err);
    }
});

// Обработка подключений клиентов
wss.on('connection', (ws) => {
    console.log('New client connected');
    ws.send(JSON.stringify({ message: 'Welcome to SmartHome Real-time Stream' }));
    
    ws.on('close', () => console.log('Client disconnected'));
});