CREATE DATABASE IF NOT EXISTS `dindin`;
USE `dindin`;

CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('income', 'expense') NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255),
  `amount` DECIMAL(10, 2) NOT NULL,
  `date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `transactions` (`type`, `category`, `description`, `amount`, `date`)
VALUES ('expense', 'moradia', 'Aluguel', 1.20, '2025-09-16');

