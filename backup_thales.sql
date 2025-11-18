-- 1. DESLIGAR VERIFICAÇÕES DE SEGURANÇA (Obrigatório)
SET FOREIGN_KEY_CHECKS = 0;

-- 2. APAGAR TABELAS ANTIGAS (Para evitar erro #1050)
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- 3. CONFIGURAÇÕES PADRÃO
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 4. CRIAR TABELA DE USUÁRIOS
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `provider` enum('email','google') DEFAULT 'email',
  `provider_id` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(20) DEFAULT 'user',
  `status` varchar(20) DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_provider` (`provider`),
  KEY `idx_reset_token` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. INSERIR USUÁRIOS (Admin e Teste)
INSERT INTO `users` (`id`, `email`, `password`, `name`, `avatar`, `provider`, `provider_id`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`, `is_admin`, `role`, `status`) VALUES
(2, 'admin.nti@embrapa.br', '$2y$10$NGstdlZ6QlgLlz4SHIKQweTAwuKNxhGwdir26MUgx.193J5Y0WVpu', 'Nucleo de Tecnologia da Informação - Embrapa', 'uploads/avatars/user_2_691bc257f0c49.jpg', 'email', NULL, 0, NULL, NULL, NULL, '2025-11-04 22:48:05', '2025-11-18 00:48:23', 0, 'admin', 'active'),
(3, 'teste@teste.com', '$2y$10$pq8LsOZjn0C7XwpAgx/WqeAcLk4dk1t9VQhhXGpVRRA3mX1YRQblW', 'teste', NULL, 'email', NULL, 0, NULL, NULL, NULL, '2025-11-17 20:10:40', '2025-11-18 01:36:53', 0, 'user', 'blocked');

-- 6. CRIAR TABELA DE CATEGORIAS
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'Chave estrangeira para a tabela users',
  `name` varchar(100) NOT NULL COMMENT 'Nome da categoria (ex: Alimentação)',
  `type` enum('income','expense') NOT NULL COMMENT 'Tipo da categoria (Receita ou Despesa)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_category_name_type` (`user_id`,`name`,`type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Armazena as categorias personalizadas dos utilizadores';

-- 7. INSERIR CATEGORIAS
INSERT INTO `categories` (`id`, `user_id`, `name`, `type`, `created_at`, `updated_at`) VALUES
(9, 2, 'salario', 'income', '2025-11-04 22:48:31', '2025-11-04 22:48:31'),
(10, 2, 'supermercado', 'expense', '2025-11-05 11:42:10', '2025-11-05 11:42:10'),
(11, 2, 'comparas', 'expense', '2025-11-05 11:42:22', '2025-11-05 11:42:22');

-- 8. CRIAR TABELA DE TRANSAÇÕES
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_date` (`date`),
  KEY `idx_category` (`category`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. INSERIR TRANSAÇÕES
INSERT INTO `transactions` (`id`, `user_id`, `type`, `category`, `description`, `amount`, `date`, `created_at`) VALUES
(1, 2, 'income', 'salario', 'salario', 3400.00, '2025-11-03', '2025-11-04 23:00:57'),
(2, 2, 'expense', 'supermercado', 'Compras do mes', 450.00, '2025-11-05', '2025-11-05 11:43:02');

COMMIT;

-- 10. RELIGAR SEGURANÇA
SET FOREIGN_KEY_CHECKS = 1;