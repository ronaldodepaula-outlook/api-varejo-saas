-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/10/2025 às 14:35
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_sas_multi`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `backup_logs`
--

CREATE TABLE `backup_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `executado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2014_10_12_000000_create_users_table', 2),
(3, '2014_10_12_100000_create_password_reset_tokens_table', 2),
(4, '2019_08_19_000000_create_failed_jobs_table', 2),
(5, '2025_10_02_000001_create_login_attempts_table', 3),
(6, '2025_10_02_000002_create_backup_logs_table', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Usuario', 10, 'api_token', 'ef80293fa39833134dd4799d08ee30dda1540ad5c1c995f24cdc0a6dcb789c74', '[\"*\"]', '2025-09-30 23:16:46', NULL, '2025-09-30 23:15:14', '2025-09-30 23:16:46'),
(2, 'App\\Models\\Usuario', 11, 'api_token', 'f52f6d64bf9e1ea5569e7f898bf162a5e242789422581ad23e384baac34c9a99', '[\"*\"]', NULL, NULL, '2025-10-01 14:37:20', '2025-10-01 14:37:20'),
(3, 'App\\Models\\Usuario', 18, 'api_token', '55480c4910657512f3a8ea302c3cc306260fb44aec0b187dabdc8829c827e273', '[\"*\"]', NULL, NULL, '2025-10-01 16:36:58', '2025-10-01 16:36:58'),
(4, 'App\\Models\\Usuario', 19, 'api_token', 'fad7bc67e20a875014851519887ea0f24db6cb2bd6fbdccebc1986dc34fae7f5', '[\"*\"]', NULL, NULL, '2025-10-02 14:50:15', '2025-10-02 14:50:15'),
(5, 'App\\Models\\Usuario', 23, 'api_token', '4d6f5f64e3f3184368bbedbc583cd85ec230dbab192de593b6b2572338055d18', '[\"*\"]', NULL, NULL, '2025-10-02 21:07:36', '2025-10-02 21:07:36'),
(6, 'App\\Models\\Usuario', 23, 'api_token', '7fdcc3f6110548244ed3594dd9f91a3579b54d65112f8c61267358a3a609accd', '[\"*\"]', NULL, NULL, '2025-10-02 21:22:26', '2025-10-02 21:22:26'),
(7, 'App\\Models\\Usuario', 23, 'api_token', '45f99ee938116db86766c861c3f77a4bc807303937bb02cd3f51ad54b4c57ea9', '[\"*\"]', NULL, NULL, '2025-10-02 21:22:35', '2025-10-02 21:22:35'),
(8, 'App\\Models\\Usuario', 23, 'api_token', '4fb91b5f2f6c7d4a25784ee2e1f4989775a677c3fb54a52d1c0f95360168a829', '[\"*\"]', NULL, NULL, '2025-10-02 21:23:37', '2025-10-02 21:23:37'),
(9, 'App\\Models\\Usuario', 23, 'api_token', 'ae6d2412082618977a4bbbd7d3cad7ae5e5d47a687542db472c405c24a7d935d', '[\"*\"]', NULL, NULL, '2025-10-02 21:24:34', '2025-10-02 21:24:34'),
(10, 'App\\Models\\Usuario', 23, 'api_token', '36eb68ca6cbd20a63cd37568b75b758e59632c89cf8f3fdef1f2d98b223ff5a4', '[\"*\"]', NULL, NULL, '2025-10-02 21:26:25', '2025-10-02 21:26:25'),
(11, 'App\\Models\\Usuario', 23, 'api_token', '83d9312363618e49c92946464b9b683bc1d8891725ebcbc476543f5e61508068', '[\"*\"]', NULL, NULL, '2025-10-02 21:46:54', '2025-10-02 21:46:54'),
(12, 'App\\Models\\Usuario', 23, 'api_token', 'e380b191387e2eec2a61338dcad79dc209e04ef754accf1678321bb72e2512f2', '[\"*\"]', NULL, NULL, '2025-10-02 21:47:49', '2025-10-02 21:47:49'),
(13, 'App\\Models\\Usuario', 23, 'api_token', '57ac829bb1c1c2ca3839b070586ef3acc4a52d7d6caccfccf040a40992cf3459', '[\"*\"]', NULL, NULL, '2025-10-02 21:52:37', '2025-10-02 21:52:37'),
(14, 'App\\Models\\Usuario', 23, 'api_token', 'e92a5b7f452472faada7ab5ed3a07d4e0c7c8e03641a443d4b07ce388c4f13b4', '[\"*\"]', NULL, NULL, '2025-10-02 21:53:44', '2025-10-02 21:53:44'),
(15, 'App\\Models\\Usuario', 23, 'api_token', '283f40d5485627952bc4851a9ac7993702873c665d5e08b0da34eab86de3feab', '[\"*\"]', NULL, NULL, '2025-10-02 21:53:55', '2025-10-02 21:53:55'),
(16, 'App\\Models\\Usuario', 23, 'api_token', '0213c09d1e023b8e8afc38a2d9e1151f4d04767b821d9ef68303b69629117c10', '[\"*\"]', NULL, NULL, '2025-10-04 04:20:02', '2025-10-04 04:20:02'),
(17, 'App\\Models\\Usuario', 23, 'api_token', 'e5101c6406162512b258ac619c71bccf32b8ef9b1eb3d54f0ea933c1266ba842', '[\"*\"]', NULL, NULL, '2025-10-04 04:31:47', '2025-10-04 04:31:47'),
(18, 'App\\Models\\Usuario', 23, 'api_token', '9fa4f112354c324f021374c4ae44eb9119f7b138d856573da1e50e0c7601c781', '[\"*\"]', NULL, NULL, '2025-10-04 04:39:33', '2025-10-04 04:39:33'),
(19, 'App\\Models\\Usuario', 23, 'api_token', '70cc983f8f2c97f706944c87f48d6bd45bd54d365d37eb03efbc522a22a29314', '[\"*\"]', NULL, NULL, '2025-10-04 05:12:57', '2025-10-04 05:12:57'),
(20, 'App\\Models\\Usuario', 23, 'api_token', '1f553d2557ced7c60da6ebffe5eefbdaf5b313bb749dd2156ec7b4bb7cc35b2f', '[\"*\"]', NULL, NULL, '2025-10-04 05:13:19', '2025-10-04 05:13:19'),
(21, 'App\\Models\\Usuario', 23, 'api_token', '413e156b2980c58b08a1506b90a7523a997429ad260f49e013bebf46ea46f097', '[\"*\"]', NULL, NULL, '2025-10-04 05:14:21', '2025-10-04 05:14:21'),
(22, 'App\\Models\\Usuario', 23, 'api_token', 'e091c871c3932ef2e5085d914dca9b668e81e56e2ae947271b30d439b4af294d', '[\"*\"]', NULL, NULL, '2025-10-04 05:14:58', '2025-10-04 05:14:58'),
(23, 'App\\Models\\Usuario', 23, 'api_token', 'e2e8bdbeaa13ae3bd7636c0cde557497a8e8af51fe15d0bfeec67a0486c837c3', '[\"*\"]', NULL, NULL, '2025-10-04 05:18:38', '2025-10-04 05:18:38'),
(24, 'App\\Models\\Usuario', 23, 'api_token', 'cf3b708b0f5b8343cefdb50d1872e2fd94cf0188098d1f4ba6551229d9e7e94c', '[\"*\"]', NULL, NULL, '2025-10-04 05:19:22', '2025-10-04 05:19:22'),
(25, 'App\\Models\\Usuario', 23, 'api_token', '1cd72ae752ed950878a2380bac9590346067c2c818886078cd27e8c8021d3ff4', '[\"*\"]', NULL, NULL, '2025-10-04 05:21:04', '2025-10-04 05:21:04'),
(26, 'App\\Models\\Usuario', 23, 'api_token', 'd81e978d7af528dcf7ba6f97a552e6c87d41297fdcb687a50e7d66a02838c6a3', '[\"*\"]', NULL, NULL, '2025-10-04 05:22:23', '2025-10-04 05:22:23'),
(27, 'App\\Models\\Usuario', 23, 'api_token', '8d909da2d7ea563b55cbe5144cb27bea8df58fa77572af9b2e7689a28818ac86', '[\"*\"]', NULL, NULL, '2025-10-04 05:27:03', '2025-10-04 05:27:03'),
(28, 'App\\Models\\Usuario', 23, 'api_token', 'e6f088dcce9a1d670e80bfe1f1fa11d668dead1a89dfb99e1089aa5c783bae26', '[\"*\"]', NULL, NULL, '2025-10-04 05:27:13', '2025-10-04 05:27:13'),
(29, 'App\\Models\\Usuario', 23, 'api_token', '6618a675b8e2e89b1b6475f4fc111a01a917a74a2b45f2d889f316f7ed92fdb4', '[\"*\"]', NULL, NULL, '2025-10-04 12:46:17', '2025-10-04 12:46:17'),
(30, 'App\\Models\\Usuario', 24, 'api_token', 'ccbec2f98bc7a7639a9d1f1b370402348cb0bdd9d08090879bb00774c926360e', '[\"*\"]', NULL, NULL, '2025-10-04 21:11:38', '2025-10-04 21:11:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_assinaturas`
--

