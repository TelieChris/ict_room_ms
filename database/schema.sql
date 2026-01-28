-- ICT Room Asset Management System
-- MySQL/MariaDB schema (normalized) + minimal seed data

SET sql_mode = 'STRICT_ALL_TABLES';
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE, -- admin, teacher, viewer
  description VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id INT UNSIGNED NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asset_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS locations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE -- ICT Room, Office, Classroom...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  asset_code VARCHAR(40) NOT NULL UNIQUE,
  asset_name VARCHAR(120) NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  brand VARCHAR(80) NULL,
  model VARCHAR(80) NULL,
  serial_number VARCHAR(80) NULL,
  purchase_date DATE NULL,
  asset_condition ENUM('New','Good','Fair','Damaged') NOT NULL DEFAULT 'Good',
  status ENUM('Available','In Use','Maintenance','Lost') NOT NULL DEFAULT 'Available',
  location_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_assets_category FOREIGN KEY (category_id) REFERENCES asset_categories(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_assets_location FOREIGN KEY (location_id) REFERENCES locations(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_assets_status (status),
  INDEX idx_assets_condition (asset_condition),
  INDEX idx_assets_category (category_id),
  INDEX idx_assets_location (location_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assignment targets are normalized into a single table for simplicity on shared hosting.
-- You can extend with separate teacher/classes tables later.
CREATE TABLE IF NOT EXISTS asset_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  asset_id INT UNSIGNED NOT NULL,
  assigned_to_type ENUM('ICT Room','Teacher','Class/Department') NOT NULL,
  assigned_to_name VARCHAR(120) NOT NULL,
  assigned_date DATE NOT NULL,
  expected_return_date DATE NULL,
  returned_date DATE NULL,
  notes TEXT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_assign_asset FOREIGN KEY (asset_id) REFERENCES assets(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_assign_user FOREIGN KEY (created_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_assign_asset (asset_id),
  INDEX idx_assign_open (returned_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS maintenance_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  asset_id INT UNSIGNED NOT NULL,
  issue_description TEXT NOT NULL,
  reported_date DATE NOT NULL,
  action_taken TEXT NULL,
  technician_name VARCHAR(120) NULL,
  cost DECIMAL(10,2) NULL,
  status ENUM('Open','In Progress','Resolved') NOT NULL DEFAULT 'Open',
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_maint_asset FOREIGN KEY (asset_id) REFERENCES assets(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_maint_user FOREIGN KEY (created_by) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_maint_asset (asset_id),
  INDEX idx_maint_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(50) NOT NULL,      -- e.g. LOGIN, ASSET_CREATE
  entity VARCHAR(50) NULL,          -- assets, maintenance, users...
  entity_id VARCHAR(50) NULL,       -- store as string to support various PKs
  description VARCHAR(255) NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_audit_created (created_at),
  INDEX idx_audit_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed roles
INSERT INTO roles (name, description) VALUES
('admin','IT Technician - full access'),
('teacher','ICT Teacher/Lab Assistant - manage usage & report issues'),
('viewer','School management - view only')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- Seed reference tables (you can add more in the UI later)
INSERT INTO asset_categories (name) VALUES
('Desktop'),('Laptop'),('Printer'),('Router'),('Switch'),('Projector'),('UPS')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO locations (name) VALUES
('ICT Room'),('Office'),('Classroom'),('Library')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Seed default admin user (CHANGE PASSWORD AFTER FIRST LOGIN)
-- password: Admin@12345
INSERT INTO users (role_id, username, full_name, email, password_hash, is_active)
SELECT r.id, 'admin', 'System Administrator', NULL,
       '$2y$10$6jHY0G4mWZtWQw2m8EwZ6e8GvAqgU8pFQ7R8bL4x7r5l7lG6o8C2a', 1
FROM roles r
WHERE r.name='admin'
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), is_active=VALUES(is_active);


