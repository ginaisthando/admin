-- Joe's Coaches Admin System Database Schema

CREATE DATABASE IF NOT EXISTS joes_coaches_admin;
USE joes_coaches_admin;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS interview_applications;
DROP TABLE IF EXISTS interviews;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS admin_sessions;

-- 1. Admin Users Table - for authentication and account management
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('super_admin', 'admin', 'hr_manager') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- 2. Admin Sessions Table - for session management
CREATE TABLE admin_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE
);

-- 3. Departments Table - managing company departments
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    head_of_department VARCHAR(100),
    budget DECIMAL(12,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. Employees Table - storing all employee information
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    position VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    hire_date DATE NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.png',
    address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    employment_status ENUM('active', 'inactive', 'terminated', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT
);

-- 5. Interviews Table - managing interview scheduling
CREATE TABLE interviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    position VARCHAR(100) NOT NULL,
    candidate_name VARCHAR(100),
    candidate_email VARCHAR(100),
    candidate_phone VARCHAR(20),
    interview_date DATETIME,
    interview_type ENUM('phone', 'video', 'in_person') DEFAULT 'in_person',
    interviewer_notes TEXT,
    status ENUM('scheduled', 'completed', 'cancelled', 'pending_review') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT
);

-- 6. Interview Applications Table - tracking application statuses
CREATE TABLE interview_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    interview_id INT NOT NULL,
    application_status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
    decision_date TIMESTAMP NULL,
    decision_notes TEXT,
    decided_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (interview_id) REFERENCES interviews(id) ON DELETE CASCADE,
    FOREIGN KEY (decided_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_employees_department ON employees(department_id);
CREATE INDEX idx_employees_email ON employees(email);
CREATE INDEX idx_employees_status ON employees(employment_status);
CREATE INDEX idx_interviews_department ON interviews(department_id);
CREATE INDEX idx_interviews_status ON interviews(status);
CREATE INDEX idx_interviews_date ON interviews(interview_date);
CREATE INDEX idx_admin_sessions_token ON admin_sessions(session_token);
CREATE INDEX idx_admin_sessions_expires ON admin_sessions(expires_at);

-- Insert sample departments based on the code
INSERT INTO departments (name, description, head_of_department, budget, is_active) VALUES
('Tech Department', 'Information Technology and Software Development', 'CTO', 250000.00, TRUE),
('Marketing Department', 'Marketing and Brand Management', 'Pamela Peterson', 180000.00, TRUE),
('Executive Department', 'Executive Leadership and Strategy', 'Joe Adams', 500000.00, TRUE),
('Transport Department', 'Fleet Management and Transportation Services', 'Jessica Bloom', 320000.00, TRUE),
('HR Department', 'Human Resources and Talent Management', NULL, 150000.00, TRUE);

-- Insert sample employees based on the HTML data
INSERT INTO employees (employee_number, first_name, last_name, email, position, department_id, salary, hire_date, profile_image) VALUES
('EMP001', 'Ashton', 'Jacobs', 'ajacobs@jcoaches.com', 'Developer', 1, 35000.00, '2023-01-15', 'hs1.png'),
('EMP002', 'Pamela', 'Peterson', 'ppeterson@jcoaches.com', 'Head of Marketing', 2, 45000.00, '2022-08-10', 'hs2.png'),
('EMP003', 'Joe', 'Adams', 'jadams@jcoaches.com', 'CEO', 3, 80000.00, '2020-01-01', 'hs3.png'),
('EMP004', 'Jessica', 'Bloom', 'jbloom@jcoaches.com', 'Head of Transport', 4, 55000.00, '2022-11-20', 'hs4.png');

-- Insert sample interviews based on the HTML data
INSERT INTO interviews (department_id, position, status) VALUES
(2, 'Social Media Content Specialist', 'scheduled'),
(4, 'Advanced Coach Driver', 'scheduled'),
(1, 'Full-stack Developer', 'scheduled');

-- Insert corresponding interview applications
INSERT INTO interview_applications (interview_id, application_status) VALUES
(1, 'pending'),
(2, 'pending'),
(3, 'pending');

-- Default admin user default password
INSERT INTO admin_users (username, email, password_hash, first_name, last_name, role) VALUES
('admin', 'admin@jcoaches.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'super_admin');

-- View for employee details with department names
CREATE VIEW employee_details AS
SELECT 
    e.id,
    e.employee_number,
    e.first_name,
    e.last_name,
    e.email,
    e.phone,
    e.position,
    d.name as department_name,
    e.salary,
    e.hire_date,
    e.profile_image,
    e.employment_status,
    e.created_at,
    e.updated_at
FROM employees e
JOIN departments d ON e.department_id = d.id;

-- View for interview details with department names
CREATE VIEW interview_details AS
SELECT 
    i.id,
    i.position,
    d.name as department_name,
    i.candidate_name,
    i.candidate_email,
    i.interview_date,
    i.interview_type,
    i.status,
    ia.application_status,
    ia.decision_date,
    i.created_at
FROM interviews i
JOIN departments d ON i.department_id = d.id
LEFT JOIN interview_applications ia ON i.id = ia.interview_id;

-- Show table structure
SHOW TABLES;
