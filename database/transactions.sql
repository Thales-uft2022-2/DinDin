-- Script para criação da tabela transaction
-- Salvar este arquivo como transaction.sql e rodar no phpMyAdmin ou MySQL CLI

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` ENUM('Receita', 'Despesa') NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `transaction_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `fk_user_transaction` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;