-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 08/01/2026 às 14:51
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
-- Estrutura para tabela `atendimentos_externos`
--

CREATE TABLE `atendimentos_externos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `ordem_servico_id` int DEFAULT NULL,
  `descricao_problema` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco_visita` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_agendada` datetime DEFAULT NULL,
  `status` enum('pendente','agendado','em_deslocamento','concluido','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `valor_deslocamento` decimal(10,2) DEFAULT '0.00',
  `observacoes_tecnicas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `nome_completo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pessoa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_principal` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_secundario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_logradouro` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_bairro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_cidade` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome_completo`, `tipo_pessoa`, `documento`, `email`, `telefone_principal`, `telefone_secundario`, `endereco_logradouro`, `endereco_numero`, `endereco_bairro`, `endereco_cidade`, `data_nascimento`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Hedigar Eli Gonçalves', 'fisica', '02.875.745-042', 'hedigar9@gmail.com', '(51) 98169-5579', '', 'Rua Alexandre Renda 160/AP', '102', 'Centro', 'Osório', NULL, '', 1, '2026-01-05 15:13:53', NULL);

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
(1, 'porcentagem_venda', '55', 'Percentual aplicado sobre o custo para calcular o valor de venda', '2025-12-29 16:21:36', '2026-01-02 17:52:45'),
(14, 'impressao_fonte_tamanho', '12', 'Tamanho da fonte para impressão de OS', '2026-01-08 10:13:00', NULL),
(15, 'impressao_exibir_observacoes', '1', 'Exibir observações (defeito/laudo) na impressão de OS', '2026-01-08 10:13:00', NULL),
(16, 'impressao_texto_observacoes', 'A garantia de serviços é de 90 dias. Peças novas conforme fabricante. Equipamentos não retirados em 90 dias serão considerados abandonados.', 'Texto de observações/termos na impressão de OS', '2026-01-08 10:13:00', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipamentos`
--

CREATE TABLE `equipamentos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `ordem_servico_id` int DEFAULT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acessorios` text COLLATE utf8mb4_unicode_ci,
  `possui_fonte` tinyint(1) DEFAULT '0',
  `sn_fonte` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `equipamentos`
--

