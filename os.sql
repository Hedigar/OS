-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 29/12/2025 às 17:25
-- Versão do servidor: 8.0.43
-- Versão do PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `os`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `nome_completo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pessoa` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_principal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_secundario` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_logradouro` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_bairro` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_cidade` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome_completo`, `tipo_pessoa`, `documento`, `email`, `telefone_principal`, `telefone_secundario`, `endereco_logradouro`, `endereco_numero`, `endereco_bairro`, `endereco_cidade`, `data_nascimento`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Joao da Silva', 'fisica', '12345678901', 'joao.silva@email.com', '51999990001', '', 'Rua das Flores', '100', 'Centro', 'Porto Alegre', NULL, '', 0, '2025-12-25 23:18:44', NULL),
(2, 'Maria Oliveira', 'fisica', '23456789012', 'maria.oliveira@email.com', '51999990002', NULL, 'Av Brasil', '250', 'Cidade Baixa', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(3, 'Carlos Pereira', 'fisica', '34567890123', 'carlos.p@email.com', '51999990003', NULL, 'Rua Sao Joao', '45', 'Floresta', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(4, 'Ana Souza', 'fisica', '45678901234', 'ana.souza@email.com', '51999990004', NULL, 'Rua Independencia', '789', 'Independencia', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(5, 'Ricardo Lima', 'fisica', '56789012345', 'ricardo.l@email.com', '51999990005', NULL, 'Av Ipiranga', '3200', 'Jardim Botanico', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(6, 'Fernanda Costa', 'fisica', '67890123456', 'fernanda.c@email.com', '51999990006', NULL, 'Rua Bento Goncalves', '150', 'Partenon', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(7, 'Paulo Martins', 'fisica', '78901234567', 'paulo.m@email.com', '51999990007', NULL, 'Rua Voluntarios', '980', 'Centro Historico', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(8, 'Juliana Rocha', 'fisica', '89012345678', 'juliana.r@email.com', '51999990008', NULL, 'Av Farrapos', '400', 'Navegantes', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(9, 'Eduardo Nunes', 'fisica', '90123456789', 'eduardo.n@email.com', '51999990009', NULL, 'Rua Cristovao', '210', 'Higienopolis', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(10, 'Patricia Almeida', 'fisica', '01234567890', 'patricia.a@email.com', '51999990010', NULL, 'Rua Silva Jardim', '55', 'Auxiliadora', 'Porto Alegre', NULL, NULL, 1, '2025-12-25 23:18:44', NULL),
(21, 'Maria', 'fisica', '', '', '', '', '', '', '', '', NULL, '', 1, '2025-12-27 13:27:47', NULL),
(22, 'Dienefer', 'fisica', '03486719033', 'dienefer.fsilva@gmail.com', '51981695579', '', '', '', '', '', '1997-12-19', 'É um t rex', 1, '2025-12-27 14:35:28', NULL),
(24, 'Hedigar Eli Gonçalves', 'fisica', '1524654654', 'hedigar@gmail.com', '51981695579', '', 'Rua Alexandre Renda 160/AP', '102', 'Centro', 'Osório', NULL, '', 0, '2025-12-28 19:31:58', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_gerais`
--

