-- Connect to the database
\c smarthome;

-- 1. Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    login VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'USER',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 2. Таблица домов
CREATE TABLE IF NOT EXISTS houses (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- 3. Связь Пользователь - Дом
CREATE TABLE IF NOT EXISTS user_houses (
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    house_id UUID REFERENCES houses(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, house_id)
);

-- 4. Владение устройствами (Связь с монолитом)
CREATE TABLE IF NOT EXISTS device_ownership (
    device_id INTEGER PRIMARY KEY, -- ID из таблицы sensors монолита
    house_id UUID REFERENCES houses(id) ON DELETE CASCADE,
    auth_key VARCHAR(100) NOT NULL,
    last_sync TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ИНИЦИАЛИЗАЦИЯ: Только Админ
-- Логин: admin, Пароль: admin123 (хеширован)
INSERT INTO users (login, password_hash, role) 
VALUES ('admin', '$2a$10$e8.Zp.H5B.uLMY8U.p7uReeQW1Z.vM2.8p.1234567890abcdef', 'ADMIN')
ON CONFLICT (login) DO NOTHING;