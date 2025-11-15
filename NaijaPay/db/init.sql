-- Create DB
CREATE DATABASE IF NOT EXISTS naijapay_db;
USE naijapay_db;

-- Table for users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    bvn VARCHAR(20),
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for user accounts and balances
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    account_name VARCHAR(100),
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table for user saved payment cards (NEW TABLE)
CREATE TABLE IF NOT EXISTS payment_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    card_nickname VARCHAR(100),
    card_last_four VARCHAR(4),
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table for transactions log
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_account_id INT,
    to_account_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    description VARCHAR(255),
    txn_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pre-seed a "Victim" user (ID 1)
-- Password is 'password'
INSERT INTO users (id, firstname, lastname, bvn, email, password_hash) VALUES
(1, 'CEO', 'NaijaPay', '11111111111', 'ceo@naijapay.ng', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Create accounts for the CEO (Victim)
INSERT INTO accounts (user_id, account_name, balance) VALUES
(1, 'CEO Savings (Victim)', 100000.00),
(1, 'CEO Domiciliary (Victim)', 50000.00);

-- Create a payment card for the CEO (Victim) - ID will be 1
INSERT INTO payment_cards (user_id, card_nickname, card_last_four) VALUES
(1, 'CEO Platinum VISA', '9999');

-- Note: The attacker (who registers) will be user_id 2
-- Their account will be ID 3, and their card will be ID 2.