CREATE DATABASE IF NOT EXISTS hams;
USE hams;

-- Admin table
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO admin (email, password) VALUES
('admin@hams.com', 'admin123');

-- Doctor table
CREATE TABLE doctor (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255)
);

INSERT INTO doctor (name, email, password) VALUES
('Dr. John Doe', 'doctor@hams.com', '12345');

-- Patient table
CREATE TABLE patient (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255)
);

