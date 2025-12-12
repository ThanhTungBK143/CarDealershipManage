DROP DATABASE IF EXISTS cardealer;
CREATE DATABASE cardealer;
USE cardealer;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'sale',
    login_token VARCHAR(255),
    token_expire INT(11)
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address VARCHAR(255) NOT NULL
);

CREATE TABLE cars (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(30) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg'
);

CREATE TABLE sales_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    sales_user_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    transaction_date DATETIME NOT NULL,
    FOREIGN KEY (sales_user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES cars(product_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

INSERT INTO users (id, username, password, email, phone, role) VALUES 
(1, 'admin', MD5('12345'), 'admin@gmail.com', '0999999999', 'admin'),
(2, 'sale1', MD5('pass123'), 'sale1@dealer.com', '0987654321', 'sale'),
(3, 'sale2', MD5('pass123'), 'sale2@dealer.com', '0912312312', 'sale');

INSERT INTO cars (product_id, make, model, year, color, quantity, price, image) VALUES
(1, 'VinFast', 'VF8', 2024, 'Red', 10, 1050000000.00, 'VinFast-VF8-1.jpg'),
(2, 'Toyota', 'Vios', 2023, 'Silver', 25, 520000000.00, 'toyota_vios_2023.jpg'),
(3, 'Ford', 'Ranger', 2023, 'Orange', 15, 850000000.00, 'ford-ranger-2023.jpg'),
(4, 'Hyundai', 'Creta', 2024, 'White', 20, 640000000.00, 'mau-xe-Hyundai-creta.png'),
(5, 'VinFast', 'VF9', 2024, 'Black', 5, 1490000000.00, 'VinFast-VF9-9.jpg'),
(6, 'Toyota', 'Camry', 2023, 'Black', 8, 1150000000.00, 'toyota camry 2023.jpg'),
(7, 'Ford', 'Everest', 2023, 'Blue', 12, 1200000000.00, 'ford-everest-2023.jpg'),
(8, 'Honda', 'CR-V', 2024, 'White', 10, 1100000000.00, 'honda cr-v.jpg'),
(9, 'Mazda', 'CX-5', 2023, 'Red', 18, 980000000.00, 'mazda-cx-5-2.jpg'),
(10, 'Kia', 'Seltos', 2024, 'Yellow', 22, 750000000.00, '2024-Kia-Seltos-1.jpg'),
(11, 'VinFast', 'VF8', 2024, 'Blue', 5, 1050000000.00, 'VinFast-VF8-1blue.jpg');

INSERT INTO customers (customer_id, full_name, email, phone, address) VALUES
(1, 'Nguyen Van An', 'an@gmail.com', '0901234567', '789 CMT8 St, HCMC'),
(2, 'Tran Thi Binh', 'binh@gmail.com', '0909876543', '101 Nguyen Hue Blvd, HCMC'),
(3, 'Le Van Cham', 'cham@gmail.com', '0912345678', '45 Dong Khoi St, HCMC'),
(4, 'Pham Van Dong', 'dong@gmail.com', '0933333333', '12 Le Duan, Da Nang'),
(5, 'Hoang Thi Yen', 'yen@gmail.com', '0944444444', '88 Ba Trieu, Ha Noi');

INSERT INTO sales_transactions (customer_id, product_id, sales_user_id, quantity, transaction_date) VALUES
(1, 1, 1, 1, '2025-11-20 10:30:00'),
(2, 2, 2, 1, '2025-11-21 14:00:00'), 
(3, 3, 1, 1, '2025-11-22 11:15:00'), 
(4, 8, 3, 1, '2025-11-23 09:00:00'), 
(5, 9, 2, 2, '2025-11-23 15:30:00'), 
(1, 10, 1, 1, '2025-11-24 10:00:00'), 
(2, 5, 3, 1, '2025-11-25 08:45:00');