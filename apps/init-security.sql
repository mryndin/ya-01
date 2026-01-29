-- Дополнение к init-security.sql
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    login VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS houses (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address TEXT
);

CREATE TABLE IF NOT EXISTS user_houses (
    user_id UUID REFERENCES users(id),
    house_id UUID REFERENCES houses(id),
    access_level VARCHAR(20),
    PRIMARY KEY (user_id, house_id)
);

CREATE TABLE IF NOT EXISTS device_ownership (
    device_id INT PRIMARY KEY, -- Ссылка на sensors.id из Legacy DB [cite: 1, 2]
    house_id UUID REFERENCES houses(id),
    auth_key VARCHAR(255) UNIQUE NOT NULL
);