INSERT INTO `equipamentos` (`id`, `cliente_id`, `ordem_servico_id`, `tipo`, `marca`, `modelo`, `serial`, `senha`, `acessorios`, `possui_fonte`, `sn_fonte`, `ativo`) VALUES
(1, 1, NULL, 'Notebook', 'Dell', 'inspirion 15', '234234', 'as4d56as4d56', 'sdfsdfsdf', 0, NULL, 1),
(2, 1, NULL, 'Notebook', 'asdas', 'dasdas', 'dasd', 'asda', 'asdasd', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_ordem_servico`
--

CREATE TABLE `itens_ordem_servico` (
  `id` int NOT NULL,
  `ordem_servico_id` int NOT NULL,
  `tipo_item` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(10,2) DEFAULT '1.00',
  `custo` decimal(10,2) DEFAULT '0.00',
  `valor_unitario` decimal(10,2) DEFAULT '0.00',
  `valor_total` decimal(10,2) DEFAULT '0.00',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `valor_custo` decimal(10,2) DEFAULT '0.00',
  `valor_mao_de_obra` decimal(10,2) DEFAULT '0.00',
  `porcentagem_margem` decimal(10,2) DEFAULT '0.00',
  `comprar_peca` tinyint(1) DEFAULT '0',
  `link_fornecedor` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_ordem_servico`
--

INSERT INTO `itens_ordem_servico` (`id`, `ordem_servico_id`, `tipo_item`, `descricao`, `quantidade`, `custo`, `valor_unitario`, `valor_total`, `status`, `ativo`, `created_at`, `valor_custo`, `valor_mao_de_obra`, `porcentagem_margem`, `comprar_peca`, `link_fornecedor`) VALUES
(1, 11, 'produto', 'SSD 240ddddd', 1.00, 150.00, 225.00, 515.00, 'pendente', 1, '2026-01-02 03:50:33', 0.00, 290.00, 0.00, 0, NULL),
(2, 11, 'servico', 'FORMATAçãO', 1.00, 0.00, 220.00, 220.00, 'pendente', 0, '2026-01-02 03:50:47', 0.00, 0.00, 0.00, 0, NULL),
(3, 11, 'servico', 'FORMATAçãO', 1.00, 0.00, 220.00, 220.00, 'pendente', 0, '2026-01-02 03:51:24', 0.00, 0.00, 0.00, 0, NULL),
(4, 11, 'servico', 'FORMATAçãO', 1.00, 0.00, 220.00, 220.00, 'pendente', 1, '2026-01-02 04:13:21', 0.00, 0.00, 0.00, 0, ''),
(5, 11, 'produto', 'FONTE', 1.00, 95.00, 142.50, 172.50, 'pendente', 1, '2026-01-02 04:20:17', 0.00, 30.00, 0.00, 0, ''),
(6, 11, 'produto', 'SSD 240', 1.00, 200.00, 300.00, 300.00, 'pendente', 1, '2026-01-02 17:48:17', 0.00, 0.00, 0.00, 1, ''),
(7, 12, 'produto', 'SSD 240', 1.00, 1800.00, 2790.00, 3390.00, 'pendente', 1, '2026-01-03 02:13:22', 0.00, 600.00, 0.00, 0, ''),
(8, 12, 'servico', 'FORMATAçãO', 1.00, 0.00, 780.00, 780.00, 'pendente', 1, '2026-01-03 02:14:09', 0.00, 0.00, 0.00, 0, ''),
(9, 12, 'produto', 'FONTE', 1.00, 800.00, 1240.00, 1270.00, 'pendente', 1, '2026-01-03 02:14:21', 0.00, 30.00, 0.00, 0, ''),
(10, 12, 'servico', 'Troca de teclado notebook', 1.00, 200.00, 0.00, 0.00, 'pendente', 0, '2026-01-03 02:22:54', 0.00, 0.00, 0.00, 0, ''),
(11, 12, 'servico', 'Troca de teclado notebook', 1.00, 200.00, 0.00, 0.00, 'pendente', 0, '2026-01-03 02:22:55', 0.00, 0.00, 0.00, 0, ''),
(12, 12, 'servico', 'Formatacao sistema operacional', 1.00, 0.00, 1200.00, 1200.00, 'pendente', 1, '2026-01-03 02:23:14', 0.00, 0.00, 0.00, 0, ''),
(13, 12, 'servico', 'Limpeza interna computador', 1.00, 0.00, 1800.00, 1800.00, 'pendente', 1, '2026-01-03 02:24:37', 0.00, 0.00, 0.00, 0, ''),
(14, 12, 'produto', 'Fonte ATX 200 W', 17.00, 100.00, 0.00, 0.00, 'pendente', 1, '2026-01-04 01:14:27', 0.00, 0.00, 0.00, 0, ''),
(15, 2, 'servico', 'Formatacao sistema operacional', 1.00, 100.00, 0.00, 0.00, 'pendente', 1, '2026-01-08 01:41:53', 0.00, 0.00, 0.00, 0, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `acao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `dados_anteriores` json DEFAULT NULL,
  `dados_novos` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `referencia`, `ip_address`, `user_agent`, `dados_anteriores`, `dados_novos`, `created_at`) VALUES
(1, NULL, 'Tentativa de login falhou', 'E-mail: admin@admin.com', '100.109.146.66', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-05 13:49:56'),
(2, 2, 'Realizou login no sistema', NULL, '100.109.146.66', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-05 13:50:02'),
(3, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 15:08:28'),
(4, 2, 'Criou novo cliente', 'Cliente #1 - Hedigar Eli Gonçalves', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 15:13:53'),
(5, 2, 'Criou nova Ordem de Serviço', 'OS #1', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 15:14:19'),
(6, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:25:10'),
(7, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:25:14'),
(8, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:38:47'),
(9, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:38:50'),
(10, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:38:50'),
(11, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:38:51'),
(12, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:39:11'),
(13, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:39:12'),
(14, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:39:13'),
(15, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:39:20'),
(16, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:43:59'),
(17, NULL, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:44:04'),
(18, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com', '192.168.0.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 14:51:38'),
(19, 2, 'Realizou login no sistema', NULL, '192.168.0.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 14:51:43'),
(20, 2, 'Criou nova Ordem de Serviço', 'OS #2', '192.168.0.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 16:42:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico`
--

CREATE TABLE `ordens_servico` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `equipamento_id` int DEFAULT NULL,
  `defeito_relatado` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `laudo_tecnico` text COLLATE utf8mb4_unicode_ci,
  `valor_total_produtos` decimal(10,2) DEFAULT '0.00',
  `valor_total_servicos` decimal(10,2) DEFAULT '0.00',
  `valor_total_os` decimal(10,2) DEFAULT '0.00',
  `status_atual_id` int NOT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ordens_servico`
--

INSERT INTO `ordens_servico` (`id`, `cliente_id`, `equipamento_id`, `defeito_relatado`, `laudo_tecnico`, `valor_total_produtos`, `valor_total_servicos`, `valor_total_os`, `status_atual_id`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'sdfsdfsdf', '', 0.00, 0.00, 0.00, 1, 1, '2026-01-05 15:14:18', NULL),
(2, 1, 2, 'asdasd', '', 0.00, 0.00, 0.00, 1, 1, '2026-01-07 16:42:30', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico_status_historico`
--

CREATE TABLE `ordens_servico_status_historico` (
  `id` int NOT NULL,
  `ordem_servico_id` int NOT NULL,
  `status_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(8, 'Diagnostico tecnico', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(9, 'Limpeza interna notebook', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(10, 'Limpeza interna computador', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(11, 'Limpeza interna impressora', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(12, 'Formatacao sistema operacional', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(13, 'Instalacao sistema operacional', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(14, 'Backup de dados', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(15, 'Remocao de virus e malwares', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(16, 'Atualizacao de software', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(17, 'Configuracao de rede', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(18, 'Troca de pasta termica', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(19, 'Troca de tela notebook', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(20, 'Troca de teclado notebook', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(21, 'Manutencao preventiva', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(22, 'Manutencao corretiva', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(23, 'SSD SATA 120 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(24, 'SSD SATA 240 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(25, 'SSD SATA 480 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(26, 'SSD SATA 1 TB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(27, 'SSD M.2 NVME 256 GB', 'produto', 100.00, 155.00, 0.00, 1, '2026-01-03 02:21:36', '2026-01-04 01:43:59'),
(28, 'SSD M.2 NVMe 512 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(29, 'SSD M.2 NVMe 1 TB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(30, 'Memoria DDR4 4 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(31, 'Memoria DDR4 8 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(32, 'Memoria DDR4 16 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(33, 'Memoria DDR4 32 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(34, 'Memoria DDR5 8 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(35, 'Memoria DDR5 16 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(36, 'Memoria DDR5 32 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(37, 'Fonte ATX 200 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(38, 'Fonte ATX 400 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(39, 'Fonte ATX 500 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(40, 'Fonte ATX 600 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(41, 'Fonte ATX 750 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(42, 'HD SATA 500 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(43, 'HD SATA 1 TB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(44, 'HD SATA 2 TB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(45, 'Pasta termica', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(46, 'Cabo SATA', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(47, 'Fonte notebook universal', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(48, 'Teclado USB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(49, 'Mouse USB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(50, 'Adaptador USB Wi-Fi', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(51, 'Cooler para processador', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_os`
--

CREATE TABLE `status_os` (
  `id` int NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cor` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#000000',
  `ordem` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `status_os`
--

INSERT INTO `status_os` (`id`, `nome`, `cor`, `ordem`) VALUES
(1, 'Aberta', '#3498db', 1),
(2, 'Em Orcamento', '#f1c40f', 2),
(3, 'Aguardando Aprovacao', '#e67e22', 3),
(4, 'Em Execucao', '#9b59b6', 4),
(5, 'Finalizada', '#2ecc71', 5),
(6, 'Cancelada', '#e74c3c', 6),
(7, 'Para POA', '#1abc9c', 7),
(8, 'Para POA autorizado', '#16a085', 8),
(9, 'Falar com o Cliente', '#f39c12', 9),
(10, 'Aguardando Cliente', '#d35400', 10),
(11, 'Comprar Peça', '#34495e', 11),
(12, 'Aguardando Peça', '#2c3e50', 12),
(13, 'Em POA', '#1abc9c', 13);
-- (14, 'Paga', '#27ae60', 14),
-- (15, 'Entregue', '#2c3e50', 15);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_acesso` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trocar_senha` tinyint(1) DEFAULT '1',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel_acesso`, `trocar_senha`, `ativo`, `created_at`) VALUES
(1, 'Administrador', 'admin@admin.com', '$2a$12$pCam4vDGSIXWh8qqrDWpp.YXE5/Z/uA5nE0QNZigIjI/JNNxKuor2', 'admin', 0, 1, '2025-12-26 02:18:36'),
(2, 'hedigar', 'hedigar9@gmail.com', '$2y$10$5KvEhQ./Mu3InJQExe8o4eBRWI1vTN9/5E.si5g9.5szof8cugKBG', 'admin', 0, 1, '2025-12-28 22:01:51'),
(3, 'hedigar', 'hedigar1@gmail.com', '$2y$10$1A7sjgpcJKeBJXi3qjW6NuTKo7SD1j1ZvgG0EGMWAevdATE6XrhY2', 'usuario', 0, 1, '2025-12-28 22:49:22'),
(4, 'hedigar122', 'hedigar2@gmail.com', '$2y$10$kY9aHTN8F5UH1RWLhnltLeALutTouYpGlSmv56KOUl7NrUmwFZsLe', 'tecnico', 0, 1, '2025-12-28 22:49:32'),
(5, 'joao', 'test@kdjfkl.com', '$2y$10$vGjyTt6Dp8IqmvigsGr8xe4ZdRdCJjNHDze8a6E6hO3hkOMZp7j0.', 'usuario', 0, 1, '2026-01-02 17:54:15');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos_externos`
--
ALTER TABLE `atendimentos_externos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimento_cliente` (`cliente_id`),
  ADD KEY `fk_atendimento_usuario` (`usuario_id`),
  ADD KEY `fk_atendimento_os` (`ordem_servico_id`);

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
-- AUTO_INCREMENT de tabela `atendimentos_externos`
--
ALTER TABLE `atendimentos_externos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `itens_ordem_servico`
--
ALTER TABLE `itens_ordem_servico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `ordens_servico_status_historico`
--
ALTER TABLE `ordens_servico_status_historico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos_servicos`
--
ALTER TABLE `produtos_servicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de tabela `status_os`
--
ALTER TABLE `status_os`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos_externos`
--
ALTER TABLE `atendimentos_externos`
  ADD CONSTRAINT `fk_atendimento_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_atendimento_os` FOREIGN KEY (`ordem_servico_id`) REFERENCES `ordens_servico` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_atendimento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

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
