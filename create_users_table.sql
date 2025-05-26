USE onlinecomputerstore_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a test admin user (password: admin123)
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$8K1p/a0dL1LXMIZoIqPK6.5M/7KQf8h5h5h5h5h5h5h5h5h5h5h5', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Insert a test regular user (password: user123)
INSERT INTO users (username, password, role) 
VALUES ('user', '$2y$10$8K1p/a0dL1LXMIZoIqPK6.5M/7KQf8h5h5h5h5h5h5h5h5h5h5h5', 'user')
ON DUPLICATE KEY UPDATE id=id; 