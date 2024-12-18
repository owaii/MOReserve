CREATE TABLE personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mothersMaidenName VARCHAR(200) NOT NULL,
    country VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    street VARCHAR(100) NOT NULL,
    buildingNumber VARCHAR(10) NOT NULL,
    apartmentNumber VARCHAR(10) NOT NULL,
    postal VARCHAR(10) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personalID INT,
    icon VARCHAR(255) DEFAULT 'astrid.webp',
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(200) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    pesel CHAR(11) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(15) NOT NULL UNIQUE,
    created DATE DEFAULT CURRENT_DATE,
    login DATE,
    balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (personalID) REFERENCES personal(id) ON DELETE CASCADE
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    toUserID INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT 'przelew',
    created DATE DEFAULT CURRENT_DATE,
    time time NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (toUserID) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    friendID INT NOT NULL,
    transactions INT DEFAULT 0,
    FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (friendID) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userID INT,
    number BIGINT UNIQUE,
    date VARCHAR(5) NOT NULL,
    holderName VARCHAR(100) NOT NULL,
    cvv INT(3) NOT NULL,
    status ENUM('active', 'inactive', 'removed') NOT NULL,
    created DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (userID) REFERENCES users(id) ON DELETE CASCADE
);

-- Initialize the starting number for cards
SET @start_number = 7000000000000000;

-- Create a trigger to auto-increment card numbers
DELIMITER $$
CREATE TRIGGER before_insert_cards
BEFORE INSERT ON cards
FOR EACH ROW
BEGIN
    IF NEW.number IS NULL OR NEW.number = 0 THEN
        SET NEW.number = @start_number;
        SET @start_number = @start_number + 1;
    END IF;
END$$
DELIMITER ;
