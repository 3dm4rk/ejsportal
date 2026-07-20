-- Create database
CREATE DATABASE IF NOT EXISTS ejs_portal;
USE ejs_portal;

-- Roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'Full system access'),
('hr', 'Human Resources - can view worker reports'),
('worker', 'Regular employee - can log attendance');

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Insert default admin
-- Password: admin123 (hashed using password_hash)
INSERT INTO users (username, password, full_name, email, role_id)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@ejs.com', 1);

-- Attendance logs (main clock in/out)
CREATE TABLE attendance_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    job_login DATETIME,
    job_logout DATETIME,
    total_hours DECIMAL(5,2) DEFAULT 0.00,
    overtime_hours DECIMAL(5,2) DEFAULT 0.00,
    late_minutes INT DEFAULT 0,
    status ENUM('Present','Late','Absent','Leave') DEFAULT 'Present',
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date)
);

-- Break records (Break In / Break Out)
CREATE TABLE break_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attendance_id INT NOT NULL,
    break_in DATETIME,
    break_out DATETIME,
    duration_minutes INT DEFAULT 0,
    FOREIGN KEY (attendance_id) REFERENCES attendance_logs(id) ON DELETE CASCADE
);

-- Lunch records (Lunch In / Lunch Out)
CREATE TABLE lunch_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attendance_id INT NOT NULL,
    lunch_in DATETIME,
    lunch_out DATETIME,
    duration_minutes INT DEFAULT 0,
    FOREIGN KEY (attendance_id) REFERENCES attendance_logs(id) ON DELETE CASCADE
);

-- Activity logs for audit
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- System settings (key-value store)
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO system_settings (setting_key, setting_value) VALUES
('company_name', 'EJS Portal'),
('timezone', 'Asia/Manila'),
('work_hours_per_day', '8'),
('overtime_rate', '1.5'),
('late_threshold_minutes', '15');