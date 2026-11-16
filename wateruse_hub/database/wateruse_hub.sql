CREATE DATABASE IF NOT EXISTS wateruse_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wateruse_hub;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS audit_logs, notifications, service_actions, complaints, payments, bills, meter_readings, users, customers, settings;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE customers (
 id INT AUTO_INCREMENT PRIMARY KEY,
 account_number VARCHAR(30) NOT NULL UNIQUE,
 full_name VARCHAR(150) NOT NULL,
 phone VARCHAR(30) NOT NULL UNIQUE,
 email VARCHAR(150) NULL UNIQUE,
 address VARCHAR(255) NULL,
 meter_number VARCHAR(60) NOT NULL UNIQUE,
 previous_reading DECIMAL(12,2) NOT NULL DEFAULT 0,
 status ENUM('active','inactive','disconnected') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','staff','customer') NOT NULL DEFAULT 'staff',
 customer_id INT NULL UNIQUE,
 status ENUM('active','inactive') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_user_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE meter_readings (
 id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id INT NOT NULL,
 billing_period CHAR(7) NOT NULL,
 previous_reading DECIMAL(12,2) NOT NULL,
 reading_value DECIMAL(12,2) NOT NULL,
 consumption DECIMAL(12,2) NOT NULL,
 reading_date DATE NOT NULL,
 recorded_by INT NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unique_customer_period(customer_id,billing_period),
 CONSTRAINT fk_reading_customer FOREIGN KEY(customer_id) REFERENCES customers(id),
 CONSTRAINT fk_reading_user FOREIGN KEY(recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE bills (
 id INT AUTO_INCREMENT PRIMARY KEY,
 bill_number VARCHAR(40) NOT NULL UNIQUE,
 customer_id INT NOT NULL,
 reading_id INT NOT NULL UNIQUE,
 billing_period CHAR(7) NOT NULL,
 consumption DECIMAL(12,2) NOT NULL,
 rate_per_m3 DECIMAL(12,2) NOT NULL,
 total_amount DECIMAL(12,2) NOT NULL,
 balance DECIMAL(12,2) NOT NULL,
 due_date DATE NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_bill_customer FOREIGN KEY(customer_id) REFERENCES customers(id),
 CONSTRAINT fk_bill_reading FOREIGN KEY(reading_id) REFERENCES meter_readings(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 bill_id INT NOT NULL,
 customer_id INT NOT NULL,
 amount DECIMAL(12,2) NOT NULL,
 method ENUM('cash','bank','other') NOT NULL DEFAULT 'cash',
 reference VARCHAR(100) NULL,
 payment_date DATE NOT NULL,
 recorded_by INT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_payment_bill FOREIGN KEY(bill_id) REFERENCES bills(id),
 CONSTRAINT fk_payment_customer FOREIGN KEY(customer_id) REFERENCES customers(id),
 CONSTRAINT fk_payment_user FOREIGN KEY(recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE complaints (
 id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id INT NOT NULL,
 subject VARCHAR(180) NOT NULL,
 description TEXT NOT NULL,
 status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
 resolution_notes TEXT NULL,
 assigned_to INT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 resolved_at DATETIME NULL,
 CONSTRAINT fk_complaint_customer FOREIGN KEY(customer_id) REFERENCES customers(id),
 CONSTRAINT fk_complaint_user FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE service_actions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id INT NOT NULL,
 action ENUM('disconnect','reconnect') NOT NULL,
 reason TEXT NOT NULL,
 performed_by INT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_service_customer FOREIGN KEY(customer_id) REFERENCES customers(id),
 CONSTRAINT fk_service_user FOREIGN KEY(performed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE notifications (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 message TEXT NOT NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_notification_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE settings (
 setting_key VARCHAR(80) PRIMARY KEY,
 setting_value TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 action VARCHAR(100) NOT NULL,
 details TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_audit_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO settings(setting_key,setting_value) VALUES
('utility_name','WaterUse Hub'),
('rate_per_m3','100');
