 Tabela de Usuários (principal)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NULL, -- Pode ser NULL para usuários que usam apenas Google OAuth
  `name` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(500) NULL, -- URL da imagem do Google ou upload
  `provider` ENUM('email', 'google') DEFAULT 'email', -- Tipo de autenticação
  `provider_id` VARCHAR(255) NULL, -- ID único do Google OAuth
  `email_verified` BOOLEAN DEFAULT FALSE,
  `verification_token` VARCHAR(100) NULL,
  `reset_token` VARCHAR(100) NULL,
  `reset_token_expires` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX `idx_email` (`email`),
  INDEX `idx_provider` (`provider`),
  INDEX `idx_reset_token` (`reset_token`)
);

-- Tabela de Transações (atualizada com user_id)
CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` ENUM('income', 'expense') NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255),
  `amount` DECIMAL(10, 2) NOT NULL,
  `date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_date` (`date`),
  INDEX `idx_category` (`category`)
);

-- Inserir usuário de exemplo (senha: "senha123" hash)
INSERT INTO `users` (`email`, `password`, `name`, `email_verified`)
VALUES ('usuario@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuário Exemplo', TRUE);

-- Atualizar transações existentes para associar ao usuário
INSERT INTO `transactions` (`user_id`, `type`, `category`, `description`, `amount`, `date`)
VALUES (1, 'expense', 'moradia', 'Aluguel', 1.20, '2025-09-16');