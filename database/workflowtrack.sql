-- WorkFlowTrack Database Schema
-- Group 9 | SE-II

CREATE DATABASE IF NOT EXISTS workflowtrack;
USE workflowtrack;

-- USERS TABLE
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM("admin", "manager", "member", "department") NOT NULL,
    status ENUM("active", "inactive") DEFAULT "active",
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TASKS TABLE
CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    priority ENUM("Low", "Medium", "High") NOT NULL,
    deadline DATE NOT NULL,
    status ENUM("Pending", "In Progress", "Done") DEFAULT "Pending",
    assigned_to INT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(user_id),
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- NOTIFICATIONS TABLE
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- AUDIT LOG TABLE
CREATE TABLE audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    old_value VARCHAR(100),
    new_value VARCHAR(100),
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- SEED DATA: Default users
INSERT INTO users (username, email, password, role) VALUES
("admin", "admin@workflowtrack.com", "y02IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "admin"),
("manager", "manager@workflowtrack.com", "y02IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "manager"),
("member1", "member1@workflowtrack.com", "y02IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "member"),
("deptmgr", "dept@workflowtrack.com", "y02IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "department");

-- SEED DATA: Sample tasks
INSERT INTO tasks (title, description, priority, deadline, status, assigned_to, created_by) VALUES
("Setup project repository", "Initialize GitHub repo and branch structure", "High", "2026-04-20", "Done", 2, 2),
("Design login screen", "Create HTML and CSS for login page", "High", "2026-04-22", "In Progress", 3, 2),
("Build dashboard", "Implement KPI tiles and chart", "Medium", "2026-04-25", "Pending", 3, 2),
("Implement task CRUD", "Create, edit, delete task functionality", "High", "2026-04-28", "Pending", 3, 2),
("Workflow tracking", "Status transition logic and validation", "Medium", "2026-04-30", "Pending", 3, 2);