CREATE TABLE `configuracoes_gerais` (
  `id` int NOT NULL,
  `chave` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes_gerais`
--

INSERT INTO `configuracoes_gerais` (`id`, `chave`, `valor`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 'porcentagem_venda', '30', 'Percentual aplicado sobre o custo para calcular o valor de venda', '2025-12-29 13:21:36', '2025-12-29 13:40:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipamentos`
--

CREATE TABLE `equipamentos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `ordem_servico_id` int DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `senha` varchar(100) DEFAULT NULL,
  `acessorios` text,
  `possui_fonte` tinyint(1) DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `equipamentos`
--

INSERT INTO `equipamentos` (`id`, `cliente_id`, `ordem_servico_id`, `tipo`, `marca`, `modelo`, `serial`, `senha`, `acessorios`, `possui_fonte`, `ativo`) VALUES
(1, 2, NULL, 'Smartphone', 'Dell', 'inspirion 15', '234234', 'as4d56as4d56', 'qweawe', 1, 1),
(2, 2, NULL, 'Notebook', 'dweqawe', 'qeweqwe', 'qweqwe', 'qweqw', 'asdasdasd', 1, 1),
(3, 1, NULL, 'Notebook', 'dweqawe', 'inspirion 15', '234234', 'qweqw', '3423423', 1, 1),
(4, 22, NULL, 'Notebook', 'Acer', 'Vermelho', '123', 'nao sabe', 'Mousepad do ursinho pooh', 0, 1),
(5, 24, NULL, 'Notebook', 'Dell', 'inspirion 15', '234234', 'as4d56as4d56', 'asdasdas', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_ordem_servico`
--

CREATE TABLE `itens_ordem_servico` (
  `id` int NOT NULL,
  `ordem_servico_id` int NOT NULL,
  `tipo_item` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` decimal(10,2) DEFAULT '1.00',
  `custo` decimal(10,2) DEFAULT '0.00',
  `valor_unitario` decimal(10,2) DEFAULT '0.00',
  `valor_total` decimal(10,2) DEFAULT '0.00',
  `status` varchar(50) DEFAULT 'pendente',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `acao` varchar(150) DEFAULT NULL,
  `referencia` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `dados_anteriores` json DEFAULT NULL,
  `dados_novos` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `referencia`, `ip_address`, `user_agent`, `dados_anteriores`, `dados_novos`, `created_at`) VALUES
(1, 2, 'Realizou logout do sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:31:23'),
(2, 2, 'Realizou login no sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:31:28'),
(3, 2, 'Criou novo cliente', 'Cliente #24 - Hedigar Eli Gonçalves', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:31:58'),
(4, 2, 'Criou nova Ordem de Serviço', 'OS #10', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:32:47'),
(5, 2, 'Excluiu Ordem de Serviço', 'OS #10', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:33:06'),
(6, 2, 'Realizou logout do sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:34:34'),
(7, 1, 'Realizou login no sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:34:41'),
(8, 1, 'Realizou logout do sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:48:59'),
(9, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:49:04'),
(10, 2, 'Realizou login no sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:49:08'),
(11, 2, 'Realizou logout do sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:49:37'),
(12, 3, 'Realizou login no sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:49:43'),
(13, 3, 'Realizou logout do sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:50:33'),
(14, 4, 'Realizou login no sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:50:39'),
(15, 4, 'Realizou logout do sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:51:18'),
(16, 2, 'Realizou login no sistema', NULL, '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 19:59:57'),
(17, 2, 'Excluiu Ordem de Serviço', 'OS #9', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 20:00:08'),
(18, 2, 'Excluiu cliente', 'Cliente #1 - Joao da Silva', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 20:02:54'),
(19, 2, 'Excluiu cliente', 'Cliente #24 - Hedigar Eli Gonçalves', '192.168.0.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-28 20:03:26'),
(20, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 13:21:45'),
(21, NULL, 'Cadastrou novo produto/serviço', 'Item #1 - SSD 240', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\": \"SSD 240\", \"tipo\": \"produto\", \"custo\": \"159.00\", \"mao_de_obra\": 0, \"valor_venda\": 0}', '2025-12-29 13:26:19'),
(22, NULL, 'Excluiu produto/serviço', 'Item #1 - SSD 240', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\": 1, \"nome\": \"SSD 240\", \"tipo\": \"produto\", \"ativo\": 1, \"custo\": \"159.00\", \"created_at\": \"2025-12-29 13:26:19\", \"updated_at\": null, \"mao_de_obra\": \"0.00\", \"valor_venda\": \"0.00\"}', '{\"ativo\": 0}', '2025-12-29 13:26:33'),
(23, NULL, 'Cadastrou novo produto/serviço', 'Item #2 - SSD 240', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\": \"SSD 240\", \"tipo\": \"produto\", \"custo\": 0, \"mao_de_obra\": 0, \"valor_venda\": 0}', '2025-12-29 13:26:45'),
(24, NULL, 'Cadastrou novo produto/serviço', 'Item #3 - FORMATAçãO', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\": \"FORMATAçãO\", \"tipo\": \"servico\", \"custo\": 0, \"mao_de_obra\": 0, \"valor_venda\": \"220.00\"}', '2025-12-29 13:27:10'),
(25, NULL, 'Alterou configuração de porcentagem de venda', 'Nova porcentagem: 60%', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 13:31:08'),
(26, NULL, 'Cadastrou novo produto/serviço', 'Item #4 - FONTE 300W', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\": \"FONTE 300W\", \"tipo\": \"produto\", \"custo\": \"250.00\", \"mao_de_obra\": \"40.00\", \"valor_venda\": \"400.00\"}', '2025-12-29 13:32:21'),
(27, NULL, 'Excluiu produto/serviço', 'Item #4 - FONTE 300W', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\": 4, \"nome\": \"FONTE 300W\", \"tipo\": \"produto\", \"ativo\": 1, \"custo\": \"250.00\", \"created_at\": \"2025-12-29 13:32:21\", \"updated_at\": null, \"mao_de_obra\": \"40.00\", \"valor_venda\": \"400.00\"}', '{\"ativo\": 0}', '2025-12-29 13:32:36'),
(28, NULL, 'Alterou configuração de porcentagem de venda', 'Nova porcentagem: 60%', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 13:39:39'),
(29, NULL, 'Realizou atualização em massa de preços', 'Margem aplicada: 60%', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 13:39:49'),
(30, NULL, 'Atualizou produto/serviço', 'Item #2 - SSD 240', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\": 2, \"nome\": \"SSD 240\", \"tipo\": \"produto\", \"ativo\": 1, \"custo\": \"0.00\", \"created_at\": \"2025-12-29 13:26:45\", \"updated_at\": null, \"mao_de_obra\": \"0.00\", \"valor_venda\": \"0.00\"}', '{\"nome\": \"SSD 240\", \"tipo\": \"produto\", \"custo\": \"132\", \"mao_de_obra\": \"0.00\", \"valor_venda\": \"211.20\"}', '2025-12-29 13:40:02'),
(31, NULL, 'Alterou configuração de porcentagem de venda', 'Nova porcentagem: 30%', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 13:40:08'),
(32, NULL, 'Realizou atualização em massa de preços', 'Margem aplicada: 30%', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 13:40:12'),
(33, NULL, 'Tentativa de login falhou', 'E-mail: asdasda@kfsjdk.com', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 16:43:45'),
(34, NULL, 'Tentativa de login falhou', 'E-mail: asdasda@kfsjdk.com', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 16:51:13'),
(35, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 16:57:44'),
(36, NULL, 'Tentativa de login falhou', 'E-mail: asdasda@kfsjdk.com', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 17:03:20'),
(37, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2025-12-29 17:03:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico`
--

CREATE TABLE `ordens_servico` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `equipamento_id` int DEFAULT NULL,
  `defeito_relatado` text NOT NULL,
  `laudo_tecnico` text,
  `valor_total_produtos` decimal(10,2) DEFAULT '0.00',
  `valor_total_servicos` decimal(10,2) DEFAULT '0.00',
  `valor_total_os` decimal(10,2) DEFAULT '0.00',
  `status_atual_id` int NOT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `ordens_servico`
--

INSERT INTO `ordens_servico` (`id`, `cliente_id`, `equipamento_id`, `defeito_relatado`, `laudo_tecnico`, `valor_total_produtos`, `valor_total_servicos`, `valor_total_os`, `status_atual_id`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'sdasdasd', NULL, 0.00, 0.00, 0.00, 1, 1, '2025-12-27 13:47:02', NULL),
(2, 2, 1, 'Kkkk', '', 0.00, 0.00, 0.00, 1, 1, '2025-12-27 13:50:31', NULL),
(3, 1, 3, '234234', '', 0.00, 0.00, 0.00, 1, 1, '2025-12-27 14:19:17', NULL),
(4, 22, 4, 'Não vem as compras de graça da shoppe', NULL, 0.00, 0.00, 0.00, 1, 1, '2025-12-27 14:36:43', NULL),
(5, 22, 4, 'Lento', NULL, 0.00, 0.00, 0.00, 1, 1, '2025-12-27 14:37:22', NULL),
(6, 2, 1, 'rgdfgd', NULL, 0.00, 0.00, 0.00, 1, 1, '2025-12-27 14:39:53', NULL),
(7, 2, 1, 'rgdfgd', NULL, 0.00, 0.00, 0.00, 1, 1, '2025-12-27 14:40:06', NULL),
(9, 1, 3, 'flksdjfkslçkdkflçskdf', NULL, 0.00, 0.00, 0.00, 1, 0, '2025-12-27 17:47:51', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico_status_historico`
--

CREATE TABLE `ordens_servico_status_historico` (
  `id` int NOT NULL,
  `ordem_servico_id` int NOT NULL,
  `status_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos_servicos`
--

CREATE TABLE `produtos_servicos` (
  `id` int NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('produto','servico') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `custo` decimal(10,2) DEFAULT '0.00',
  `valor_venda` decimal(10,2) DEFAULT '0.00',
  `mao_de_obra` decimal(10,2) DEFAULT '0.00',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos_servicos`
--

INSERT INTO `produtos_servicos` (`id`, `nome`, `tipo`, `custo`, `valor_venda`, `mao_de_obra`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'SSD 240', 'produto', 159.00, 0.00, 0.00, 0, '2025-12-29 13:26:19', '2025-12-29 13:26:33'),
(2, 'SSD 240', 'produto', 132.00, 171.60, 0.00, 1, '2025-12-29 13:26:45', '2025-12-29 13:40:12'),
(3, 'FORMATAçãO', 'servico', 0.00, 220.00, 0.00, 1, '2025-12-29 13:27:10', NULL),
(4, 'FONTE 300W', 'produto', 250.00, 400.00, 40.00, 0, '2025-12-29 13:32:21', '2025-12-29 13:32:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_os`
--

CREATE TABLE `status_os` (
  `id` int NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cor` varchar(7) DEFAULT '#000000',
  `ordem` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `status_os`
--

INSERT INTO `status_os` (`id`, `nome`, `cor`, `ordem`) VALUES
(1, 'aberta', '#3498db', 1),
(2, 'em_orcamento', '#f1c40f', 2),
(3, 'aguardando_aprovacao', '#e67e22', 3),
(4, 'em_execucao', '#9b59b6', 4),
(5, 'finalizada', '#2ecc71', 5),
(6, 'cancelada', '#e74c3c', 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` varchar(50) NOT NULL,
  `trocar_senha` tinyint(1) DEFAULT '1',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `trocar_senha`, `ativo`, `created_at`) VALUES
(1, 'Administrador', 'admin@admin.com', '$2a$12$pCam4vDGSIXWh8qqrDWpp.YXE5/Z/uA5nE0QNZigIjI/JNNxKuor2', 'admin', 0, 1, '2025-12-25 23:18:36'),
(2, 'hedigar', 'hedigar9@gmail.com', '$2y$10$5KvEhQ./Mu3InJQExe8o4eBRWI1vTN9/5E.si5g9.5szof8cugKBG', 'admin', 0, 1, '2025-12-28 19:01:51'),
(3, 'hedigar', 'hedigar1@gmail.com', '$2y$10$1A7sjgpcJKeBJXi3qjW6NuTKo7SD1j1ZvgG0EGMWAevdATE6XrhY2', 'usuario', 0, 1, '2025-12-28 19:49:22'),
(4, 'hedigar122', 'hedigar2@gmail.com', '$2y$10$kY9aHTN8F5UH1RWLhnltLeALutTouYpGlSmv56KOUl7NrUmwFZsLe', 'tecnico', 0, 1, '2025-12-28 19:49:32');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_cliente_documento_unico` (`documento`);

--
-- Índices de tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_chave` (`chave`);

--
-- Índices de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ordem_servico_id` (`ordem_servico_id`),
  ADD KEY `fk_equipamentos_cliente` (`cliente_id`);

--
-- Índices de tabela `itens_ordem_servico`
--
ALTER TABLE `itens_ordem_servico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ordem_servico_id` (`ordem_servico_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_created_at` (`created_at`),
  ADD KEY `idx_logs_usuario_id` (`usuario_id`);

--
-- Índices de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `status_atual_id` (`status_atual_id`),
  ADD KEY `fk_os_equipamento` (`equipamento_id`);

--
-- Índices de tabela `ordens_servico_status_historico`
--
ALTER TABLE `ordens_servico_status_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ordem_servico_id` (`ordem_servico_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `produtos_servicos`
--
ALTER TABLE `produtos_servicos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `status_os`
--
ALTER TABLE `status_os`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `itens_ordem_servico`
--
ALTER TABLE `itens_ordem_servico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `ordens_servico_status_historico`
--
ALTER TABLE `ordens_servico_status_historico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos_servicos`
--
ALTER TABLE `produtos_servicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `status_os`
--
ALTER TABLE `status_os`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `equipamentos`
--
ALTER TABLE `equipamentos`
  ADD CONSTRAINT `fk_equipamentos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Restrições para tabelas `itens_ordem_servico`
--
ALTER TABLE `itens_ordem_servico`
  ADD CONSTRAINT `itens_ordem_servico_ibfk_1` FOREIGN KEY (`ordem_servico_id`) REFERENCES `ordens_servico` (`id`);

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD CONSTRAINT `fk_os_equipamento` FOREIGN KEY (`equipamento_id`) REFERENCES `equipamentos` (`id`),
  ADD CONSTRAINT `ordens_servico_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ordens_servico_ibfk_2` FOREIGN KEY (`status_atual_id`) REFERENCES `status_os` (`id`);

--
-- Restrições para tabelas `ordens_servico_status_historico`
--
ALTER TABLE `ordens_servico_status_historico`
  ADD CONSTRAINT `ordens_servico_status_historico_ibfk_1` FOREIGN KEY (`ordem_servico_id`) REFERENCES `ordens_servico` (`id`),
  ADD CONSTRAINT `ordens_servico_status_historico_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status_os` (`id`),
  ADD CONSTRAINT `ordens_servico_status_historico_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
