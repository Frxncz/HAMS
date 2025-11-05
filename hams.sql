CREATE DATABASE IF NOT EXISTS hams;
USE hams;

-- Admin table
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO admin (email, password) VALUES
('admin@hams.com', 'admin123');

-- Doctor table
CREATE TABLE doctor (
  docid INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255)
) ENGINE=InnoDB;

INSERT INTO doctor (name, email, password) VALUES
('Dr. John Doe', 'doctor@hams.com', '12345');

-- Patient table
CREATE TABLE patient (
  pid INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255)
) ENGINE=InnoDB;

-- ✅ Appointments table (fixed)
CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT,  -- make this NULLABLE because of ON DELETE SET NULL
  appt_date DATE NOT NULL,
  appt_time TIME NOT NULL,
  purpose ENUM('regular','new_patient','follow_up') NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patient(pid) ON DELETE CASCADE,
  FOREIGN KEY (doctor_id) REFERENCES doctor(docid) ON DELETE SET NULL
) ENGINE=InnoDB;