CREATE TABLE `tb_assinaturas` (
  `id_assinatura` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `plano` enum('basic','pro','enterprise') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('ativa','expirada','cancelada') DEFAULT 'ativa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_empresas`
--

CREATE TABLE `tb_empresas` (
  `id_empresa` int(11) NOT NULL,
  `nome_empresa` varchar(150) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `email_empresa` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `segmento` enum('varejo','ecommerce','alimentacao','turismo_hotelaria','imobiliario','esportes_lazer','midia_entretenimento','industria','construcao','agropecuaria','energia_utilities','logistica_transporte','financeiro','contabilidade_auditoria','seguros','marketing','saude','educacao','ciencia_pesquisa','rh_recrutamento','juridico','ongs_terceiro_setor','seguranca','outros') NOT NULL,
  `status` enum('pendente','ativa','inativa') DEFAULT 'pendente',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_empresas`
--

INSERT INTO `tb_empresas` (`id_empresa`, `nome_empresa`, `cnpj`, `email_empresa`, `telefone`, `website`, `endereco`, `cep`, `cidade`, `estado`, `segmento`, `status`, `data_cadastro`, `created_at`, `updated_at`) VALUES
(10, 'Empresa Exemplo', '03720882000158', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-09-30 19:17:56', '2025-09-30 22:17:56', '2025-09-30 22:17:56'),
(15, 'GRUPO MSLZ3', '03720882000310', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 11:36:18', '2025-10-01 14:36:18', '2025-10-01 14:36:18'),
(16, 'GRUPO MSLZ2', '03720882000300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 12:11:34', '2025-10-01 15:11:34', '2025-10-01 15:11:34'),
(17, 'GRUPO MSLZ2', '03720882000311', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 12:14:50', '2025-10-01 15:14:50', '2025-10-01 15:14:50'),
(18, 'GRUPO MSLZ2', '03720882000312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 12:21:13', '2025-10-01 15:21:13', '2025-10-01 15:21:13'),
(19, 'GRUPO MSLZ2', '03720882000313', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 12:23:24', '2025-10-01 15:23:24', '2025-10-01 15:23:24'),
(20, 'GRUPO MSLZ2', '03720882000314', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 13:23:30', '2025-10-01 16:23:30', '2025-10-01 16:23:30'),
(21, 'GRUPO MSLZ2', '03720882000315', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 13:31:29', '2025-10-01 16:31:29', '2025-10-01 16:31:29'),
(22, 'GRUPO MSLZ2', '03720882000316', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'varejo', 'pendente', '2025-10-01 13:36:09', '2025-10-01 16:36:09', '2025-10-01 16:36:09'),
(23, 'GRUPO MSLZ2', '03720882000317', 'contato@grupomslz.com.br', '11999999999', 'https://grupomslz.com.br', 'Rua Exemplo, 123', '01001-000', 'São Paulo', 'SP', 'varejo', 'pendente', '2025-10-02 11:49:14', '2025-10-02 14:49:14', '2025-10-02 14:49:14'),
(24, 'GRUPO MSLZ', '03720882001201', 'admin@email.com', '85987761553', 'mslx.com.br', '250 Rua José Severino', '61762-270', 'Eusébio', 'CE', 'varejo', 'pendente', '2025-10-02 12:22:57', '2025-10-02 15:22:57', '2025-10-02 15:22:57'),
(25, 'GRUPO MSLZ', '03.720.882/0012-01', 'admin@email.com', '(85) 98776-1553', 'mslx.com.br', '250 Rua José Severino', 'CE', 'Eusébio', NULL, 'industria', 'pendente', '2025-10-02 13:05:14', '2025-10-02 16:05:14', '2025-10-02 16:05:14'),
(26, 'GRUPO MSLZ', '03.720.882/0012-09', 'admin@email.com', '(85) 98776-1553', 'mslx.com.br', '250 Rua José Severino', 'CE', 'Eusébio', NULL, 'construcao', 'pendente', '2025-10-02 13:08:46', '2025-10-02 16:08:46', '2025-10-02 16:08:46'),
(27, 'GRUPO MSLZ', '03.720.882/0012-00', 'ronaldodepaula@mslz.com.br', '(85) 98776-1553', 'mslx.com.br', '250 Rua José Severino', 'CE', 'Eusébio', NULL, 'financeiro', 'pendente', '2025-10-02 13:31:40', '2025-10-02 16:31:40', '2025-10-02 16:31:40'),
(28, 'GRUPO MSLZ', '03.720.882/0005-82', 'admis@mslz.com.br', '(85) 98776-1553', 'mslx.com.br', '250 Rua José Severino', 'CE', 'Eusébio', NULL, 'varejo', 'pendente', '2025-10-04 13:09:54', '2025-10-04 16:09:54', '2025-10-04 16:09:54'),
(29, 'GRUPO MSLZ', '03.720.882/0001-59', 'admin@email.com.br', '(85) 98776-1553', 'mslx.com.br', '250 Rua José Severino', '61762-270', 'Eusébio', 'CE', 'varejo', 'pendente', '2025-10-04 13:20:24', '2025-10-04 16:20:24', '2025-10-04 16:20:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_filiais`
--

CREATE TABLE `tb_filiais` (
  `id_filial` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `nome_filial` varchar(150) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_licencas`
--

CREATE TABLE `tb_licencas` (
  `id_licenca` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `plano` enum('trial','basic','pro','enterprise') DEFAULT 'trial',
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('ativa','expirada','cancelada') DEFAULT 'ativa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_licencas`
--

INSERT INTO `tb_licencas` (`id_licenca`, `id_empresa`, `plano`, `data_inicio`, `data_fim`, `status`, `created_at`, `updated_at`) VALUES
(2, 10, 'trial', '2025-09-30', '2025-12-30', 'ativa', '2025-09-30 22:17:56', '2025-09-30 22:17:56'),
(3, 15, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 14:36:18', '2025-10-01 14:36:18'),
(4, 16, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 15:11:34', '2025-10-01 15:11:34'),
(5, 17, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 15:14:50', '2025-10-01 15:14:50'),
(6, 18, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 15:21:14', '2025-10-01 15:21:14'),
(7, 19, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 15:23:25', '2025-10-01 15:23:25'),
(8, 20, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 16:23:31', '2025-10-01 16:23:31'),
(9, 21, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 16:31:29', '2025-10-01 16:31:29'),
(10, 22, 'trial', '2025-10-01', '2026-01-01', 'ativa', '2025-10-01 16:36:09', '2025-10-01 16:36:09'),
(11, 23, 'trial', '2025-10-02', '2026-01-02', 'ativa', '2025-10-02 14:49:15', '2025-10-02 14:49:15'),
(12, 24, 'trial', '2025-10-02', '2026-01-02', 'ativa', '2025-10-02 15:22:57', '2025-10-02 15:22:57'),
(13, 25, 'trial', '2025-10-02', '2026-01-02', 'ativa', '2025-10-02 16:05:14', '2025-10-02 16:05:14'),
(14, 26, 'trial', '2025-10-02', '2026-01-02', 'ativa', '2025-10-02 16:08:46', '2025-10-02 16:08:46'),
(15, 27, 'trial', '2025-10-02', '2026-01-02', 'ativa', '2025-10-02 16:31:41', '2025-10-02 16:31:41'),
(16, 28, 'trial', '2025-10-04', '2026-01-04', 'ativa', '2025-10-04 16:09:54', '2025-10-04 16:09:54'),
(17, 29, 'trial', '2025-10-04', '2026-01-04', 'ativa', '2025-10-04 16:20:24', '2025-10-04 16:20:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_pagamentos`
--

CREATE TABLE `tb_pagamentos` (
  `id_pagamento` int(11) NOT NULL,
  `id_assinatura` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `metodo` enum('cartao','boleto','pix') NOT NULL,
  `status` enum('pago','pendente','falha') DEFAULT 'pendente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_usuarios`
--

CREATE TABLE `tb_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('super_admin','admin_empresa','usuario') NOT NULL DEFAULT 'usuario',
  `ativo` tinyint(1) DEFAULT 0,
  `aceitou_termos` tinyint(1) DEFAULT 0,
  `newsletter` tinyint(1) DEFAULT 0,
  `email_verificado_em` timestamp NULL DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_usuarios`
--

INSERT INTO `tb_usuarios` (`id_usuario`, `id_empresa`, `nome`, `email`, `senha`, `perfil`, `ativo`, `aceitou_termos`, `newsletter`, `email_verificado_em`, `data_criacao`, `created_at`, `updated_at`) VALUES
(10, 10, 'Ronaldo de Paula', 'ronaldodepaulasurf@gmail.com', '$2y$12$TaUmVpYZ1V6gc/hbPwJtlO0IPTMfCAUH73TH9Qy2NdFiVw0GUxUb2', 'admin_empresa', 1, 0, 0, '2025-09-30 22:19:16', '2025-09-30 19:17:56', '2025-09-30 22:17:56', '2025-09-30 22:19:16'),
(11, 15, 'Ronaldo de Paula', 'ronaldodepaulasurf1@yahoo.com.br', '$2y$12$gxY9rXX4m5Is/iVqYpemIuxW5/qDNWglkhsISO1VihEMLgMjOE9U2', 'admin_empresa', 1, 0, 0, '2025-10-01 14:37:12', '2025-10-01 11:36:18', '2025-10-01 14:36:18', '2025-10-01 14:37:12'),
(12, 16, 'Ronaldo de Paula', 'ronaldodepaulasurf2@yahoo.com.br', '$2y$12$l/dbozHWO7cFhgGzj5FUfemLMK2LZ4/CyywQ8n/WEYaYTXDgJ4Z3S', 'admin_empresa', 1, 0, 0, '2025-10-01 15:12:10', '2025-10-01 12:11:34', '2025-10-01 15:11:34', '2025-10-01 15:12:10'),
(13, 17, 'Ronaldo de Paula', 'ronaldodepaulasurf3@yahoo.com.br', '$2y$12$9Ih7SMhK8HJFRGkslsQ/LuLr.uyBTKqJZV1s.6v53GCnMq9R3u8vu', 'admin_empresa', 1, 0, 0, '2025-10-01 15:15:23', '2025-10-01 12:14:50', '2025-10-01 15:14:50', '2025-10-01 15:15:23'),
(14, 18, 'Ronaldo de Paula', 'ronaldodepaulasurf4@yahoo.com.br', '$2y$12$im82WziR5NglvTDyuFRQ5uNnjua2PcA.Pqrf0WFNu83wuMY6BW3x.', 'admin_empresa', 1, 0, 0, '2025-10-01 15:21:36', '2025-10-01 12:21:14', '2025-10-01 15:21:14', '2025-10-01 15:21:36'),
(15, 19, 'Ronaldo de Paula', 'ronaldodepaulasurf5@yahoo.com.br', '$2y$12$/kihCY8aVd5qJvb4oMGjSO1/h0HEiKML3qeZkhQRDq1wNDtdanscO', 'admin_empresa', 1, 0, 0, '2025-10-01 16:20:48', '2025-10-01 12:23:25', '2025-10-01 15:23:25', '2025-10-01 16:20:48'),
(16, 20, 'Ronaldo de Paula', 'ronaldodepaulasurf6@yahoo.com.br', '$2y$12$ARMfTQWB1JtI1AUawDqxTOh8pkce9OMlTDTer8nt65/j/ApdaWkrm', 'admin_empresa', 1, 0, 0, '2025-10-01 16:24:31', '2025-10-01 13:23:31', '2025-10-01 16:23:31', '2025-10-01 16:24:31'),
(17, 21, 'Ronaldo de Paula', 'ronaldodepaulasur7f@yahoo.com.br', '$2y$12$dUh56aw8nqTeaAtJysRoUung1WoWZsMb6zIN2PA6SREPFdTzjL5o2', 'admin_empresa', 1, 0, 0, '2025-10-01 16:32:13', '2025-10-01 13:31:29', '2025-10-01 16:31:29', '2025-10-01 16:32:13'),
(18, 22, 'Ronaldo de Paula', 'ronaldodepaulasurf8@yahoo.com.br', '$2y$12$MdhWijeDM8G5Jcz2dc3eN.sVFnWq62ofoIqvyczRToPyezE/mVkjG', 'admin_empresa', 1, 0, 0, '2025-10-01 16:36:50', '2025-10-01 13:36:09', '2025-10-01 16:36:09', '2025-10-01 16:36:50'),
(19, 23, 'Ronaldo de Paula', 'ronaldodepaulasur9f@yahoo.com.br', '$2y$12$gD1yp/di6dCysCHQIRPg9OKigxxTSNY/2bCXwBArMbAvENwhyBRhG', 'admin_empresa', 1, 1, 1, '2025-10-02 14:49:50', '2025-10-02 11:49:15', '2025-10-02 14:49:15', '2025-10-02 14:49:50'),
(20, 24, 'Antonio Ronaldo de Paula Nascimento', 'ronaldodepaulasurf10@yahoo.com.br', '$2y$12$UJDHtIYYkRiA64Sd0ytGmuwAIf.vlJM28jiRpB7XoBfWexcZisY2W', 'admin_empresa', 0, 1, 1, NULL, '2025-10-02 12:22:57', '2025-10-02 15:22:57', '2025-10-02 15:22:57'),
(21, 25, 'Antonio Ronaldo de Paula Nascimento', 'ronaldodepaulasurf11@yahoo.com.br', '$2y$12$9jDdu2eAXDmMy82RlovDSuUPQhiFFZD1j23AO1PIA5NbYYTF/uYhy', 'admin_empresa', 0, 1, 1, NULL, '2025-10-02 13:05:14', '2025-10-02 16:05:14', '2025-10-02 16:05:14'),
(22, 26, 'Antonio Ronaldo de Paula Nascimento', 'ronaldodepaulasurf12@yahoo.com.br', '$2y$12$pjurrrgwfSsunKUa.qgpxOYX5T7sQCujJVpKVNKBEkMQQjJcglpBS', 'admin_empresa', 0, 1, 1, NULL, '2025-10-02 13:08:46', '2025-10-02 16:08:46', '2025-10-02 16:08:46'),
(23, 27, 'Antonio Ronaldo de Paula Nascimento', 'ronaldodepaulasurf@yahoo.com.br', '$2y$12$S/MD3nDgua869LW9U2o1ieeRMw/dfojLbA8jqnmVehB7rTYGH5iF2', 'admin_empresa', 1, 1, 1, '2025-10-02 21:07:30', '2025-10-02 13:31:41', '2025-10-02 16:31:41', '2025-10-02 21:07:30'),
(24, 28, 'Antonio Ronaldo de Paula Nascimento', 'ronaldo_de_paula@hotmail.com', '$2y$12$49dKdYt.DcWMpwAdH9Zj3.NXUpPjCqyq8HLjfc3HDAxipQ9QQjQkW', 'admin_empresa', 1, 1, 1, '2025-10-04 16:11:08', '2025-10-04 13:09:54', '2025-10-04 16:09:54', '2025-10-04 16:11:08'),
(25, 29, 'Antonio Ronaldo de Paula Nascimento', 'ronaldodepaula@mslz.com.br', '$2y$12$v0X21FEyoXCmcEPmr6K4zOtCD1tSQwEZzx1kMp4DdmvzMklP11UTa', 'admin_empresa', 0, 1, 1, NULL, '2025-10-04 13:20:24', '2025-10-04 16:20:24', '2025-10-04 16:20:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_verificacoes_email`
--

CREATE TABLE `tb_verificacoes_email` (
  `id_verificacao` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_verificacoes_email`
--

INSERT INTO `tb_verificacoes_email` (`id_verificacao`, `id_usuario`, `token`, `criado_em`, `created_at`, `updated_at`) VALUES
(16, 20, 'he97hx7KlfFgJTrfQjX1BKkYomqcYezbNH4WbF0iDDuYA2Ms96QDk5TMiSfk', '2025-10-02 12:22:57', '2025-10-02 15:22:57', '2025-10-02 15:22:57'),
(17, 21, 'mKYDI9YU4MBs4TEjjfUeUxqBdunBNRSnUDSSJD7km8mOGgHzcXMM3Ub8inkE', '2025-10-02 13:05:14', '2025-10-02 16:05:14', '2025-10-02 16:05:14'),
(18, 22, 'QyucG2EMyTuhBZRzJmNLSgHEdrfkk5PPSdSVENPpcxtdExh4Njib5TJFgerj', '2025-10-02 13:08:46', '2025-10-02 16:08:46', '2025-10-02 16:08:46'),
(21, 25, 'c9dJ24OAISdM1QjUSVCz87Ag1UCtt3jksChB4rvKPDzyH5dekbGmB9RF9gux', '2025-10-04 13:20:24', '2025-10-04 16:20:24', '2025-10-04 16:20:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Índices de tabela `tb_assinaturas`
--
ALTER TABLE `tb_assinaturas`
  ADD PRIMARY KEY (`id_assinatura`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Índices de tabela `tb_empresas`
--
ALTER TABLE `tb_empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `tb_filiais`
--
ALTER TABLE `tb_filiais`
  ADD PRIMARY KEY (`id_filial`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Índices de tabela `tb_licencas`
--
ALTER TABLE `tb_licencas`
  ADD PRIMARY KEY (`id_licenca`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Índices de tabela `tb_pagamentos`
--
ALTER TABLE `tb_pagamentos`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_assinatura` (`id_assinatura`);

--
-- Índices de tabela `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Índices de tabela `tb_verificacoes_email`
--
ALTER TABLE `tb_verificacoes_email`
  ADD PRIMARY KEY (`id_verificacao`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `tb_assinaturas`
--
ALTER TABLE `tb_assinaturas`
  MODIFY `id_assinatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_empresas`
--
ALTER TABLE `tb_empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `tb_filiais`
--
ALTER TABLE `tb_filiais`
  MODIFY `id_filial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_licencas`
--
ALTER TABLE `tb_licencas`
  MODIFY `id_licenca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `tb_pagamentos`
--
ALTER TABLE `tb_pagamentos`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `tb_verificacoes_email`
--
ALTER TABLE `tb_verificacoes_email`
  MODIFY `id_verificacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tb_assinaturas`
--
ALTER TABLE `tb_assinaturas`
  ADD CONSTRAINT `tb_assinaturas_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `tb_empresas` (`id_empresa`);

--
-- Restrições para tabelas `tb_filiais`
--
ALTER TABLE `tb_filiais`
  ADD CONSTRAINT `tb_filiais_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `tb_empresas` (`id_empresa`);

--
-- Restrições para tabelas `tb_licencas`
--
ALTER TABLE `tb_licencas`
  ADD CONSTRAINT `tb_licencas_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `tb_empresas` (`id_empresa`);

--
-- Restrições para tabelas `tb_pagamentos`
--
ALTER TABLE `tb_pagamentos`
  ADD CONSTRAINT `tb_pagamentos_ibfk_1` FOREIGN KEY (`id_assinatura`) REFERENCES `tb_assinaturas` (`id_assinatura`);

--
-- Restrições para tabelas `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD CONSTRAINT `tb_usuarios_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `tb_empresas` (`id_empresa`);

--
-- Restrições para tabelas `tb_verificacoes_email`
--
ALTER TABLE `tb_verificacoes_email`
  ADD CONSTRAINT `tb_verificacoes_email_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
