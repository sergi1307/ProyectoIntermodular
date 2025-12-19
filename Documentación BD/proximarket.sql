DROP DATABASE ProxiMarkt;
CREATE DATABASE IF NOT EXISTS ProxiMarkt;

USE ProxiMarkt;

CREATE TABLE USERS (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone CHAR(12),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE CATEGORIES (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE DELIVERY_POINT (
    id_delivery_point INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    name VARCHAR(255) NOT NULL,
    direction VARCHAR(255),
    latitude DOUBLE,
    length DOUBLE,
    FOREIGN KEY (id_user) REFERENCES USERS(id_user)
);

CREATE TABLE PRODUCTS (
    id_product INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_categorie INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DOUBLE NOT NULL,
    stock DOUBLE NOT NULL,
    image VARCHAR(255),
    type_stock ENUM('Kg', 'unidad') NOT NULL,
    state ENUM('Agotado', 'Reservado', 'Disponible') DEFAULT 'Disponible',
    publication_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES USERS(id_user),
    FOREIGN KEY (id_categorie) REFERENCES CATEGORIES(id_categorie)
);

CREATE TABLE MESSAGES (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_product INT,
    id_transmitter INT,
    id_receiver INT,
    content TEXT,
    shipping_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_product) REFERENCES PRODUCTS(id_product),
    FOREIGN KEY (id_transmitter) REFERENCES USERS(id_user),
    FOREIGN KEY (id_receiver) REFERENCES USERS(id_user)
);

CREATE TABLE REVIEWS (
    id_review INT AUTO_INCREMENT PRIMARY KEY,
    id_sale INT,
    calification ENUM('1', '2', '3', '4', '5'),
    comment TEXT,
    review_date DATE
);

CREATE TABLE SALES (
    id_sale INT AUTO_INCREMENT PRIMARY KEY,
    id_product INT UNIQUE,
    id_buyer INT,
    id_seller INT,
    id_delivery_point INT,
    id_review INT,
    sale_date DATE,
    total DOUBLE,
    collection_date DATE,
    state ENUM('Pendiente', 'En Curso', 'Terminado') DEFAULT 'Pendiente',
    FOREIGN KEY (id_product) REFERENCES PRODUCTS(id_product),
    FOREIGN KEY (id_buyer) REFERENCES USERS(id_user),
    FOREIGN KEY (id_seller) REFERENCES USERS(id_user),
    FOREIGN KEY (id_review) REFERENCES REVIEWS(id_review),
    FOREIGN KEY (id_delivery_point) REFERENCES DELIVERY_POINT(id_delivery_point)
);
-- Como una la table review depende de sale y sale necesita review necesitamos hacer un alter table
-- Para poder hacer la relación entre saley review
ALTER TABLE REVIEWS ADD FOREIGN KEY (id_sale) REFERENCES SALES(id_sale);

