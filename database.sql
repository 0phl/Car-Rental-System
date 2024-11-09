CREATE DATABASE IF NOT EXISTS car_rental_db;
USE car_rental_db;

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    model VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    seats INT NOT NULL,
    transmission VARCHAR(50) NOT NULL,
    fuel_type VARCHAR(50) NOT NULL,
    cost_per_day DECIMAL(10,2) NOT NULL,
    features TEXT NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    status ENUM('available', 'booked') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    car_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    pickup_datetime DATETIME NOT NULL,
    return_datetime DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    reference_no VARCHAR(20) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id)
);

-- Insert default admin account
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$8KbVRZYB.pG/ozxIuoZyAeXH1c5nqq.pXD9yFc.XzXBqWzNxB3YvG');
-- Default password is 'admin123' 