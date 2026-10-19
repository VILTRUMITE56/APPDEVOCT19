-- Database: pet_grooming_db

-- Parent table: Pet Owners
CREATE TABLE pet_owners (
    owner_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100)
);

-- Child table: Pets (linked to a pet owner via foreign key)
CREATE TABLE pets (
    pet_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_name VARCHAR(100) NOT NULL,
    species VARCHAR(50) NOT NULL,
    breed VARCHAR(50),
    grooming_date DATE,
    status VARCHAR(50) DEFAULT 'Pending',
    owner_id INT NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES pet_owners(owner_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
