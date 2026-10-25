-- Safe to import from phpMyAdmin's home page or with pet_grooming_db selected.
-- CREATE DATABASE / CREATE TABLE IF NOT EXISTS preserve existing data.
CREATE DATABASE IF NOT EXISTS pet_grooming_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pet_grooming_db;

-- Parent table: pet owners.
CREATE TABLE IF NOT EXISTS pet_owners (
    owner_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Child table: each pet belongs to one existing owner.
CREATE TABLE IF NOT EXISTS pets (
    pet_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_name VARCHAR(100) NOT NULL,
    species VARCHAR(50) NOT NULL,
    breed VARCHAR(50) NULL,
    grooming_date DATE NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    owner_id INT NOT NULL,
    CONSTRAINT fk_pets_pet_owner FOREIGN KEY (owner_id)
        REFERENCES pet_owners(owner_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
