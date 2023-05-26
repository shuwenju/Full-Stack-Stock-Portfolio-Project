CREATE DATABASE IF NOT EXISTS portfolio_manager;
USE portfolio_manager;

CREATE TABLE IF NOT EXISTS user_info (
    user_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    first_name VARCHAR(15) NOT NULL,
    last_name VARCHAR(25) NOT NULL
);


CREATE TABLE IF NOT EXISTS stock_info (
    stock_info_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    stock_name VARCHAR(255) NOT NULL UNIQUE,
    stock_ticker VARCHAR(5) NOT NULL UNIQUE,
    stock_owner VARCHAR(255) NOT NULL,
    stock_description VARCHAR(255) NOT NULL,
    country_of_company VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS user_financial_info (
    user_stock_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user_info(user_id),
    stock_ticker VARCHAR(5) NOT NULL,
    FOREIGN KEY (stock_ticker) REFERENCES stock_info(stock_ticker),
    stock_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (stock_name) REFERENCES stock_info(stock_name),
    stock_initial_price FLOAT NOT NULL,
    buying_date DATE NOT NULL,
    quantity FLOAT NOT NULL
);



INSERT INTO stock_info (stock_name, stock_ticker, stock_owner, stock_description, country_of_company) VALUES
('Walt Disney Company', 'DIS', 'Disney', 'American multinational media and entertainment conglomerate that operates a vast portfolio of brands and businesses.', 'USA'),
('AT&T Inc.', 'T', 'AT&T', 'American multinational telecommunications conglomerate that provides wireless services, internet services, and television services.', 'USA'),
('Cisco Systems Inc.', 'CSCO', 'Cisco', 'Technology company that designs, manufactures, and sells networking hardware, telecommunications equipment, and other high-technology services and products.', 'USA'),
('Intel Corporation', 'INTC', 'Intel', 'Technology company that designs and manufactures microprocessors and other semiconductor components.', 'USA'),
('The Home Depot Inc.', 'HD', 'Home Depot', 'American home improvement supplies retailing company that sells tools, construction products, and services.', 'USA'),
('3M Company', 'MMM', '3M', 'Multinational conglomerate that produces a variety of products including adhesives, abrasives, passive fire protection, and electronic materials.', 'USA'),
('Oracle Corporation', 'ORCL', 'Oracle', 'Technology company that develops and sells computer hardware, middleware, and software, as well as database management systems.', 'USA'),
('Verizon Communications Inc.', 'VZ', 'Verizon', 'American multinational telecommunications conglomerate that provides wireless services, internet services, and television services.', 'USA'),
('The Coca-Cola Company', 'KO', 'Coca-Cola', 'American multinational beverage corporation that manufactures, retails, and markets nonalcoholic beverage concentrates and syrups.', 'USA'),
('Apple Inc.', 'AAPL', 'Apple', 'Multinational technology company that designs, manufactures, and markets consumer electronics, computer software, and online services.', 'USA'),
('Microsoft Corporation', 'MSFT', 'Microsoft', 'Technology company that develops, licenses, and sells computer software, consumer electronics, and personal computers.', 'USA'),
('Amazon.com Inc.', 'AMZN', 'Amazon', 'American multinational technology company that focuses on e-commerce, cloud computing, digital streaming, and artificial intelligence.', 'USA'),
('Alphabet Inc.', 'GOOGL', 'Google', 'Multinational conglomerate that specializes in Internet-related services and products, including search engines, online advertising technologies, cloud computing, software, and hardware.', 'USA'),
('Ford Motor Company', 'F', 'Ford', 'American multinational automobile manufacturer that produces and sells cars, trucks, and SUVs.', 'USA'),
('The Procter & Gamble Company', 'PG', 'Procter & Gamble', 'American multinational consumer goods corporation that specializes in a wide range of personal health, consumer health, and hygiene products.', 'USA');

