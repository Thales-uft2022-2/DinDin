CREATE DATABASE IF NOT EXISTS `dindin`;
USE `dindin`;

-- Tabela de Usuários (principal)
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
  `category` VARCHAR(100) NOT NULL, -- OBS: Esta coluna será ALTERADA/REMOVIDA quando integrarmos US-Cat-02/US-Tx-06
  `description` VARCHAR(255),
  `amount` DECIMAL(10, 2) NOT NULL,
  `date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_date` (`date`),
  INDEX `idx_category` (`category`)
);

-- Tabela de Categorias (NOVA - Sprint 4 / US-Cat-01)
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL COMMENT 'Chave estrangeira para a tabela users',
  `name` VARCHAR(100) NOT NULL COMMENT 'Nome da categoria (ex: Alimentação)',
  `type` ENUM('income', 'expense') NOT NULL COMMENT 'Tipo da categoria (Receita ou Despesa)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  -- Chave estrangeira ligando ao utilizador
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,

  -- Índice para garantir que um utilizador não repita nome e tipo de categoria
  UNIQUE KEY `uq_user_category_name_type` (`user_id`, `name`, `type`),

  -- Índices adicionais para performance
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_type` (`type`)
) COMMENT 'Armazena as categorias personalizadas dos utilizadores';


-- --- DADOS DE EXEMPLO ---

-- Inserir usuário de exemplo (senha: "senha123" hash)
-- Certifique-se de que o ID deste usuário seja 1 para os exemplos abaixo funcionarem
INSERT INTO `users` (`id`, `email`, `password`, `name`, `email_verified`)
VALUES (1, 'usuario@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuário Exemplo', TRUE)
ON DUPLICATE KEY UPDATE email = email; -- Evita erro se o usuário 1 já existir

-- Atualizar transações existentes para associar ao usuário 1 (se existirem)
-- Atenção: O INSERT abaixo SÓ funciona se a tabela transactions estiver VAZIA.
-- Se já tiver dados, use UPDATE ou adicione novas transações.
-- Exemplo de INSERT (apenas para base limpa):
-- INSERT INTO `transactions` (`user_id`, `type`, `category`, `description`, `amount`, `date`)
-- VALUES (1, 'expense', 'Moradia', 'Aluguel', 1200.00, '2025-10-05'),
--        (1, 'income', 'Salário', 'Pagamento Outubro', 5000.00, '2025-10-01');

-- (Você pode adicionar INSERTs para categorias padrão se desejar)
-- INSERT INTO `categories` (`user_id`, `name`, `type`) VALUES (1, 'Salário', 'income'), (1, 'Moradia', 'expense'), (1, 'Alimentação', 'expense');