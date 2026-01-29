-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 28/01/2026 às 20:09
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
  `descricao_problema` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco_visita` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_agendada` datetime DEFAULT NULL,
  `status` enum('pendente','agendado','em_deslocamento','concluido','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `pagamento` enum('não','parcial','pago') COLLATE utf8mb4_unicode_ci DEFAULT 'não',
  `valor_deslocamento` decimal(10,2) DEFAULT '0.00',
  `valor_total` decimal(10,2) DEFAULT '0.00',
  `observacoes_tecnicas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `hora_entrada` time DEFAULT NULL,
  `hora_saida` time DEFAULT NULL,
  `tempo_total` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `equipamentos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detalhes_servico` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atendimentos_externos`
--

INSERT INTO `atendimentos_externos` (`id`, `cliente_id`, `usuario_id`, `ordem_servico_id`, `descricao_problema`, `endereco_visita`, `data_agendada`, `status`, `pagamento`, `valor_deslocamento`, `valor_total`, `observacoes_tecnicas`, `created_at`, `updated_at`, `hora_entrada`, `hora_saida`, `tempo_total`, `ativo`, `equipamentos`, `detalhes_servico`) VALUES
(2, 930, 2, NULL, 'Instalar novo certificado Digital', 'Rua Santos Dumont  2814 Albatroz Osório', '2026-01-06 00:00:00', 'concluido', 'não', 130.00, 0.00, '', '2026-01-11 23:35:57', '2026-01-11 23:36:20', NULL, NULL, '1:00', 1, '', ''),
(3, 930, 2, NULL, 'Ajuda no acesso de cancelamento de protesto de boletos (renato)', 'Rua Santos Dumont  2814 Albatroz Osório', '2026-01-09 00:00:00', 'concluido', 'não', 130.00, 0.00, 'Esse atendimento foi 10 minutos', '2026-01-11 23:37:25', NULL, NULL, NULL, '1:00', 1, '', 'Login feito pelo gov'),
(4, 931, NULL, NULL, 'Não funciona o pin pad', 'RS 30  Laranjeiras Osório', '2026-01-05 00:00:00', 'concluido', 'não', 130.00, 0.00, 'Configuração do clipp, para novas exigência do sefaz. (Cadastro do banco junto a TEF HOUSE)', '2026-01-11 23:41:59', '2026-01-20 13:56:08', NULL, NULL, '1:00', 1, NULL, NULL),
(6, 926, 9, NULL, 'Não funciona Office', 'R. Machado de Assis, 407 407 Centro Osorio', '2026-01-05 00:00:00', 'concluido', 'pago', 160.00, 0.00, '', '2026-01-13 20:24:21', NULL, NULL, NULL, '1:00', 1, '', 'Resintalar o programa. '),
(7, 940, NULL, NULL, 'Não imprime e nem funciona o Scanner', 'Osório', '2026-01-20 00:00:00', 'concluido', 'não', 130.00, 0.00, 'Notebook do Juliano está quente. Agendar Limpeza', '2026-01-20 13:52:16', NULL, '09:00:00', '10:00:00', '1 ', 1, 'RICOH 3500', 'Ajusta no nome da rede, conflito no NETBIOS.'),
(8, 944, NULL, NULL, 'Nao abre o Editor de Texto', 'Santos Dumont   Osório', '2026-01-20 00:00:00', 'concluido', 'pago', 130.00, 0.00, '', '2026-01-21 12:36:42', '2026-01-27 23:51:21', '14:00:00', '15:00:00', '1 ', 1, 'Computador da Frente Secretaria ', 'Reinstalado o software, voltou a funcionar. ');

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
(1, 'Luciano', 'Física', '12345', NULL, '123456', '123456', 'Rua do lado dalo', '1234', 'Osorio', '12', '0000-00-00', 'Migrado do sistema antigo', 0, '2020-05-18 11:28:16', '2020-05-18 11:28:16'),
(2, 'David Logam', 'Física', '00097348058', NULL, '(51) 98127-2169', '', 'Rua JosÃ© Vieira de Souza 135', 'ap 01', 'PanorÃ¢mico', 'OsÃ³rio', '1983-02-12', 'Migrado do sistema antigo', 1, '2020-05-18 15:39:35', '2020-05-18 15:39:35'),
(3, 'Jiovane Oliveira da Silveira', 'Física', '041.560.160-64', NULL, '51 998488543', '', '', 'Rua da ', 'Morro Alto', 'MaquinÃ©', '2000-09-02', 'Migrado do sistema antigo', 1, '2020-05-19 09:28:21', '2020-05-19 09:28:21'),
(7, 'Dorori Schaeffer Gatelli', 'Física', '004.158.220-92', NULL, '(51)99837-2743', '(51)3601-1891', 'Rs 30', 'km74', 'Laranjeiras', 'Osorio', '1945-01-24', 'Migrado do sistema antigo', 1, '2020-05-20 08:57:35', '2020-05-20 08:57:35'),
(8, 'Marcio Rauch', 'Física', '003.627.500-09', NULL, '(55)99609-3550', '', 'Estrada da Goiabeira', '504', 'Borrucia', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-05-20 13:41:34', '2020-05-20 13:41:34'),
(9, 'Edite Rodrigues da Rocha', 'Física', '597.773.200-78', NULL, '(51)98276-7472', '', 'Rua Bage', '25', 'Santa Luzia', 'OsÃ³rio', '1961-06-14', 'Migrado do sistema antigo', 1, '2020-05-20 14:12:59', '2020-05-20 14:12:59'),
(10, 'Ivanete Miranda', 'Física', '844.035.949-72', NULL, '', '', '', '', '', '', NULL, 'Migrado do sistema antigo', 1, '2020-05-20 17:21:31', '2020-05-20 17:21:31'),
(11, 'Adriana Lima', 'Física', '008.780.510-31', NULL, '(51)99669-5390', '', 'rua nossa senhora da conceiÃ§Ã£o', '144', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2020-05-21 15:12:20', '2020-05-21 15:12:20'),
(12, 'Marlei de Oliveira Santos', 'Física', '605.881.010-87', NULL, '(51)99958-3471', '', 'Rua 7 de Setembro', '672/102', 'centro', 'OsÃ³rio', '1970-01-21', 'Migrado do sistema antigo', 1, '2020-05-25 10:27:21', '2020-05-25 10:27:21'),
(13, 'Ludimila Xavier Silveira Dias', 'Física', '819.821.350-34', NULL, '(51)99175-0184', '(51)99235-0528', 'Av. Jorge Dariva', '2643', 'Parque Real', 'Osorio', '1980-11-14', 'Migrado do sistema antigo', 1, '2020-05-25 15:45:18', '2020-05-25 15:45:18'),
(14, 'Anderson Wust', 'Física', '016.969.620-05', NULL, '(51)99680-7276', '(51)99647-4703', 'Oto Lilinetal', '242', 'Albatroz', 'OsÃ³rio', '1988-05-29', 'Migrado do sistema antigo', 1, '2020-05-27 14:31:24', '2020-05-27 14:31:24'),
(15, 'Rogerio mauricio krenn', 'Física', '948.307.200-00', NULL, '(51)99618-8230', '', 'Rua das Enseadas', '819', 'Atlantida Sul', 'Osorio', '1980-04-07', 'Migrado do sistema antigo', 1, '2020-05-29 16:39:28', '2020-05-29 16:39:28'),
(16, 'Amanda de Oliveira', 'Física', '036.291.930-56', NULL, '(51)99664-2557', '', 'Rua 7 de Setembro', '672/101', 'Centro', 'OsÃ³rio', '1998-11-19', 'Migrado do sistema antigo', 1, '2020-06-01 16:53:01', '2020-06-01 16:53:01'),
(17, 'Adilson', 'Física', '215.412.132-21', NULL, '(51)99511-8590', '', 'Rua das Flores', '399', 'Primavera', 'Osorio', '1985-10-10', 'Migrado do sistema antigo', 1, '2020-06-02 14:46:50', '2020-06-02 14:46:50'),
(18, 'Deise Cardoso Oliveira Giacomelli', 'Física', '719.602.100-25', NULL, '(51)99955-0815', '', 'Duque de Caxias', '818', 'Centro', 'Maquine', '1971-05-13', 'Migrado do sistema antigo', 1, '2020-06-03 08:35:04', '2020-06-03 08:35:04'),
(19, 'Jane Rodrigues', 'Física', '790.189.550-00', NULL, '(51)99205-0607', '', 'lameda dos AraÃ§as', '45', 'CondomÃ­nio Parque da Lagoa', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2020-06-04 15:31:59', '2020-06-04 15:31:59'),
(20, 'Marcia Vanessa Batista', 'Física', '985.394.180-53', NULL, '(51)99689-1352', '', '', '', '', '', NULL, 'Migrado do sistema antigo', 1, '2020-06-05 09:05:35', '2020-06-05 09:05:35'),
(21, 'Alessandro da Silva Horn', 'Física', '001.219.870-60', NULL, '(51)99601-7807', '', 'Rua Emilio Francisco da Silva', '179', 'Albatroz', 'OsÃ³rio', '1981-12-17', 'Migrado do sistema antigo', 1, '2020-06-05 13:28:17', '2020-06-05 13:28:17'),
(22, 'Gilmar Bonorino', 'Física', '303.908.180-20', NULL, '(51)99695-6711', '', 'Rua Maria do Carmo,', '175', 'Bastos', 'Capivari do Sul', '1959-08-02', 'Migrado do sistema antigo', 1, '2020-06-05 18:20:26', '2020-06-05 18:20:26'),
(23, 'Jonas da Silva Kenne', 'Física', '022.895.170-46', NULL, '(51)99659-4576', '', 'Rua dos Trilhos', '695', 'Passinhos', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-06-09 14:12:00', '2020-06-09 14:12:00'),
(24, 'Miriane Leite Ramos', 'Física', '001.288.100-76', NULL, '(51)98431-2334', '', 'Pinheiro Machado', '760', 'Sulbrasileiro', 'OsÃ³rio', '1982-02-05', 'Migrado do sistema antigo', 1, '2020-06-09 17:55:25', '2020-06-09 17:55:25'),
(25, 'Fernando Luiz Nunes Ferreira', 'Física', '991.223.750-15', NULL, '(51)98464-4360', '(51)98865-7837', 'Rua Imbe', '1461', 'Primavera', 'Osorio', '1979-10-22', 'Migrado do sistema antigo', 1, '2020-06-12 13:37:57', '2020-06-12 13:37:57'),
(26, 'Aline da Rosa Araujo', 'Física', '001.892.120-55', NULL, '(51)98151-2152', '', 'Rua Major JoÃ£o Marques', '1075', 'Centro', 'OsÃ³rio', '1982-01-14', 'Migrado do sistema antigo', 1, '2020-06-13 11:10:07', '2020-06-13 11:10:07'),
(27, 'Rodrigo Simon', 'Física', '007.492.420-60', NULL, '(51)98512-1903', '', 'Rua da Lagoa', '1111', 'Belvile', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2020-06-15 14:57:35', '2020-06-15 14:57:35'),
(28, 'Claudineia Santos Nostrani', 'Física', '048.236.410-60', NULL, '(51)99636-6535', '(51)9864-1523', 'Linha fagundes dutra', '110', '', 'Maquine', '2001-03-14', 'Migrado do sistema antigo', 1, '2020-06-16 09:55:20', '2020-06-16 09:55:20'),
(29, 'Jairo Luiz Albuquerque dos Santos', 'Física', '360.767.240-72', NULL, '(54)99993-1981', '', 'Rua 7 de Setembro', '672/306', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-06-17 09:40:59', '2020-06-17 09:40:59'),
(30, 'Veranice Wolter', 'Física', '000.041.530-81', NULL, '(51)99995-2482', '', 'Nelson Silveira de Souza', '1393', 'Centro', 'OsÃ³rio', '1982-02-18', 'Migrado do sistema antigo', 1, '2020-06-17 14:12:14', '2020-06-17 14:12:14'),
(31, 'Gustavo Baptista Rissotto', 'Física', '041.646.460-20', NULL, '(51)99935-2586', '', 'Garibaldi', '190', 'Sulbrasileiro', 'OsÃ³rio', '2001-12-02', 'Migrado do sistema antigo', 1, '2020-06-17 15:00:12', '2020-06-17 15:00:12'),
(32, 'Tiago Ferreira Lopes', 'Física', '014.291.070-81', NULL, '51)995029658', '', 'Mario Miguel', '80', 'Passinhos', 'OsÃ³rio', '1986-03-18', 'Migrado do sistema antigo', 1, '2020-06-18 18:08:30', '2020-06-18 18:08:30'),
(33, 'Gabriela da Silva Panne', 'Física', '039.020.760-89', NULL, '(51)99225-6268', '', 'RS 030 Km 76', '4290', 'Laranjeiras', 'OsÃ³rio', '1996-06-03', 'Migrado do sistema antigo', 1, '2020-06-19 16:41:16', '2020-06-19 16:41:16'),
(34, 'Cristiano Duarte Pinheiro', 'Física', '609.391.410-00', NULL, '(51)92001-4983', '', 'BR 101 Km 73', '', 'AguapÃ©s', 'OsÃ³rio', '1973-02-06', 'Migrado do sistema antigo', 1, '2020-06-22 08:47:55', '2020-06-22 08:47:55'),
(35, 'Melissa negruni', 'Física', '623.982.370-87', NULL, '(51)99848-4779', '', 'Lateral BR 101 Km 86', '2395', 'Encosta da Serra', 'OsÃ³rio', '1972-05-18', 'Migrado do sistema antigo', 1, '2020-06-22 10:12:10', '2020-06-22 10:12:10'),
(36, 'JoÃ£o Batista Alvez', 'Física', '295.250.220-91', NULL, '(51)99785-5154', '', 'Osvaldo Bastos', '128', 'Gloria', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-06-23 11:10:01', '2020-06-23 11:10:01'),
(37, 'Bruna de Lima Monteiro', 'Física', '022.646.830-58', NULL, '(51)99405-1254', '', 'Rua 20 de Setembro', '409', 'Gloria', 'OsÃ³rio', '1992-07-05', 'Migrado do sistema antigo', 1, '2020-06-23 14:06:50', '2020-06-23 14:06:50'),
(38, 'Lorena da Cunha de Ávila (Vicente Inacio Martins d', 'Física', '235.272.180-68', NULL, '(51)99868-2756', '', 'Av. TÃºnel Verde', '653', '', '', NULL, 'Migrado do sistema antigo', 1, '2020-06-24 10:31:22', '2020-06-24 10:31:22'),
(39, 'Alessandro Gelsdorf', 'Física', '756.297.710-00', NULL, '(51)98948-6701', '', 'Rua Ancoras', '1228', 'Atlantida Sul', 'OsÃ³rio', '1974-09-15', 'Migrado do sistema antigo', 1, '2020-06-24 16:07:21', '2020-06-24 16:07:21'),
(40, 'Roselito de Lima Freitas', 'Física', '660.552.610-49', NULL, '(51)99157-9560', '', 'Rua Duque de Caixias', '276', 'Porto Lacustre', 'OsÃ³rio', '1972-03-08', 'Migrado do sistema antigo', 1, '2020-06-25 09:19:07', '2020-06-25 09:19:07'),
(41, 'Paulo Alves', 'Física', '225.206.270-34', NULL, '(51)99861-1032', '', 'Rua 16 de Dezembro', '528', 'Gloria', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-06-25 14:30:32', '2020-06-25 14:30:32'),
(42, 'Andreia Moraes', 'Física', '541.931.250-68', NULL, '(51)99785-7030', '', 'Antonio Marques da Rosa', '263/402', 'Sulbrasileiro', 'OsÃ³rio', '1971-06-04', 'Migrado do sistema antigo', 1, '2020-06-26 16:48:23', '2020-06-26 16:48:23'),
(43, 'Andriana Marques andrade', 'Física', '686.532.040-91', NULL, '(51)99805-1602', '(51)99959-1054', 'Travessa Peris Borges', '281', 'Santa Rosa', 'Capivari do Sul', NULL, 'Migrado do sistema antigo', 1, '2020-07-03 10:47:13', '2020-07-03 10:47:13'),
(44, 'Juliano Weber Sabadin', 'Física', '809.176.820-68', NULL, '(51)99921-7996', '(51)98569-7173', 'Machado de Assis', '481 / 5', 'centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2020-07-06 09:08:42', '2020-07-06 09:08:42'),
(46, 'Valtor Firmino da Silva', 'Física', '651.562.420-49', NULL, '(51)99667-6501', '', 'Rua Emilio Francisco da Silva', '108', 'Albatroz', 'OsÃ³rio', '1975-08-28', 'Migrado do sistema antigo', 1, '2020-07-09 14:13:45', '2020-07-09 14:13:45'),
(47, 'Rafael de Freitas Floriano', 'Física', '946.888.340-04', NULL, '(51)98438-3744', '(51)99991-8263', 'Rua 24 de Maio', '56', 'Centro', 'OsÃ³rio', '1978-06-09', 'Migrado do sistema antigo', 1, '2020-07-09 14:38:48', '2020-07-09 14:38:48'),
(48, 'Marta da Silva Alves', 'Física', '816.789.750-87', NULL, '(51)99708-1406', '(51)99613-0143', 'RS 030 km, 73', '7864', 'Bela Vista', 'OsÃ³rio', '1978-10-21', 'Migrado do sistema antigo', 1, '2020-07-10 14:51:14', '2020-07-10 14:51:14'),
(49, 'Amauri Boeira Fernandes Junior', 'Física', '020.753.620-17', NULL, '(51)99315-7268', '', 'armando fajardo', '763', 'Igara', 'Canoas', '1996-11-22', 'Migrado do sistema antigo', 1, '2020-07-11 09:49:53', '2020-07-11 09:49:53'),
(50, 'Dayana Dalpiaz dos Santos', 'Física', '833.131.250-34', NULL, '(51)99831-0722', '', 'Rua bento GonÃ§alves', '280', 'Caiu do Ceu', 'OsÃ³rio', '1985-12-22', 'Migrado do sistema antigo', 1, '2020-07-13 10:00:05', '2020-07-13 10:00:05'),
(51, 'Claudia Marta Schultz Venes Flores', 'Física', '454.523.850-53', NULL, '(51)99706-0717', '(51)3663-4550', 'Rua Garibaldi', '366, ap', 'Sulbrasileiro', 'osÃ³rio', '1966-02-26', 'Migrado do sistema antigo', 1, '2020-07-13 13:55:34', '2020-07-13 13:55:34'),
(52, 'Eunice Soares', 'Física', '425.108.990-15', NULL, '(51)98013-5634', '(51)98164-1071', 'RS 030', '4257', 'Laranjeiras', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-07-13 14:28:31', '2020-07-13 14:28:31'),
(53, 'Ana Paula Machado Ribeiro', 'Física', '023.863.870-70', NULL, '(51)99645-2198', '(51)99743-8517', 'Corel Reduzino Pacheco', '1205', 'Sulbrasileiro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-07-13 14:33:34', '2020-07-13 14:33:34'),
(54, 'Denis Caputi Araujo', 'Física', '466.245.380-15', NULL, '(51)99169-6320', '(51)99974-9975', 'Rua Coronel Reduzino Pacheco', '610', 'Centro', 'OsÃ³rio', '1967-11-20', 'Migrado do sistema antigo', 1, '2020-07-14 13:43:53', '2020-07-14 13:43:53'),
(55, 'Jonas Ariel da Rosa Cardoso', 'Física', '009.304.990-00', NULL, '(51)99653-3852', '', 'Rua Sinval Antonio Ribeiro', '580', 'Panoramico', 'OsÃ³rio', '1986-01-08', 'Migrado do sistema antigo', 1, '2020-07-17 16:43:42', '2020-07-17 16:43:42'),
(56, 'Cristiane Seelig Rosario', 'Física', '621.632.480-20', NULL, '(51)99969-4657', '(51)3160-4344', 'BR 101, 83', '', 'Arroio das Pedras', 'osÃ³rio', '1971-02-12', 'Migrado do sistema antigo', 1, '2020-07-23 16:49:20', '2020-07-23 16:49:20'),
(57, 'Andre Ferreira de Souza', 'Física', '093.024.299-89', NULL, '(48)99671-3290', '', 'Rua BarÃ£o do Rio Branco', '1221', 'OsÃ³rio', 'Centro', '1995-03-14', 'Migrado do sistema antigo', 1, '2020-07-23 20:38:25', '2020-07-23 20:38:25'),
(58, 'Renan Ribeiro Gomes', 'Física', '100.258.429-94', NULL, '(49)98898-8244', '', 'Nelson da Silva Kirch', '58 (apt', 'Porto', 'OsÃ³rio', '1997-11-25', 'Migrado do sistema antigo', 1, '2020-07-25 11:11:44', '2020-07-25 11:11:44'),
(59, 'Fatima de lurdes de Oliveira Ferri', 'Física', '882.248.480-00', NULL, '(51)99683-8432', '', 'Rua das Hortensias', '87', 'Laranjeiras', 'OsÃ³rio', '1976-10-13', 'Migrado do sistema antigo', 1, '2020-07-27 08:36:08', '2020-07-27 08:36:08'),
(60, 'Rovani Lauro Martins de Azevedo', 'Física', '290.220.100-15', NULL, '(51)99758-9319', '(51)3663-6321', 'Rua Pinheiro Machado', '787', 'Sulbrasileiro', 'OsÃ³rio', '1960-02-16', 'Migrado do sistema antigo', 1, '2020-07-27 11:19:05', '2020-07-27 11:19:05'),
(61, 'Raquel Dalpiaz', 'Física', '947.338.560-91', NULL, '(51)99927-5356', '', 'Rua Fermiano OsÃ³rio', '773', 'Centro', 'OsÃ³rio', '1960-01-11', 'Migrado do sistema antigo', 1, '2020-07-27 13:50:26', '2020-07-27 13:50:26'),
(62, 'Sandra Andrade', 'Física', '411.985.730-15', NULL, '(51)99762-2433', '', 'rua alexandre renda', '160 - A', 'centro', 'osorio', '1957-05-09', 'Migrado do sistema antigo', 1, '2020-07-29 08:52:58', '2020-07-29 08:52:58'),
(63, 'Jorge Luiz Pinto Garcia', 'Física', '040.362.350-92', NULL, '(51)99863-3865', '(51)99776-2363', 'Rua cessi Bastos', '368', 'Gloria', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-03 08:22:35', '2020-08-03 08:22:35'),
(64, 'Deiveti Goes Maciel', 'Física', '026.325.580-85', NULL, '(51)98900-4594', '', 'Osvaldo Bastos', '372', 'Gloria', 'OsÃ³rio', '1991-09-25', 'Migrado do sistema antigo', 1, '2020-08-03 09:51:36', '2020-08-03 09:51:36'),
(65, 'Angela Maria Fofonka Pires', 'Física', '463.662.530-72', NULL, '(51)99966-2570', '', 'BarÃ£o do Triunfo', '1986', 'Centro', 'OsÃ³rio', '1966-07-25', 'Migrado do sistema antigo', 1, '2020-08-04 16:46:51', '2020-08-04 16:46:51'),
(66, 'Lucas Alvez', 'Física', '002.915.930-03', NULL, '(51)99892-2687', '', '16 de Dezembro', '528', 'OsÃ³rio', 'Gloria', '1983-08-29', 'Migrado do sistema antigo', 1, '2020-08-07 08:14:05', '2020-08-07 08:14:05'),
(67, 'Taison Toledo Adolfo', 'Física', '014.977.690-00', NULL, '(51)99857-1923', '', 'Rua Santos Dumont', '1365', 'Sulbrasileiro', 'OsÃ³rio', '1987-10-04', 'Migrado do sistema antigo', 1, '2020-08-07 13:20:43', '2020-08-07 13:20:43'),
(68, 'Marcelle Borba dos Santos', 'Física', '016.173.790-00', NULL, '(51)98643-9976', '', 'Rua Alvita Alves de Oliveira', '42', 'Parque do Sol', 'OsÃ³rio', '1997-12-29', 'Migrado do sistema antigo', 1, '2020-08-07 13:52:16', '2020-08-07 13:52:16'),
(69, 'Silvia Fernanda Rosa Nunes da Silva', 'Física', '692.933.500-25', NULL, '(51)98465-5689', '(51)98266-0980', 'Rua Rio de Janeiro', '356', 'Caiu do Ceu', 'OsÃ³rio', '1973-03-25', 'Migrado do sistema antigo', 1, '2020-08-11 08:44:26', '2020-08-11 08:44:26'),
(70, 'Marinara Mattos Favin', 'Física', '025.430.320-02', NULL, '(51)99692-6039', '(51)99602-0868', 'Estrada da Figueira Grande', '990', 'Borrucia', 'Osorio', '1993-04-17', 'Migrado do sistema antigo', 1, '2020-08-11 15:10:37', '2020-08-11 15:10:37'),
(71, 'Lidia Rosa Alves', 'Física', '285.934.300-82', NULL, '(51)99289-0178', '', 'Rua Guarapari', '1133', 'Atlantida Sul', 'OsÃ³rio', '1951-10-10', 'Migrado do sistema antigo', 1, '2020-08-13 14:33:06', '2020-08-13 14:33:06'),
(72, 'Lucas Prussler dos Santos', 'Física', '01522154094', NULL, '51984080223', '51995133437', 'Rua Marechal Deodoro', '1150', 'Gloria', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-17 12:32:09', '2020-08-17 12:32:09'),
(73, 'Andre Santos Araujo', 'Física', '991.880.580-34', NULL, '(51)98205-0682', '(51)98326-0111', 'Rua Holfman', '56', 'Popular', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-17 13:28:53', '2020-08-17 13:28:53'),
(74, 'Samuel Wolter', 'Física', '031.990.309-55', NULL, '(51)99716-7420', '(51)99995-2482', 'Rua Dr. Nelson Silveira de Souza', '1393', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-17 13:44:26', '2020-08-17 13:44:26'),
(75, 'Cristiano Becker Maggi', 'Física', '973.591.490-53', NULL, '(51)99532-5200', '', 'Rua BarÃ£o do rio Branco', '444', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-18 14:16:20', '2020-08-18 14:16:20'),
(76, 'JoÃ£o Pedro', 'Física', '920.319.000-72', NULL, '(51)98011-7705', '(51)98029-2324', 'Rua Nelson Silveira de Souza', '1005', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-18 15:45:28', '2020-08-18 15:45:28'),
(77, 'Tatiana da Silva Silveira', 'Física', '718.286.720-68', NULL, '(51)98186-1182', '', 'Rua Antonio Mesquita', '677', 'Caiu do Ceu', 'OsÃ³rio', '1972-02-18', 'Migrado do sistema antigo', 1, '2020-08-19 14:33:26', '2020-08-19 14:33:26'),
(78, 'Nilton Seliste Pedro', 'Física', '370.069.240-49', NULL, '(51)99709-4345', '(51)99969-0361', 'Rua Manoel jose da Silva', '550', 'Engenho da Serra', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-25 13:52:24', '2020-08-25 13:52:24'),
(79, 'Sonia Mara Silveira Lopes', 'Física', '898.799.100-87', NULL, '(51)99828-2536', '(51)98055-9788', 'RS 030 Km 77', '3613', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2020-08-27 09:36:25', '2020-08-27 09:36:25'),
(80, 'Daiane Ferreira Rodrigues', 'Física', '018.141.050-85', NULL, '(51)98057-6885', '', 'JoÃ£o sarmento', '1518', 'Sul Brasileiro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-08-31 13:47:47', '2020-08-31 13:47:47'),
(81, 'Cristiane Mainardi da Silva', 'Física', '805.231.610-34', NULL, '(51)99141-1990', '', 'RS 40, km 75', '', 'Rancho Velho', 'Capivari do Sul', NULL, 'Migrado do sistema antigo', 1, '2020-08-31 14:41:08', '2020-08-31 14:41:08'),
(82, 'Gislaine Maria da Silva Gomes', 'Física', '350.703.150-72', NULL, '(51)99966-2194', '(51)3663-1265', 'Voluntarios da Patria', '252', 'Porto Lacustre', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-01 15:14:16', '2020-09-01 15:14:16'),
(83, 'Mariza Lima da Silva', 'Física', '541.877.610-04', NULL, '(51)99958-1976', '', 'Av Generelda', '945', 'Rainha do Mar', 'XangrilÃ¡', NULL, 'Migrado do sistema antigo', 1, '2020-09-02 07:33:06', '2020-09-02 07:33:06'),
(84, 'Pedro Henrique dos Santos Martin', 'Física', '031.625.430-45', NULL, '(51)98226-5379', '', 'SolidÃ£o', '2135', 'SolidÃ£o', 'Maquine', NULL, 'Migrado do sistema antigo', 1, '2020-09-03 12:41:13', '2020-09-03 12:41:13'),
(85, 'Igor Firmino da Silva', 'Física', '038.753.120-31', NULL, '(51)98278-9775', '(51)98190-8192', 'Rua General Osorio', '440', 'Pitangas', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-08 17:54:51', '2020-09-08 17:54:51'),
(86, 'Ozi De Matos', 'Física', '123.456.789', NULL, '', '(51)9997-6522', '(51)99791-1564', '', '', '', NULL, 'Migrado do sistema antigo', 1, '2020-09-11 13:17:56', '2020-09-11 13:17:56'),
(87, 'Isabel silveira dos santos', 'Física', '470.778.080-68', NULL, '(51)99915-7203', '', 'Rua JoÃ£o Inacio da Silva', '122', 'Caiu do Ceu', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-11 14:11:27', '2020-09-11 14:11:27'),
(88, 'Renato Amaral Rodrigues (IrmÃ£o Neriana)', 'Física', '305.201.690-34', NULL, '(51)99784-1132', '', 'RS 30 Km 77', '3620', 'Laranjeiras', 'OsÃ³rio', '2005-04-15', 'Migrado do sistema antigo', 1, '2020-09-15 12:28:43', '2020-09-15 12:28:43'),
(89, 'Galdino Vivian', 'Física', '095.625.080-10', NULL, '(51)98627-4737', '(51)98545-1426', 'Rua Bento gonÃ§alves', '279', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2020-09-18 10:11:42', '2020-09-18 10:11:42'),
(90, 'Leandro Silva', 'Física', '672.639.760-72', NULL, '(51)99714-3588', '(51', 'rua lagoa vermelha', '496', 'Centro', 'ImbÃ©', NULL, 'Migrado do sistema antigo', 1, '2020-09-18 13:16:02', '2020-09-18 13:16:02'),
(91, 'Claudiomar Ribeiro Pereira', 'Física', '644.894.930-53', NULL, '(51)99582-6801', '(51)99789-9458', 'Rua SÃ£o pedro do sul', '1115', 'Serra mar', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-21 14:30:00', '2020-09-21 14:30:00'),
(92, 'Guilherme Formagio Fernandes', 'Física', '023.196.060-33', NULL, '(51)98585-1298', '', 'BR 101', '3362', 'Encosta da Serra', 'OsÃ³rio', '1995-04-25', 'Migrado do sistema antigo', 1, '2020-09-23 07:52:56', '2020-09-23 07:52:56'),
(93, 'Lorena Horst', 'Física', '654.784.870-15', NULL, '(51)99902-9266', '', 'Rua Bage', '144', 'Santa Luzia', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-23 10:33:24', '2020-09-23 10:33:24'),
(94, 'Bruno Essig', 'Física', '039.005.270-16', NULL, '(51)99892-2556', '', 'Vila Gruta', '535', 'Barra do Ouro', 'MaquinÃ©', NULL, 'Migrado do sistema antigo', 1, '2020-09-23 13:03:05', '2020-09-23 13:03:05'),
(95, 'Regina Maria Alvez', 'Física', '233.566.000-44', NULL, '(51)99628-8308', '(48)98835-6033', 'Av Getulio Vargas', '531 ap ', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-28 14:18:47', '2020-09-28 14:18:47'),
(96, 'Gugue limpa foÃ§a', 'Física', '175.300.240-00', NULL, '(51)99354-4964', '', 'Rua 7 de Setembro', '2008', 'Gloria', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-28 16:44:42', '2020-09-28 16:44:42'),
(97, 'Rangel Zanandrea', 'Física', '012.141.300-46', NULL, '(54)99129-3689', '(54)99992-0146', 'Rua egidio escatula', '240', 'Sao Paulo', 'Tapejara', NULL, 'Migrado do sistema antigo', 1, '2020-09-29 08:41:58', '2020-09-29 08:41:58'),
(98, 'ParÃ³quia CATEDRAL', 'Física', '017.169.930-08', NULL, '(51)98160-4881', '', 'MARECHAL FLORIANO', '923', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2020-09-29 09:17:44', '2020-09-29 09:17:44'),
(99, 'Osvaldo Machado Filho', 'Física', '271.829.240-72', NULL, '(55)99710-9962', '(55)98107-5577', 'BarÃ£o do trinufo', '664 ap ', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-09-29 10:53:06', '2020-09-29 10:53:06'),
(100, 'Roberto de Souza Hallam', 'Física', '334.918.100-78', NULL, '(51)99956-2931', '(51)99662-0113', 'Rua das Seringueiras', '129', 'Parque Real', 'OsÃ³rio', '1961-06-04', 'Migrado do sistema antigo', 1, '2020-10-01 09:35:50', '2020-10-01 09:35:50'),
(101, 'Alencastro Ambiental Ltda', 'Física', '395.719.120-34', NULL, '(51)99512-0011', '', 'Major JoÃ£o Marques', '370/401', 'Centro', 'OsÃ³rio', NULL, 'Migrado do sistema antigo', 1, '2020-10-05 14:18:17', '2020-10-05 14:18:17'),
(326, 'Raquel mendes Correa', 'Física', '972.217.240-91', NULL, '(51)99969-7150', '', 'Rua Rosalino Araujo', '1482', 'Major Pinto', 'Palmares do Sul', '1977-11-19', 'Migrado do sistema antigo', 1, '2021-12-15 17:04:41', '2021-12-15 17:04:41'),
(327, 'Gabriela Loreto dos Santos', 'Física', '053.717.230-05', NULL, '(51)99744-5016', '', 'Rua Simões neto', '401', 'panoramico', 'Osório', '2001-10-09', 'Migrado do sistema antigo', 1, '2022-01-03 17:43:58', '2022-01-03 17:43:58'),
(328, 'Marcos Antonio Fortes Francisco', 'Física', '514.806.460-91', NULL, '(51)99708-3586', '', 'Br 101 km79', '', 'Livramento', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2022-01-04 14:18:18', '2022-01-04 14:18:18'),
(329, 'Rena Silva de Oliveira', 'Física', '041.610.230-14', NULL, '(51)98028-4333', '', 'Rua Jorge Dariva 2525', '2525 ap', 'Parque Real', 'Osório', '1997-01-04', 'Migrado do sistema antigo', 1, '2022-01-04 14:26:41', '2022-01-04 14:26:41'),
(330, 'Iguatemi Macedo Goulart', 'Física', '379.923.410-15', NULL, '(51)99422-6192', '', 'Rua João Pessoa', '755', 'Porto', 'Osório', '1960-05-28', 'Migrado do sistema antigo', 1, '2022-01-05 17:42:29', '2022-01-05 17:42:29'),
(331, 'Isair da Silva Santos', 'Física', '495.080.670-04', NULL, '(51)98323-4700', '', 'Travessa Formagio', '94', 'Porto Lacustre', 'Osório', '1970-12-30', 'Migrado do sistema antigo', 1, '2022-01-07 11:35:30', '2022-01-07 11:35:30'),
(332, 'Luana Crippa Borba da Rosa', 'Física', '036.131.740-90', NULL, '(51)9801-4409', '', 'RS 030 KM 88', '7925', 'Capão da arei', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-01-08 11:52:08', '2022-01-08 11:52:08'),
(333, 'Fernando Gabriel Dariva', 'Física', '056.743.169-02', NULL, '(51)99689-0896', '', 'AV Getulio Vargas', '101', 'Centro', 'Maquiné', '1985-07-05', 'Migrado do sistema antigo', 1, '2022-01-10 11:53:20', '2022-01-10 11:53:20'),
(334, 'Isadora Rosa Dias', 'Física', '012.096.370-14', NULL, '(51)98059-6723', '', 'Rua Barão do Triunfo', '234 ap ', 'Caiu do Céu', 'Osório', '2000-01-03', 'Migrado do sistema antigo', 1, '2022-01-11 12:51:34', '2022-01-11 12:51:34'),
(335, 'Rosimar Vieira', 'Física', '954.568.210-87', NULL, '(51)99822-7669', '', 'Rua Nair Dumbler', '7', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-01-11 13:18:18', '2022-01-11 13:18:18'),
(336, 'João Batista da Silva Nunes', 'Física', '03439539008', NULL, '(51)99749-5074', '', 'Rua Barão do Rio Branco', '728', 'Centro', 'Osório', '1998-10-09', 'Migrado do sistema antigo', 1, '2022-01-19 13:56:47', '2022-01-19 13:56:47'),
(337, 'Henrique Steinnzil Lopes', 'Física', '043.034.830-44', NULL, '(51)99522-0281', '', 'Avenida Getulio vargas', '531 ap1', 'Centro', 'Osório', '2001-03-27', 'Migrado do sistema antigo', 1, '2022-01-26 19:59:31', '2022-01-26 19:59:31'),
(338, 'Dilene  Nunes', 'Física', '396.551.480-68', NULL, '(51)98626-1740', '', 'Rua 7 de setembro ap 301', '782', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-01-28 19:20:54', '2022-01-28 19:20:54'),
(339, 'Igor Ferreira Menti', 'Física', '035.145.430-62', NULL, '(51)99617-5630', '', 'Rua Manuel', '44', 'Chacará velha', 'Palmares do Sul', '1996-04-01', 'Migrado do sistema antigo', 1, '2022-01-28 20:04:30', '2022-01-28 20:04:30'),
(340, 'Nelia Leiria Rocha Witt', 'Física', '430.963.950-04', NULL, '(51)99546-1162', '', 'Rua deulindo Magio', '93', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-01-31 20:25:56', '2022-01-31 20:25:56'),
(341, 'Rodrigo dos Santos Boeira', 'Física', '006.296.980-31', NULL, '(51)99727-2823', '', 'Rua Barão do Rio Branco', '1878', 'Sulbrasileiro', 'Osório', '1984-11-23', 'Migrado do sistema antigo', 1, '2022-02-01 12:58:49', '2022-02-01 12:58:49'),
(342, 'Hedi Jacinta Rambo Telles', 'Física', '731.902.760-72', NULL, '(51)99586-5666', '(51)99621-0757', 'Avenida Kilombo', '333', 'Centro', 'Capivari do Sul', '1973-01-30', 'Migrado do sistema antigo', 1, '2022-02-02 12:41:37', '2022-02-02 12:41:37'),
(343, 'Marcos Aurélio Santos de Menezes', 'Física', '400.853.410-53', NULL, '(51)98216-3130', '', 'Ruas Oscar Pacheco Geyer', '41', 'Pitangas', 'Osório', '1967-02-11', 'Migrado do sistema antigo', 1, '2022-02-03 18:16:51', '2022-02-03 18:16:51'),
(344, 'Natalia Avila', 'Física', '026.025.370-71', NULL, '(51)99809-9254', '', 'Rodovia RSC 101', 'KM 102', 'Rural', 'Osório', '1992-07-05', 'Migrado do sistema antigo', 1, '2022-02-08 16:53:31', '2022-02-08 16:53:31'),
(345, 'Dewes da Silva Junior', 'Física', '001.483.590-86', NULL, '(51)99808-7320', '', 'Rua Santana', '125', 'Popular', 'Osório', '1982-11-25', 'Migrado do sistema antigo', 1, '2022-02-08 18:45:52', '2022-02-08 18:45:52'),
(346, 'Daiane Nostrani Gomes', 'Física', '018.456.470-09', NULL, '(51)99686-9115', '', 'Rua Santana', '521', 'Popular', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-02-10 20:09:45', '2022-02-10 20:09:45'),
(347, 'Lidiane dos Santos', 'Física', '836.789.050-72', NULL, '(51)99912-7885', '', 'BR 101 Km 86', '3160', 'Costa Verde', 'Osório', '1985-10-16', 'Migrado do sistema antigo', 1, '2022-02-16 18:34:23', '2022-02-16 18:34:23'),
(348, 'Oscar Miguel Herrera Murdocco', 'Física', '094.038.088-96', NULL, '(54)99948-6042', '', 'Estrada Geral da Borrucia KM 5', '', 'Borrucia', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-02-18 12:42:28', '2022-02-18 12:42:28'),
(349, 'Ozeis da Conceição', 'Física', '033.051.430-02', NULL, '(51)99773-9373', '', 'Rua Dom Pedro de Alcântara', '64', 'Serra Mar', 'Osório', '1994-02-03', 'Migrado do sistema antigo', 1, '2022-02-21 14:16:25', '2022-02-21 14:16:25'),
(350, 'Dilma Santos de Oliveira', 'Física', '350.701.610-91', NULL, '(51)99678-3029', '', 'Estrada do Mar Km 6', '2890', 'Varzea do Padre', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-02-21 17:32:07', '2022-02-21 17:32:07'),
(351, 'Elisangela Pelissoli', 'Física', '748.052.820-53', NULL, '(51)3663-2024', '', 'Rua 24 DE maio', '451 sal', 'Centro', 'Osório', '1974-08-06', 'Migrado do sistema antigo', 1, '2022-02-21 20:22:59', '2022-02-21 20:22:59'),
(352, 'Vladimir Cardoso', 'Física', '580.232.240-34', NULL, '(51)99638-8183', '', 'Barão do Triunfo', '1225', 'Centro', 'Osório', '1791-11-02', 'Migrado do sistema antigo', 1, '2022-02-22 14:22:25', '2022-02-22 14:22:25'),
(353, 'Joice Santos Ferri', 'Física', '014.136.460-26', NULL, '(51)98021-5965', '', 'Rua Jose Airton Jaques de Oliveira', '539', 'Caravagio', 'Osório', '1986-01-27', 'Migrado do sistema antigo', 1, '2022-02-23 14:02:06', '2022-02-23 14:02:06'),
(354, 'Gregor Marco da Silva', 'Física', '034.665.710-57', NULL, '(51)99702-8122', '', 'Ladislau Vieira', '692', 'Granja Vargas', 'Palmares do Sul', '1997-12-30', 'Migrado do sistema antigo', 1, '2022-02-23 14:16:57', '2022-02-23 14:16:57'),
(355, 'Wagner Nunes', 'Física', '893.158.470-91', NULL, '(51)98427-4842', '', 'Rua Tenente Mogar', '185', 'Pirua', 'Osório', '1977-09-09', 'Migrado do sistema antigo', 1, '2022-02-23 18:03:07', '2022-02-23 18:03:07'),
(356, 'Emerson Marins', 'Física', '710.381.400-72', NULL, '(51)99907-0732', '(51)99817-5837', 'Prefeitura de osorio', '', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-02-24 11:31:54', '2022-02-24 11:31:54'),
(357, 'João Antonio Rigotti', 'Física', '215.719.889-04', NULL, '(51)98108-4882', '(51)8278-7093', '24 de maio', '835 ap ', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-02-24 11:51:59', '2022-02-24 11:51:59'),
(358, 'Marilu Batista', 'Física', '161.363.730-68', NULL, '(51)99972-9092', '', 'Emilio Tarrago', '129', 'Porto', 'Osório', '1947-09-14', 'Migrado do sistema antigo', 1, '2022-02-25 13:20:17', '2022-02-25 13:20:17'),
(359, 'Eva de Oliveira Consul', 'Física', '612.107.900-25', NULL, '(51)9742-8785', '(51)9593-0720', 'Estrada Geral', '2549', 'Borrusia', 'Osório', '1967-10-17', 'Migrado do sistema antigo', 1, '2022-03-02 16:37:00', '2022-03-02 16:37:00'),
(360, 'Mário Henrique Benfica', 'Física', '923.535.920-15', NULL, '(51)99345-6206', '', 'Rua Firmiano', '391', 'Caio do Céu', 'Osório', '1977-03-12', 'Migrado do sistema antigo', 1, '2022-03-04 16:55:40', '2022-03-04 16:55:40'),
(361, 'Marcia Elaine', 'Física', '719.417.890-72', NULL, '(51)99856-8297', '', 'Travessa Formagio', '115', 'Porto lacustre', 'Osório', '1974-11-29', 'Migrado do sistema antigo', 1, '2022-03-05 12:40:17', '2022-03-05 12:40:17'),
(362, 'Bianca Aquere Lucas', 'Física', '933.089.900-59', NULL, '(51)99786-8266', '', 'Rua 15 de novembro', '1455', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-03-07 20:05:56', '2022-03-07 20:05:56'),
(363, 'Maicon dos Santos Amigoni', 'Física', '032.271.790-67', NULL, '(51)99649-4812', '', 'Ildefonso Simões Lopes', '1133/30', 'Pitangas', 'Osório', '1995-01-26', 'Migrado do sistema antigo', 1, '2022-03-11 18:23:21', '2022-03-11 18:23:21'),
(364, 'Mikael Pinheiro da Silveira', 'Física', '042.263.890-00', NULL, '(51)9575-6299', '', 'Marechal Floriano Peixoto', '1086', 'Centro', 'Osório', '2001-09-08', 'Migrado do sistema antigo', 1, '2022-03-15 18:21:57', '2022-03-15 18:21:57'),
(365, 'Aurea Estela da Silva', 'Física', '710.379.340-91', NULL, '(51)999632-9109', '(51)3663-2869', 'BR 101 KM 87', 'STIL MO', 'CENTRO', 'OSÓRIO', '1956-02-23', 'Migrado do sistema antigo', 1, '2022-03-16 16:34:03', '2022-03-16 16:34:03'),
(366, 'Aldair Alves de Noraes', 'Física', '120.648.900-68', NULL, '(51)99644-7116', '', 'Rua Pinheiro Machado', '507 ap ', 'Sul Brisileiro', 'Osório', '1951-07-03', 'Migrado do sistema antigo', 1, '2022-03-21 16:34:36', '2022-03-21 16:34:36'),
(367, 'Pedro Luciano Motta', 'Física', '578.302.820-72', NULL, '(51)99823-9073', '', '15 de novembro', '519/505', 'Centro', 'Osório/rs', '1973-10-23', 'Migrado do sistema antigo', 1, '2022-03-21 19:01:47', '2022-03-21 19:01:47'),
(368, 'Rodrigo Anflor', 'Física', '013.280.3801-1', NULL, '(51)99511-7592', '', 'Estrada da Perua', '18', '', 'Osório', '1991-08-01', 'Migrado do sistema antigo', 1, '2022-03-23 12:00:59', '2022-03-23 12:00:59'),
(369, 'Rodrigo', 'Física', '013.280.380-17', NULL, '', '', '', '', '', '', NULL, 'Migrado do sistema antigo', 1, '2022-03-23 12:04:57', '2022-03-23 12:04:57'),
(370, 'Rosilaine Aliardi', 'Física', '017.502.310-77', NULL, '(51)999387-5187', '(51)99501-5552', 'Santos dumont', '2240', 'Albatroz', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-03-28 14:41:02', '2022-03-28 14:41:02'),
(371, 'Juliano Firmino', 'Física', '013.139.3660-0', NULL, '(51)98278-9775', '', 'Rua General Osorio', '440', 'Pitangas', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-03-29 17:08:44', '2022-03-29 17:08:44'),
(373, 'Juliano Firmino', 'Física', '113.393.660-02', NULL, '(51)98278-9775', '', 'Rua General Osorio', '440', 'Pitangas', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-03-29 17:09:40', '2022-03-29 17:09:40'),
(374, 'Sidinei', 'Física', '396.503.320-49', NULL, '(51)98194-8677', '(51)3663-3677', '7 DE Setembro', '1331', 'Gloria', 'osório', NULL, 'Migrado do sistema antigo', 1, '2022-03-31 11:28:53', '2022-03-31 11:28:53'),
(375, 'Fabio Dutra', 'Física', '963.608.010-00', NULL, '(51)99647-1717', '', 'Farrapos', '372', 'Sul Brasileiro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-05 16:32:16', '2022-04-05 16:32:16'),
(376, 'Sonia Azeredo', 'Física', '570.732.440-20', NULL, '(51)99951-5553', '', 'Rua da lagoa casa 20', '1401', 'Farroupilha', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-05 18:02:10', '2022-04-05 18:02:10'),
(377, 'Franciele Miranda vieira', 'Física', '035.717.240-03', NULL, '(51)98011-2640', '', 'Rua Dotor Mario Santo dani', '1189', '', 'Osório', '1994-10-06', 'Migrado do sistema antigo', 1, '2022-04-05 20:29:56', '2022-04-05 20:29:56'),
(378, 'Danubia Machado da Silveira', 'Física', '014.206.770-98', NULL, '(51)99831-4118', '', 'Antonio Mesquita', '640', 'Caio do Céu', 'Osório', '1988-06-08', 'Migrado do sistema antigo', 1, '2022-04-07 19:44:24', '2022-04-07 19:44:24'),
(379, 'Juliana Melo Cordeiro', 'Física', '928.401.080-20', NULL, '(51)9141-8781', '', 'RS 0330829', '02', 'Laranjeiras', 'Osório', '1977-04-11', 'Migrado do sistema antigo', 1, '2022-04-08 17:42:55', '2022-04-08 17:42:55'),
(380, 'Everson Matheus dos Santos Teixeira', 'Física', '030.098.410-35', NULL, '(51)9896-2184', '', 'Marcelino Nunes da Silveira', '210', 'Laranjeiras', 'Osório', '1993-03-07', 'Migrado do sistema antigo', 1, '2022-04-11 17:06:51', '2022-04-11 17:06:51'),
(381, 'João Batista Pereira', 'Física', '340.982.750-15', NULL, '(51)99971-7107', '', 'Strada do carashi', '1000', '', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-13 16:37:45', '2022-04-13 16:37:45'),
(382, 'Arthur dos Santos Carvalho', 'Física', '043.869.680-82', NULL, '(51)9952-4213', '', 'Rua Boa ventura de souza', '527', 'Caravajo', 'Osório', '2002-01-14', 'Migrado do sistema antigo', 1, '2022-04-18 17:30:48', '2022-04-18 17:30:48'),
(383, 'Dionatas Binello dos Santos', 'Física', '016.654.680-17', NULL, '(51)98038-3550', '', 'Av. Brasil', '1732', 'Caravagio', 'Osório', '1986-08-25', 'Migrado do sistema antigo', 1, '2022-04-20 13:22:47', '2022-04-20 13:22:47'),
(384, 'Maria Loriza de Lima Pires', 'Física', '423.698.190-49', NULL, '(51)99919-5976', '', 'Getulio Vargas', '1102/50', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-22 12:48:18', '2022-04-22 12:48:18'),
(385, 'Rodrigo Rogrigues Vaz', 'Física', '624.815.920-34', NULL, '(51)99232-3152', '(51)98452-4746', 'Barão do Rio Branco', '688', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-22 14:11:56', '2022-04-22 14:11:56'),
(386, 'Alice Lauriane Pires Ferreira', 'Física', '710.410.280-91', NULL, '(51)99675-7217', '', 'Rua Joanim Gamba', '552', 'Caiu do ceu', 'Osório', '1973-02-20', 'Migrado do sistema antigo', 1, '2022-04-22 20:46:59', '2022-04-22 20:46:59'),
(387, 'Bruna Santos Candido', 'Física', '049.534.380-35', NULL, '(51)99619-1056', '(51)99761-6931', '7 DE Setembro', '483', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-25 14:19:14', '2022-04-25 14:19:14'),
(388, 'Gabriel Inacio de Farias', 'Física', '043.877.040-47', NULL, '(51)99560-4544', '', 'Rua marechal deodoro', '1273', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-25 14:31:30', '2022-04-25 14:31:30'),
(389, 'Roberto dos santos Bobsin', 'Física', '644.934.660-49', NULL, '(51)99691-3721', '', 'firmiano osorio', '1177', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-25 16:40:41', '2022-04-25 16:40:41'),
(390, 'Edson dorine', 'Física', '166.795.980-87', NULL, '(51)98300-2867', '', 'Rua Osvaldo Bastos', '297', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-04-29 12:11:18', '2022-04-29 12:11:18'),
(391, 'Marta Giseli Machado', 'Física', '820.657.300-30', NULL, '(51)99726-4878', '', 'Leão Rodrigues Madalena', '12', 'Pitangas', 'Osório', '1997-09-09', 'Migrado do sistema antigo', 1, '2022-04-30 14:30:08', '2022-04-30 14:30:08'),
(392, 'Julio Leone Barcelo dos Santos', 'Física', '027.363.360-03', NULL, '(51)99580-6321', '(51)99789-2372', 'AV tunel Verde', '593', 'Tunel Verde', 'Balnerio Pinhal', NULL, 'Migrado do sistema antigo', 1, '2022-05-02 11:34:52', '2022-05-02 11:34:52'),
(393, 'Tatiele Brusch Grassi', 'Física', '026.261.690-48', NULL, '(51)99831-3056', '', 'Rua Santos Dumont', '226/301', 'Centro', 'Osório', '1996-02-15', 'Migrado do sistema antigo', 1, '2022-05-04 16:33:32', '2022-05-04 16:33:32'),
(394, 'Pedro da Silveira Dias', 'Física', '580.941.800-72', NULL, '(51)99983-7222', '', 'Rua 24 de Maio', '808/202', 'Centro', 'Osório', '1961-12-02', 'Migrado do sistema antigo', 1, '2022-05-04 18:40:25', '2022-05-04 18:40:25'),
(395, 'Aparecida Celia Carvalho', 'Física', '818.298.337-15', NULL, '(21)99420-3292', '(51)99999-9999', 'Rua Julio de Castilho,', '1009/40', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-04 19:01:03', '2022-05-04 19:01:03'),
(396, 'Bruno Silva Pereira Messaig', 'Física', '053.556.760-06', NULL, '(51)99634-2297', '', 'Rua bela vista', '260', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-05 12:11:30', '2022-05-05 12:11:30'),
(397, 'Maria Beatriz Vieira Pedroso', 'Física', '053.573.480-81', NULL, '(51)99749-5324', '', 'Barra Do Ouro', '827', 'Primaveira', 'Osorio', '2001-08-09', 'Migrado do sistema antigo', 1, '2022-05-05 21:59:10', '2022-05-05 21:59:10'),
(398, 'Carmen Reyes Martin', 'Física', '841.125.060-15', NULL, '(51)99645-7567', '(51)99794-1037', 'Rua Delma da Silva', '975', 'Emboaba', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-06 13:02:16', '2022-05-06 13:02:16'),
(399, 'Eder Luis Nunes Mendes (IEL)', 'Física', '764.381.200-49', NULL, '(51)99677-2833', '(36)6318-61', 'Mario Santo Dani', '183', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-09 17:03:11', '2022-05-09 17:03:11'),
(400, 'Diego Menezes Bastos', 'Física', '029.937.440-89', NULL, '(51)98276-1308', '', 'Rua Capão da Canoa', '889', 'Primavera', 'Osório', '1993-06-23', 'Migrado do sistema antigo', 1, '2022-05-09 17:30:28', '2022-05-09 17:30:28'),
(401, 'Wladirema Rosa Dias', 'Física', '547.590.050-72', NULL, '(51)99495-7777', '', '15 De novembro', '1101 ap', 'Centro', 'Osório', '1996-10-29', 'Migrado do sistema antigo', 1, '2022-05-09 20:28:19', '2022-05-09 20:28:19'),
(402, 'Nicole da Silva de Souza', 'Física', '043.585.070-93', NULL, '(51)99950-4338', '(51)3601-0593', 'Av Getulio Vargas', '1102', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-16 20:49:46', '2022-05-16 20:49:46'),
(403, 'Tanise Alves de Oliveira', 'Física', '032.307.340-92', NULL, '(51)99910-1520', '', 'Estrada do Mar Km 6', '2795', 'Vargea do Padre', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-19 11:33:43', '2022-05-19 11:33:43'),
(404, 'Marcia F Fagundes', 'Física', '287.027.190-53', NULL, '(51)99665-1931', '', 'Alzira sarcone Martins', '113', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-20 14:12:55', '2022-05-20 14:12:55'),
(405, 'Ronaldo Colombo Flor', 'Física', '959.269.090-15', NULL, '(51)99687-8031', '', 'Rua Marechal Floriano', '1961', 'Centro', 'Osório', '1979-06-19', 'Migrado do sistema antigo', 1, '2022-05-20 17:38:50', '2022-05-20 17:38:50'),
(406, 'Mairon Noronha da Rosa', 'Física', '009.099.650-05', NULL, '(51)99555-1392', '', 'Major João Marques', '500', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-05-23 17:11:51', '2022-05-23 17:11:51'),
(407, 'Marcos José Baggo', 'Física', '042.707.290-58', NULL, '(51)99528-7501', '', 'Rua Ipanema', '1276', 'Atlantida sul', 'Oaório', '1997-08-30', 'Migrado do sistema antigo', 1, '2022-05-24 12:20:29', '2022-05-24 12:20:29'),
(408, 'Elviz Ney Carmargo Conceição', 'Física', '432.134.130-20', NULL, '(51)99933-7965', '', 'Barao do rio branco Sao sipriano', '1004', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2022-05-26 14:38:32', '2022-05-26 14:38:32'),
(409, 'Juliana cardoso', 'Física', '013.747.890-97', NULL, '(51)99891-1995', '', 'Rua 6', '76', 'Mariluz Norte', 'Imbé', '1987-08-07', 'Migrado do sistema antigo', 1, '2022-05-28 11:37:03', '2022-05-28 11:37:03'),
(410, 'Pedro Francisco Schoffen', 'Física', '140.977.530-53', NULL, '(51)99978-5706', '', 'Major João Marques', '1673', 'Sulbrasileiro', 'Osório', '1952-06-01', 'Migrado do sistema antigo', 1, '2022-05-31 20:29:44', '2022-05-31 20:29:44'),
(411, 'Diulia Peres da Silva', 'Física', '034.743.140-25', NULL, '(51)98668-6772', '', 'Rua Paul Harrys', '250', 'Centro', 'Osório', '1995-12-15', 'Migrado do sistema antigo', 1, '2022-06-01 18:03:07', '2022-06-01 18:03:07'),
(412, 'Gustavo Michelon', 'Física', '001.338.810-73', NULL, '(51)98279-5038', '', 'Str do Rincao', '700', 'Aguapes', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-02 13:31:01', '2022-06-02 13:31:01'),
(413, 'Gislaine Moura', 'Física', '918.956.770-68', NULL, '(51)99361-4892', '(51)3601-0762', 'Rua Leopodo Nunes Martins', '98', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-03 12:26:26', '2022-06-03 12:26:26'),
(414, 'Flavio Fagundes da Silva', 'Física', '356.020.070-91', NULL, '(51)99187-0330', '', 'Rua ceci bastos', '255', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-03 18:12:30', '2022-06-03 18:12:30'),
(415, 'Camilla Ambrosi Borges', 'Física', '039.699.150-58', NULL, '(51)99542-7495', '(51)99716-4983', 'Rua capão da canoa', '816', 'Primavera', 'O´sorio', NULL, 'Migrado do sistema antigo', 1, '2022-06-06 18:19:20', '2022-06-06 18:19:20'),
(416, 'Alessandra Dalpiaz', 'Física', '777.895.550-49', NULL, '(51)98152-8436', '(51)99580-2601', 'Voluntarios da patria', '200', 'Porto Lacustre', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-06 18:31:16', '2022-06-06 18:31:16'),
(417, 'Gislene Boeira Gomes', 'Física', '008.465.680-80', NULL, '(51)99927-7922', '(51)99640-2676', 'Rua Ildeofonso  Simoes Lopes', '1133/20', 'Pitangas', 'Osório', '1981-12-11', 'Migrado do sistema antigo', 1, '2022-06-08 19:11:59', '2022-06-08 19:11:59'),
(418, 'Larissa De Barros Oliveiras', 'Física', '101.002.659-36', NULL, '(51)99158-1902', '(51)99230-8695', 'Miguel Luiz Isopo', '226', 'Pitangas', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-09 13:43:36', '2022-06-09 13:43:36'),
(419, 'Magda de Fraga Pereira', 'Física', '985.380.630-49', NULL, '(51)98103-3020', '', 'Marechal Deodoro', '1040/03', 'Centro', 'Osório', '1978-10-23', 'Migrado do sistema antigo', 1, '2022-06-13 13:21:59', '2022-06-13 13:21:59'),
(420, 'Daiane Fagundes da Silva', 'Física', '014.292.450-42', NULL, '(51)98313-1266', '(51)3663-9492', 'Rua Santos Dumont', '1710', 'Albatros', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-13 13:53:24', '2022-06-13 13:53:24'),
(421, 'Igor Velho', 'Física', '809.683.500-97', NULL, '(51)99261-4169', '(51)99240-3517', 'Marechal dodoro', '259', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-13 16:52:50', '2022-06-13 16:52:50'),
(422, 'Janice De Nard', 'Física', '651.552.380-72', NULL, '(51)99668-5171', '', 'Traveça Nossa Senhora da Conceição', '10', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-13 17:04:17', '2022-06-13 17:04:17'),
(423, 'Antônio Gilberto Gomes', 'Física', '626.564.570-68', NULL, '(51)99876-2307', '(51)98957-5091', 'Av Tunel Verde', '862', 'Tunel Verde', 'Pinhal', NULL, 'Migrado do sistema antigo', 1, '2022-06-13 18:00:53', '2022-06-13 18:00:53'),
(424, 'Dayana da Conceição de Souza', 'Física', '021.660.780-95', NULL, '(51)99770-3349', '', 'Rua Boa Ventura de Souza', '376', 'Caravajio', 'Osório', '1989-06-27', 'Migrado do sistema antigo', 1, '2022-06-13 19:08:24', '2022-06-13 19:08:24'),
(425, 'Lidia Simone Batista', 'Física', '002.419.610-09', NULL, '(51)99857-6649', '', 'Estrada da Pedra', '301', 'Borrucia', 'Osório', '1983-01-08', 'Migrado do sistema antigo', 1, '2022-06-14 14:14:51', '2022-06-14 14:14:51'),
(426, 'Jairo Lopes da Costa', 'Física', '608.281.100-34', NULL, '(51)98103-5592', '', 'Rua Ceci Bastos', '155', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-15 12:31:50', '2022-06-15 12:31:50'),
(427, 'Everson Mateus dos Santos Teixeira', 'Física', '030.984.103-35', NULL, '(51)99896-2184', '(51)99202-3345', 'Marcelino Nunes da Silveira', '410', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-21 14:08:10', '2022-06-21 14:08:10'),
(428, 'Elton Almeida dos Santos', 'Física', '804.759.060-04', NULL, '(55)99931-7828', '', 'Rua Nelson', '574 cas', 'Centro', 'Osório', '1980-01-29', 'Migrado do sistema antigo', 1, '2022-06-21 19:02:31', '2022-06-21 19:02:31'),
(429, 'Pedro de Oliveira Dias', 'Física', '261.981.180-53', NULL, '(51)99503-3477', '', 'Rua Alcindo Lopes Benfica', '87', 'Laranjeiras', 'Osório', '1954-07-11', 'Migrado do sistema antigo', 1, '2022-06-22 16:14:32', '2022-06-22 16:14:32'),
(430, 'Renan Rolim Santos', 'Física', '898.143.125-68', NULL, '(51)98011-9671', '', 'Getulio Vargas', '1291', 'Centro', 'Osório', '1976-10-25', 'Migrado do sistema antigo', 1, '2022-06-23 13:26:52', '2022-06-23 13:26:52'),
(431, 'Jose Adailton da Silveira', 'Física', '177.020.380-04', NULL, '(51)99966-3888', '', 'BR 101 Km 81', '6015', 'Arroio das Pedras', 'Osório', '1952-11-04', 'Migrado do sistema antigo', 1, '2022-06-24 18:43:39', '2022-06-24 18:43:39'),
(432, 'Mirian Anflor', 'Física', '001.832.660-62', NULL, '(51)98030-3566', '', 'Rua minas do Leao', '326', 'Serra mar', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-06-29 19:07:36', '2022-06-29 19:07:36'),
(433, 'Arenis da Silva', 'Física', '01885549040', NULL, '(51)99553-4635', '', 'estrada da produçao', '850', 'borrusia', 'osorio', NULL, 'Migrado do sistema antigo', 1, '2022-07-02 11:29:56', '2022-07-02 11:29:56'),
(434, 'Longuino', 'Física', '437.379.350-68', NULL, '(51)99819-2298', '(51)99855-9710', 'Pasinhos/Ferreirinha', '266', 'ferreirinha', 'Osorio destrito passinhos', NULL, 'Migrado do sistema antigo', 1, '2022-07-06 20:14:18', '2022-07-06 20:14:18'),
(435, 'Ana Carla Suelem de Souza', 'Física', '035.686.660-22', NULL, '(51)99882-4083', '', 'Gian Macedo', '652', 'Caravagio', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-07-12 18:42:01', '2022-07-12 18:42:01'),
(436, 'Paulo Dutra', 'Física', '661.236.310-04', NULL, '(51)98144-6280', '(51)98192-6694', 'Capao da Canoa', '375', 'Medianeira', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-07-14 12:31:42', '2022-07-14 12:31:42'),
(437, 'Vanderlei decosta', 'Física', '676.309.600-34', NULL, '(51)99662-0223', '', 'Rua gaspar de lemos', '157', 'Vila Ipiranga', 'Porto alegre', NULL, 'Migrado do sistema antigo', 1, '2022-07-15 14:17:37', '2022-07-15 14:17:37'),
(438, 'Micael Rios', 'Física', '042.224.000-17', NULL, '(51)99411-9539', '', 'Ildfonso simones lopes', '2136', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-07-20 12:22:22', '2022-07-20 12:22:22'),
(439, 'Daiana', 'Física', '016.629.140-40', NULL, '(51)99859-4592', '', 'Avenida marcilio dias', '1922', 'pitangas', 'osorio', '1989-08-17', 'Migrado do sistema antigo', 1, '2022-07-20 17:06:00', '2022-07-20 17:06:00'),
(440, 'Elizandro Jacoby', 'Física', '010.664.070-45', NULL, '(51)99394-7445', '', '', '', '', 'Maquine', '1986-05-28', 'Migrado do sistema antigo', 1, '2022-07-20 17:53:52', '2022-07-20 17:53:52'),
(441, 'Adriana Martins', 'Física', '633.593.320-91', NULL, '(51)99918-6868', '', 'Rua 24 de Maio', '750 ap.', 'Centro', 'Osorio', '1970-07-25', 'Migrado do sistema antigo', 1, '2022-07-22 20:53:00', '2022-07-22 20:53:00'),
(442, 'Antonio Nunes', 'Física', '459.233.379-91', NULL, '(51)99677-5773', '', 'Avenida Angelo Gabriel Bofe Quaceli', '100 Ap.', 'Parque Real', 'Osorio', '1962-06-03', 'Migrado do sistema antigo', 1, '2022-07-23 12:52:09', '2022-07-23 12:52:09'),
(443, 'Antônio Orci Marques', 'Física', '576.934.980-87', NULL, '(51)99872-9929', '', 'Costagama', '1652', 'Glória', 'Osório', '1962-04-15', 'Migrado do sistema antigo', 1, '2022-07-25 19:22:01', '2022-07-25 19:22:01'),
(444, 'Marilene da Silva de Bittencourt Lopes', 'Física', '000.290.150-14', NULL, '(51)99701-7690', '', 'Rua Mario Miguel', '80', 'Passinhos', 'Osório', '1982-08-13', 'Migrado do sistema antigo', 1, '2022-07-27 12:56:43', '2022-07-27 12:56:43'),
(445, 'Alexandre Santos Neumann', 'Física', '577.673.270-00', NULL, '(51)99538-0575', '', 'Costagama', '1122', 'Centro', 'Osório', '1971-03-17', 'Migrado do sistema antigo', 1, '2022-07-27 19:49:55', '2022-07-27 19:49:55'),
(446, 'Sinara Bombardi', 'Física', '980.933.800-78', NULL, '(51)99111-9979', '', 'Nelson silveira de Souza', '1165', 'Centro', 'Osório', '1979-04-03', 'Migrado do sistema antigo', 1, '2022-07-27 20:06:46', '2022-07-27 20:06:46');
INSERT INTO `clientes` (`id`, `nome_completo`, `tipo_pessoa`, `documento`, `email`, `telefone_principal`, `telefone_secundario`, `endereco_logradouro`, `endereco_numero`, `endereco_bairro`, `endereco_cidade`, `data_nascimento`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(447, 'Edimar Mansan', 'Física', '662.342.040-15', NULL, '(51)99954-2120', '', 'Rua General Osorio', '1278', 'Centro', 'Maquine', '1969-05-13', 'Migrado do sistema antigo', 1, '2022-07-28 12:28:05', '2022-07-28 12:28:05'),
(448, 'Francisco Cochen Trevisan', 'Física', '028.539.930-60', NULL, '(51)99766-8321', '', 'Cel Reduzido Pacheco', '711', 'Centro', 'Osório', '1997-08-04', 'Migrado do sistema antigo', 1, '2022-08-03 11:48:04', '2022-08-03 11:48:04'),
(449, 'João Luis Machado', 'Física', '471.003.700-00', NULL, '(51)99868-7355', '', 'Rua Major Joãqo Marques', '640/603', 'Centro', 'Osório', '1968-04-30', 'Migrado do sistema antigo', 1, '2022-08-05 16:40:47', '2022-08-05 16:40:47'),
(450, 'Gabriel de Souza Ricardo', 'Física', '049.148.520-47', NULL, '(51)99791-6842', '', 'Rua lagoa do oraço', '', 'Capão da areia', 'Osório', '2001-08-23', 'Migrado do sistema antigo', 1, '2022-08-06 12:16:35', '2022-08-06 12:16:35'),
(451, 'Osório Braga Pacheco', 'Física', '253.440.050-91', NULL, '(51)99467-1782', '(51)994678-323', 'Rua beco do ipe', '501', 'Capão da areia', 'Osório', '1953-03-24', 'Migrado do sistema antigo', 1, '2022-08-06 13:11:53', '2022-08-06 13:11:53'),
(452, 'Daiane Castro', 'Física', '001.330.070-63', NULL, '(51)99971-2729', '', 'Rua da Lagoa', '1111', 'Belvile', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-08-08 14:20:12', '2022-08-08 14:20:12'),
(453, 'Maria Adriana Antunes Martins', 'Física', '962.606.640-04', NULL, '(51)99898-5161', '', 'RUa da Lagoa', '1040', 'Panoramico', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-08-10 12:02:34', '2022-08-10 12:02:34'),
(454, 'Valdeniro Ribieiro da Silva', 'Física', '834.489.420-49', NULL, '(51)99681-2792', '(51)99598-7432', 'Alameda da Serra', '823', 'Interlagos', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-08-10 19:13:06', '2022-08-10 19:13:06'),
(455, 'Beto Saraiva dos Reis', 'Física', '025.124.290-08', NULL, '(51)99550-5738', '', 'Rua 24 de Maio', '808/201', 'Centro', 'Osório', '1992-07-15', 'Migrado do sistema antigo', 1, '2022-08-15 11:26:15', '2022-08-15 11:26:15'),
(456, 'Fabio Alves', 'Física', '031.678.480-06', NULL, '(51)99791-3364', '(51)99722-5284', 'Rua Israel Fernandes da Silva', '213', 'Caravagio', 'Osorio', '1994-04-11', 'Migrado do sistema antigo', 1, '2022-08-15 18:24:20', '2022-08-15 18:24:20'),
(457, 'Vinicius Daniel Souza', 'Física', '004.419.011-58', NULL, '(65)99251-6969', '', 'Rua Independência', '235 ap.', 'Sul Brasileiro', 'Osorio', '1999-11-05', 'Migrado do sistema antigo', 1, '2022-08-15 20:02:04', '2022-08-15 20:02:04'),
(458, 'Joice Rabello', 'Física', '830.821.400-25', NULL, '(51)98166-8054', '(51)99625-2045', 'General Osorio', '576', 'Centro', 'Maquine', '1990-09-17', 'Migrado do sistema antigo', 1, '2022-08-18 18:23:04', '2022-08-18 18:23:04'),
(459, 'Ivo Gomes de Lima', 'Física', '185.457.930-49', NULL, '(51)997055-0016', '', 'Rua Augusto Andrieli', '194', 'Laranjeiras', 'Osório', '1954-02-17', 'Migrado do sistema antigo', 1, '2022-08-26 11:53:49', '2022-08-26 11:53:49'),
(460, 'Odete Lopes Batista', 'Física', '698.826.060-91', NULL, '(51)99708-2077', '', 'Rua Firmino Osorio', '1207 AP', 'Centro', 'Osorio', '1966-06-10', 'Migrado do sistema antigo', 1, '2022-08-29 17:28:03', '2022-08-29 17:28:03'),
(461, 'Heron Rosale Guimarães', 'Física', '035.167.230-33', NULL, '(55)99973-7689', '', 'Marechal Floriano Peixoto', '4', 'Caiu do Céu', 'Osorio', '2000-11-25', 'Migrado do sistema antigo', 1, '2022-08-31 17:41:02', '2022-08-31 17:41:02'),
(462, 'Victoria Barcelos de Souza', 'Física', '037.766.450-24', NULL, '(51)99591-4727', '', 'Rua voluntarios da Patria', '354', 'Lacustre', 'Osorio', '1994-12-19', 'Migrado do sistema antigo', 1, '2022-09-03 13:49:52', '2022-09-03 13:49:52'),
(463, 'Dainer Torres', 'Física', '030.972.159-80', NULL, '(51)99704-3275', '', 'Rua Tramandaí', '780', 'Medianeira', 'Osorio', '1981-10-03', 'Migrado do sistema antigo', 1, '2022-09-05 18:23:10', '2022-09-05 18:23:10'),
(464, 'Eliane da Silva Moreira', 'Física', '935.057.950-20', NULL, '(51)99843-5279', '', 'RSC 101', '4548', 'Passinhos', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-09-06 17:39:40', '2022-09-06 17:39:40'),
(465, 'Luiz Fernando', 'Física', '998.981.2120-6', NULL, '(51)98138-6699', '(51)3663-2524', 'Getulio Vargas', '1379', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-09-08 17:35:02', '2022-09-08 17:35:02'),
(466, 'Luiz Fernando', 'Física', '989.812.120-68', NULL, '(51)98138-6699', '(51)3663-2524', 'Getulio Vargas', '1379', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-09-08 17:35:46', '2022-09-08 17:35:46'),
(467, 'Josiane Oliveira Gomes dos Santos', 'Física', '000.746.460-60', NULL, '(51)99996-5917', '', 'Major Joao Marques', '1103 ap', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2022-09-12 16:36:39', '2022-09-12 16:36:39'),
(468, 'Cleber Jocimar da Silva', 'Física', '623.978.850-34', NULL, '(51)99763-5872', '', 'Rua 16 de Dezembro', '79', 'Centro', 'Osório', '1967-07-30', 'Migrado do sistema antigo', 1, '2022-09-13 13:24:53', '2022-09-13 13:24:53'),
(469, 'Milene Souza da Silva', 'Física', '019.139.870-51', NULL, '(51)99998-4461', '(51)99633-2010', 'Clementino nunes da silva', '323', 'Borrucia', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-09-13 22:05:08', '2022-09-13 22:05:08'),
(470, 'Andresa Silveira Escobar', 'Física', '012.912.300-50', NULL, '(51)99921-4418', '', 'Trav. Cabo Espinosa', '35', 'Albatroz', 'Osório', '1987-10-22', 'Migrado do sistema antigo', 1, '2022-09-14 12:04:06', '2022-09-14 12:04:06'),
(471, 'Tania Maria Soares de Lemos', 'Física', '396.456.730-20', NULL, '(51)99963-4296', '', 'Rua Jose Colares Filhos', '297', '', 'Mostardas', NULL, 'Migrado do sistema antigo', 1, '2022-09-14 20:35:47', '2022-09-14 20:35:47'),
(472, 'Giovanni Mateus Grassi Amigoni', 'Física', '038.961.210-30', NULL, '(51)99714-2497', '(51)99555-0080', 'Estrada Tombadouro', '3150', 'Arroio Grande', 'Osório', '1998-10-07', 'Migrado do sistema antigo', 1, '2022-09-17 12:51:25', '2022-09-17 12:51:25'),
(473, 'Guilherme Crestani', 'Física', '029.638.650-23', NULL, '(51)98540-2705', '', 'Rua 7 de Setembro', '385 Sal', 'Centro', 'Osóorio', '1993-07-29', 'Migrado do sistema antigo', 1, '2022-09-21 14:14:19', '2022-09-21 14:14:19'),
(474, 'Josemar Rosa da Costa', 'Física', '012.857.430-59', NULL, '(51)99882-8087', '', 'Rua padre rels', '393', 'Glória', 'Osório', '1990-01-23', 'Migrado do sistema antigo', 1, '2022-09-22 18:28:25', '2022-09-22 18:28:25'),
(476, 'Fabrian Prestes Brião', 'Física', '029.683.300-20', NULL, '(51)99657-1768', '', 'Av. Fausto Borba Prates', '3536', 'Centro', 'Cidreira', '1992-10-22', 'Migrado do sistema antigo', 1, '2022-09-26 13:59:50', '2022-09-26 13:59:50'),
(477, 'Gilvanio de Souza Rodrigues', 'Física', '505.603.280-87', NULL, '(51)99629-5734', '', 'Rua da Lagoa', '685', 'Farroupilha', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-09-26 16:46:31', '2022-09-26 16:46:31'),
(478, 'Eda Laides Rodrigues dos Santos', 'Física', '990.939.870-20', NULL, '(51)99631-9473', '(51)99853-9070', 'Barão do rio Branco', '940', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2022-10-07 11:45:13', '2022-10-07 11:45:13'),
(479, 'Tuilara Boriviski', 'Física', '623.068.390-34', NULL, '(54)99959-6715', '(54)99267-2866', 'Manoel Marques da Rosa', '1438', 'Sul Brasileiro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-10-10 17:17:11', '2022-10-10 17:17:11'),
(480, 'Sergio Supriano', 'Física', '410.876.740-34', NULL, '(51)99307-1449', '', 'Rua Tirandentes', '401', 'Gloria', 'Osório', '1966-08-30', 'Migrado do sistema antigo', 1, '2022-10-11 19:08:05', '2022-10-11 19:08:05'),
(481, 'Marcia Martins Davila', 'Física', '578.091.940-20', NULL, '(51)99662-4962', '', 'Rua Firmiano Osório', '507', 'Centro', 'Osório', '1967-03-06', 'Migrado do sistema antigo', 1, '2022-10-18 13:19:42', '2022-10-18 13:19:42'),
(482, 'Marcia Costa Andrade', 'Física', '505.909.010-87', NULL, '(51)99639-1589', '', '15 de Novembro', '519/606', 'Centro', 'Osório', '1968-05-09', 'Migrado do sistema antigo', 1, '2022-10-21 11:55:55', '2022-10-21 11:55:55'),
(483, 'Diego Jose Moreira', 'Física', '002.220.500-47', NULL, '(51)99896-4428', '', 'Rua Pinheiro Machado', '1654', 'Gloria', 'Osório', '1984-06-03', 'Migrado do sistema antigo', 1, '2022-10-21 16:55:40', '2022-10-21 16:55:40'),
(484, 'Valdomir Cortinovim', 'Física', '089.099.460-91', NULL, '(51)99983-1725', '', 'Barão do Triunfo', '1155 fu', 'Centro', 'Osório', '1943-03-17', 'Migrado do sistema antigo', 1, '2022-10-22 13:41:17', '2022-10-22 13:41:17'),
(485, 'Girlene Teixeira Nunes', 'Física', '533.972.740-87', NULL, '(51)99760-8552', '', 'Rua Manoel F. Anflor (Viela 1)', '50', 'Laranjeiras', 'Osório', '1966-12-12', 'Migrado do sistema antigo', 1, '2022-10-25 12:25:17', '2022-10-25 12:25:17'),
(486, 'Marvin Henrique Ferri', 'Física', '048.022.770-59', NULL, '(51)99761-9631', '', 'Rua 7 de Setembro,', '483/201', 'Centro', 'Osório', '1999-05-10', 'Migrado do sistema antigo', 1, '2022-10-27 21:04:05', '2022-10-27 21:04:05'),
(487, 'Daniela Brum da Silva', 'Física', '987.505.930-72', NULL, '(51)99919-4979', '', 'Rua Pitngueira', '21', 'Parque Real', 'Osório', '1979-07-26', 'Migrado do sistema antigo', 1, '2022-10-29 14:06:10', '2022-10-29 14:06:10'),
(488, 'Jurandir Vargas Peres', 'Física', '001.220.940-32', NULL, '(51)99596-5836', '(51)99668-7442', 'Rua Jaguarao', '1495', '', 'Maquine', NULL, 'Migrado do sistema antigo', 1, '2022-10-31 17:47:12', '2022-10-31 17:47:12'),
(489, 'Fladimir Moretto', 'Física', '396.452.820-04', NULL, '(33)99161-1637', '(51)99789-3427', 'Rua Angelo Famer', '23', 'Caiu do Ceu', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-11-03 17:36:36', '2022-11-03 17:36:36'),
(490, 'Nair Cecilia Petri Feijó', 'Física', '477.262.299-34', NULL, '(05)199536-7507', '', 'Rua Acelio Pedro Souza', '555', 'Medianeira', 'Osório', '1963-07-25', 'Migrado do sistema antigo', 1, '2022-11-07 12:51:30', '2022-11-07 12:51:30'),
(491, 'Lucas Moutinho', 'Física', '035.674.090-00', NULL, '(51)99669-7074', '', 'Marcelino de Freitas', '297', 'Pitangas', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-11-11 18:39:01', '2022-11-11 18:39:01'),
(492, 'Angela Roulim Stainki', 'Física', '536.007.540-68', NULL, '(51)99956-0623', '(82)98206-0623', 'Professor Vital Barbosa', '574 ap ', 'Ponta Verde', 'Maceio', NULL, 'Migrado do sistema antigo', 1, '2022-11-16 14:29:07', '2022-11-16 14:29:07'),
(493, 'Dennison Padilha Garcia', 'Física', '001.330.220-56', NULL, '51)996727728', '', 'Rua Santos Dumont', '1975', 'Albatroz', '', '1981-02-05', 'Migrado do sistema antigo', 1, '2022-11-22 22:27:01', '2022-11-22 22:27:01'),
(494, 'Janaina Nunes', 'Física', '021.919.580-36', NULL, '(51)99120-9164', '', 'Rua Angelica Diel', '617 cas', 'Umaitá', 'Tramandaí', '1990-06-19', 'Migrado do sistema antigo', 1, '2022-11-24 13:22:41', '2022-11-24 13:22:41'),
(495, 'Camila Reis', 'Física', '003.419.890-37', NULL, '(51)99754-1116', '', 'Alameda dos Platanos', '1120', 'Cod. Parque da Lagoa', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2022-11-30 13:31:07', '2022-11-30 13:31:07'),
(496, 'Larissa pereira Nascimento', 'Física', '041.094.480-77', NULL, '(51)99544-6736', '', 'Rua Garibaldi', '860', 'Gloria', 'Osório', '2000-08-02', 'Migrado do sistema antigo', 1, '2023-01-03 18:18:38', '2023-01-03 18:18:38'),
(497, 'Mario Pacheco Dorneles', 'Física', '003.648.430-04', NULL, '(51)99967-1650', '', 'Rua Laranjeiras', '555', 'Jardim da lagoa', 'Osório', '1933-05-14', 'Migrado do sistema antigo', 1, '2023-01-06 12:53:09', '2023-01-06 12:53:09'),
(498, 'Lorrane Fagundes (Escritorio Marques)', 'Física', '043.340.450-73', NULL, '(51)98472-9234', '', 'Jorge Dariva', '', 'Centro', 'Osório', '2004-06-08', 'Migrado do sistema antigo', 1, '2023-01-09 12:05:20', '2023-01-09 12:05:20'),
(499, 'Valtair paulo da Silva', 'Física', '563.966.190-91', NULL, '(53)99707-9256', '', 'Rua Marques do Herval', '225 APT', 'Caravagio', 'Osório', '1969-10-15', 'Migrado do sistema antigo', 1, '2023-01-09 15:00:33', '2023-01-09 15:00:33'),
(500, 'Susana Terezinha Ferreira de Oliveira', 'Física', '615.494.790-91', NULL, '(51)99616-0233', '', 'Rua São Conrado', '1243', 'Atlantida Sul', 'Osório', '1964-02-02', 'Migrado do sistema antigo', 1, '2023-01-11 12:09:29', '2023-01-11 12:09:29'),
(501, 'Eduardo da Silva Alves', 'Física', '055.612.710-24', NULL, '(51)99932-9583', '(51)98411-4879', 'Rua capão da Canoa', '851', 'Capão da Canoa', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-01-12 12:13:59', '2023-01-12 12:13:59'),
(502, 'Anderson Nostrani da Silva', 'Física', '021.745.860-29', NULL, '(51)99627-6394', '', 'Trav Cristino Nunes', '137', 'Parque do Sol', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-01-19 16:51:08', '2023-01-19 16:51:08'),
(503, 'Luis Antonio Benetti Boff', 'Física', '034.402.410-50', NULL, '(51)98062-9976', '', 'Linha Cerrito', '1780', 'Barra do Ouro', 'Maquiné', '2002-07-30', 'Migrado do sistema antigo', 1, '2023-01-19 18:05:07', '2023-01-19 18:05:07'),
(504, 'Rosilene Kirsch Veronez', 'Física', '533.084.920-91', NULL, '', '', 'Rua Lafaite Pereira dos Santos', '212', 'Caravagio', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-01-25 20:02:54', '2023-01-25 20:02:54'),
(505, 'Stefani Rodrigues', 'Física', '045.058.970-60', NULL, '(51)99765-3156', '', 'Rua Trav. Formagio', '278', 'Farroupilha', 'Osório', '2003-05-03', 'Migrado do sistema antigo', 1, '2023-01-27 13:38:24', '2023-01-27 13:38:24'),
(506, 'Luis Felipe Portal Soares', 'Física', '029.216.280-40', NULL, '(51)99818-2415', '', 'Travessa Sub Estação Osorio 2', '110', '', '', NULL, 'Migrado do sistema antigo', 1, '2023-02-02 18:26:57', '2023-02-02 18:26:57'),
(507, 'Marcia dos Santos Nunes', 'Física', '802.016.360-34', NULL, '51980274169', '51995269410', 'Estrada Ventos do Sul', '1080', 'Serramar', 'Osório', '1979-05-03', 'Migrado do sistema antigo', 1, '2023-02-07 13:07:59', '2023-02-07 13:07:59'),
(508, 'Josiane Barbosa', 'Física', '995.235.820-20', NULL, '(51)99628-2950', '', 'Rua da Alamandas', '75', 'Vila da Serra', 'Osorio', '1979-12-15', 'Migrado do sistema antigo', 1, '2023-02-09 17:29:54', '2023-02-09 17:29:54'),
(509, 'Julia Silveira De Mattos', 'Física', '034.253.260-74', NULL, '(51)99930-4284', '(51)9665-0605', 'Rua 24 de Maio', '268', 'Centro', 'Osorio', '2005-12-05', 'Migrado do sistema antigo', 1, '2023-02-13 20:19:22', '2023-02-13 20:19:22'),
(510, 'Gabriel Mansan de Mattos', 'Física', '010.134.490-29', NULL, '(51)98126-5586', '', 'Rua Conego Pedro Jacobs', '436', 'Caravagio', 'Osório', '1990-03-13', 'Migrado do sistema antigo', 1, '2023-02-14 13:55:40', '2023-02-14 13:55:40'),
(511, 'Joarez Neres Santiago', 'Física', '126.050.888-98', NULL, '(51)98034-4458', '', 'Rua Barão do Triunfo', '1525', 'Centro', 'Osório', '1969-08-13', 'Migrado do sistema antigo', 1, '2023-02-16 17:56:36', '2023-02-16 17:56:36'),
(512, 'Airton da Silveira Lopes', 'Física', '241.716.700-04', NULL, '(51)99762-6974', '', 'Rua João Manoel Kiles', '140', 'Laranjeiras', 'Osório', '1956-10-08', 'Migrado do sistema antigo', 1, '2023-02-27 10:55:40', '2023-02-27 10:55:40'),
(513, 'Cassiana Silveira', 'Física', '957.234.390-49', NULL, '(51)98143-5047', '', 'Av Luis Silveira', '1018', 'Centro', 'Palmares do Sul', '1975-01-24', 'Migrado do sistema antigo', 1, '2023-02-28 20:18:47', '2023-02-28 20:18:47'),
(514, 'Claires Rejane Rebeiro Pereira', 'Física', '221.464.740-68', NULL, '(51)99908-8157', '(51)99681-2792', 'Alameda da serra', '823', 'Interlagos', 'Osório', '1954-06-26', 'Migrado do sistema antigo', 1, '2023-03-02 13:00:30', '2023-03-02 13:00:30'),
(515, 'Angela Rosa Xavier', 'Física', '254.858.900-59', NULL, '(51)99787-1084', '', 'Av jorge Davariva', '2643', 'Osorio', 'Parque Real', NULL, 'Migrado do sistema antigo', 1, '2023-03-13 11:46:54', '2023-03-13 11:46:54'),
(516, 'Cesar Santos de Souza', 'Física', '030.027.550-10', NULL, '(51)99908-0276', '', 'Estrada Palmital', '14790', 'Palmital', 'osorio', '1992-01-25', 'Migrado do sistema antigo', 1, '2023-03-13 20:59:38', '2023-03-13 20:59:38'),
(517, 'Neli Terezinha da Silva', 'Física', '718.298.650-72', NULL, '(51)99844-9011', '', 'Rainha Ringa Maria Tereza', '158', 'Caravagio', 'Osorio', '1959-03-28', 'Migrado do sistema antigo', 1, '2023-03-14 12:41:11', '2023-03-14 12:41:11'),
(518, 'Sadi Junior', 'Física', '033.351.750-42', NULL, '(51)998183-3240', '', 'Rua 15 de Novembro', '1770', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-03-14 17:21:56', '2023-03-14 17:21:56'),
(519, 'Samuel da Silva Lucas', 'Física', '055.139.570-27', NULL, '(51)99860-7462', '', 'Rua 10 de Novembro', '253', 'Porto Lacustre', 'osorio', '2003-08-29', 'Migrado do sistema antigo', 1, '2023-03-14 18:29:54', '2023-03-14 18:29:54'),
(520, 'Vitoria Bersag Colombo', 'Física', '060.636.060-33', NULL, '(51)98029-0166', '(51)99686-5560', 'Str Geral da Borrucia', '', 'Borrucia', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-03-17 12:54:10', '2023-03-17 12:54:10'),
(521, 'Ana Paula Santellano de Oliveira', 'Física', '988.280.370-91', NULL, '(51)98128-6690', '', 'Rua Independencia, Apto 104 Bloco C', '235', 'Glória', 'Osorio', '1981-04-12', 'Migrado do sistema antigo', 1, '2023-03-21 18:12:54', '2023-03-21 18:12:54'),
(522, 'Eduardo Alves Bobsin', 'Física', '051.194.880-80', NULL, '(51)99525-1528', '', 'Linha Cachoeira', '3100', 'Centro', 'Maquine', '1998-06-23', 'Migrado do sistema antigo', 1, '2023-03-24 12:13:17', '2023-03-24 12:13:17'),
(523, 'Gabriela Rosa dos Santos', 'Física', '485.793.588-09', NULL, '(51)99718-6093', '(51)99866-7247', 'Av Mario Santo Dani', '1277', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-03-27 20:32:26', '2023-03-27 20:32:26'),
(524, 'Rosangela Pereira de macedo', 'Física', '707.503.510-04', NULL, '(51)99366-8860', '', 'Av getulio vargas 1102', 'ap 403', 'centro', 'osorio', '1968-07-11', 'Migrado do sistema antigo', 1, '2023-03-28 16:14:13', '2023-03-28 16:14:13'),
(525, 'Nelson Thalles Santos Lima', 'Física', '119.154.904-64', NULL, '(51)98129-6144', '', 'Estrada Mar Km 9', '5559', 'Varzea do padre', 'Osório', '2003-07-03', 'Migrado do sistema antigo', 1, '2023-04-01 14:56:11', '2023-04-01 14:56:11'),
(526, 'Paulo Gustavo Ribeiro', 'Física', '335.328.000-63', NULL, '(51)99948-0601', '', 'Rua martins gonçalves', '38', '', 'Mostardas', '1960-02-17', 'Migrado do sistema antigo', 1, '2023-04-03 13:10:14', '2023-04-03 13:10:14'),
(527, 'Vinicius Garcia', 'Física', '004.199.320-97', NULL, '(51)98541-9813', '', 'Rua Firmiano Osório', '480 ap ', 'Caiu do céu', 'Osório', '1984-08-16', 'Migrado do sistema antigo', 1, '2023-04-13 20:45:19', '2023-04-13 20:45:19'),
(528, 'Eder Gilson Freitas de Sousa', 'Física', '524.107.650-68', NULL, '(51)98544-4528', '', 'Rua Edite de lurdes', '798', 'Santa Teresinha', 'Imbe', NULL, 'Migrado do sistema antigo', 1, '2023-04-14 17:55:59', '2023-04-14 17:55:59'),
(529, 'Luis Fernando Willrich', 'Física', '160.465.790-15', NULL, '(51)98128-9252', '(51)98148-5374', '24 de Maio', '750 ap ', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-04-17 18:17:43', '2023-04-17 18:17:43'),
(530, 'Cristian Douglas Fernandes', 'Física', '007.073.860-20', NULL, '(51)98055-7768', '', 'Rua dos Busios', '608', 'Atlântida sul', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-04-18 13:45:24', '2023-04-18 13:45:24'),
(531, 'Aline de Oliveira da Conceição Cardoso', 'Física', '001.266.100-46', NULL, '(51)99628-4626', '(51)98151-8163', 'Rua candido osorio da rosa', '467', 'Caiu do Ceu', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-04-18 17:27:51', '2023-04-18 17:27:51'),
(532, 'Isalon Medeiros da Silva', 'Física', '486.156.500-63', NULL, '(51)98124-8000', '', 'Julho de Castilhos', '1082', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-04-19 20:19:57', '2023-04-19 20:19:57'),
(533, 'Valmi Fernandes', 'Física', '571.446.800-72', NULL, '51 98411-0391', '', 'Rua Alzira Sarcony', '84', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-04-24 14:20:05', '2023-04-24 14:20:05'),
(534, 'Aline Vitoria Garcia', 'Física', '977.541.610-87', NULL, '(51)981977-381', '', 'Rua Firmiano osório residencial veneza 17', '1195', 'Centro', 'osorio', '1980-08-08', 'Migrado do sistema antigo', 1, '2023-04-24 18:30:32', '2023-04-24 18:30:32'),
(535, 'Gustavo da Silveira Gomes', 'Física', '039.647.340-71', NULL, '(51)98348-9263', '', 'Rua Salgado Filho', '181', 'Albatroz', 'Osório', '2002-01-16', 'Migrado do sistema antigo', 1, '2023-04-26 22:46:06', '2023-04-26 22:46:06'),
(536, 'João Vitor Kalata', 'Física', '053.639.160-29', NULL, '(51)99275-3151', '(51)98117-2685', 'Rua Estrela', '655', 'Mariluz', 'Imbé', NULL, 'Migrado do sistema antigo', 1, '2023-04-27 12:37:25', '2023-04-27 12:37:25'),
(537, 'Alessandra Teotonio de Morais', 'Física', '028.763.650-06', NULL, '(51)98050-1707', '', 'Estrada da produção', '750', 'Figueira Grande', 'osorio', '1993-03-03', 'Migrado do sistema antigo', 1, '2023-04-28 17:53:36', '2023-04-28 17:53:36'),
(538, 'Augusto Fabio Aliardi da Cunha', 'Física', '033.173.620-90', NULL, '(51)98353-7733', '', 'Rua Professor Romildo Bolsan', '310', 'Encosta da Serra', 'Osório', '1994-08-30', 'Migrado do sistema antigo', 1, '2023-05-02 20:26:38', '2023-05-02 20:26:38'),
(539, 'Djalma Ribeiro', 'Física', '914.938.110-53', NULL, '(51)98170-1479', '', 'Borges de medeiros', '26', 'Albatroz', 'Osório', '1974-09-09', 'Migrado do sistema antigo', 1, '2023-05-03 17:12:33', '2023-05-03 17:12:33'),
(540, 'Carina Ferri Fonini', 'Física', '012.232.120-09', NULL, '(51)99866-6342', '', 'Estrada do Mar Km 8', '5010', 'Varzea do Padre', 'Osório', '1985-02-19', 'Migrado do sistema antigo', 1, '2023-05-08 11:25:34', '2023-05-08 11:25:34'),
(541, 'Larissa Costa', 'Física', '033.780.620-96', NULL, '(51)99669-8220', '', 'Pinheiro Machado', '507 Ap ', 'Sulbrasileiro', 'Osório', '1996-09-15', 'Migrado do sistema antigo', 1, '2023-05-08 18:03:11', '2023-05-08 18:03:11'),
(542, 'Adriana Santos da Silva', 'Física', '959.324.530-87', NULL, '(51)99579-3992', '', 'Rondonia', '634', 'Nova Tramandai', 'Osório', '1978-06-26', 'Migrado do sistema antigo', 1, '2023-05-10 20:16:26', '2023-05-10 20:16:26'),
(543, 'Waller', 'Física', '475.086.550-87', NULL, '(51)99987-3863', '', '', '', '', '', NULL, 'Migrado do sistema antigo', 1, '2023-05-12 13:56:22', '2023-05-12 13:56:22'),
(544, 'Diogo Sapaio Santana', 'Física', '001.199.020-10', NULL, '(48)98818-8522', '', 'Rua 15 de Novembro', '519 ap ', 'Centro', 'Osoiro', NULL, 'Migrado do sistema antigo', 1, '2023-05-15 19:39:31', '2023-05-15 19:39:31'),
(545, 'Larissa de Souza Jesus', 'Física', '953.124.630-00', NULL, '(51)99958-0303', '(51)98578-3908', 'Av Sao Jose', '1891', '', 'Palmares do Sul', NULL, 'Migrado do sistema antigo', 1, '2023-05-19 16:45:06', '2023-05-19 16:45:06'),
(546, 'Klaiton Colombo Formaggio', 'Física', '047.534.390-54', NULL, '+55 51 9274-226', '', 'Rua Major João Marques', '2450', 'Albatroz', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-05-23 16:52:14', '2023-05-23 16:52:14'),
(547, 'Wolni de Jesus Gorziza', 'Física', '152.301.630-20', NULL, '51 98191-8645', '', 'Rua Farrapos', '685/305', 'Sulbrasileiro', 'osório', '1945-03-22', 'Migrado do sistema antigo', 1, '2023-05-23 16:56:26', '2023-05-23 16:56:26'),
(548, 'Gustavo Rodrigues da Silva', 'Física', '040.903.590-40', NULL, '(51)99850-6233', '', 'BR 101 Km 82', '5287', 'Arroio da Pedras', 'Osório', '2004-04-27', 'Migrado do sistema antigo', 1, '2023-05-25 21:09:03', '2023-05-25 21:09:03'),
(549, 'Jhone Ribeiro Fernandes', 'Física', '064.336.479-08', NULL, '(99)99120-6574', '', 'Rua 15 de Novembro, Apto. 101', '1119', 'Glória', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-05-26 19:14:52', '2023-05-26 19:14:52'),
(550, 'Anelia da Silva Walmrath', 'Física', '392.184.730-34', NULL, '(51)99357-9745', '', 'Rua Farrapos', '685/305', 'Sulbrasileiro', 'Osório', '1949-09-10', 'Migrado do sistema antigo', 1, '2023-05-29 18:44:36', '2023-05-29 18:44:36'),
(551, 'Diuare Nogueira Gamba', 'Física', '043.554.930-83', NULL, '(51)98147-7651', '', 'Rua Torres', '564', 'Medianeira', 'Osório', '2002-01-08', 'Migrado do sistema antigo', 1, '2023-05-31 12:11:56', '2023-05-31 12:11:56'),
(552, 'Elisete Mota da Silva', 'Física', '034.569.750-23', NULL, '(51)99874-6810', '', 'Voluntarios da Patria', '52', 'Porto', 'Osório', '1998-07-25', 'Migrado do sistema antigo', 1, '2023-05-31 20:28:35', '2023-05-31 20:28:35'),
(553, 'Vangelica Coser', 'Física', '015.174.800-43', NULL, '(51)99512-2210', '', 'Trav. Formage', '97', 'Porto Lacustre', 'Osorio', '1984-07-02', 'Migrado do sistema antigo', 1, '2023-06-02 20:45:05', '2023-06-02 20:45:05'),
(554, 'Maria Angelica Schieck', 'Física', '452.106.320-91', NULL, '(51)99984-9108', '', 'Prof. Alvita Alves de Oliveira', '107', 'Parque do Sol', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-06-05 14:05:55', '2023-06-05 14:05:55'),
(555, 'Itauana do Amaral Fernandes', 'Física', '012.813.940-41', NULL, '(51)99738-5131', '', 'Estrada Emboaba', '7200', 'Zona Rural', 'Osório', '1994-01-18', 'Migrado do sistema antigo', 1, '2023-06-05 16:12:30', '2023-06-05 16:12:30'),
(556, 'Maike Dos Anjos Martins', 'Física', '041.081.100-99', NULL, '(51)99555-3859', '(51)98027-7981', 'Marcilio dias', '893', 'Medianeira', 'Osório', '1996-08-03', 'Migrado do sistema antigo', 1, '2023-06-05 16:40:05', '2023-06-05 16:40:05'),
(557, 'George Lucas', 'Física', '026.405.750-31', NULL, '(51)99315-2959', '', 'Rua da Lagoa (Circulação 14 Casa 80)', '1111', 'Bellville', 'Osório', '2006-08-02', 'Migrado do sistema antigo', 1, '2023-06-06 11:52:28', '2023-06-06 11:52:28'),
(558, 'Selivio Bobsin', 'Física', '000.385.650-78', NULL, '', '', 'Rua Candido Osório da Rosa', '176', 'Caiu do Ceu', 'Osório', '1966-06-15', 'Migrado do sistema antigo', 1, '2023-06-06 13:22:39', '2023-06-06 13:22:39'),
(559, 'Driely Azevedo Gomes', 'Física', '000.632.450-12', NULL, '(51)99626-9963', '', 'BR 101', '', 'Costa Verde', 'Osório', '2003-07-17', 'Migrado do sistema antigo', 1, '2023-06-06 16:01:39', '2023-06-06 16:01:39'),
(560, 'Mauricio Franco', 'Física', '045.076.690-06', NULL, '(51)98182-2188', '', 'Paul Percy Harris', '77', 'Centro', 'Osório', '2000-05-18', 'Migrado do sistema antigo', 1, '2023-06-06 16:47:45', '2023-06-06 16:47:45'),
(561, 'Natieli Matos Pereira', 'Física', '852.339.510-53', NULL, '(51)99553-4635', '', 'Estrada da Produção', '850', 'Figueira Grande', 'Osório', '1999-12-10', 'Migrado do sistema antigo', 1, '2023-06-09 18:29:37', '2023-06-09 18:29:37'),
(562, 'Mt Implementos Rodoviarios', 'Física', '108.503.25001-', NULL, '(51)99903-2717', '(51)98585-0150', 'BR101 km 96', '3362', '', 'Osoiro', NULL, 'Migrado do sistema antigo', 1, '2023-06-12 12:12:35', '2023-06-12 12:12:35'),
(563, 'Wellington Christia Martins da Rosa', 'Física', '095.266.899-84', NULL, '(51)98478-3826', '', 'Estrada lagoado oraço', '2655', '', 'Osório', '1993-03-01', 'Migrado do sistema antigo', 1, '2023-06-12 12:33:39', '2023-06-12 12:33:39'),
(564, 'Catiane Da Silva Silveira', 'Física', '002.208.010-45', NULL, '(55)99955-4948', '', 'Rua Firmiano Osorio', '31', 'Caiu do Ceu', 'Osorio', '1980-11-21', 'Migrado do sistema antigo', 1, '2023-06-12 20:07:49', '2023-06-12 20:07:49'),
(565, 'Tamara Borba', 'Física', '012.637.620-42', NULL, '(51)99731-9676', '', 'Rua Almirante Tamandare', '473', 'Albatroz', 'Osório', '1992-12-19', 'Migrado do sistema antigo', 1, '2023-06-14 21:21:25', '2023-06-14 21:21:25'),
(566, 'Paulo Zwick', 'Física', '001.758.490-38', NULL, '', '', '', '', '', '', '1983-12-21', 'Migrado do sistema antigo', 1, '2023-06-26 13:54:36', '2023-06-26 13:54:36'),
(567, 'Toribio Gubert', 'Física', '004.085.750-30', NULL, '(51)99998-3945', '', 'Manoel Marques da Rosa', '764', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2023-06-28 14:35:14', '2023-06-28 14:35:14'),
(568, 'Rafael dos Santos Muniz', 'Física', '021.485.130-36', NULL, '(51)9871-7715', '(51)99575-9534', 'Rua Passinhos', '144', '', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-07-07 18:17:00', '2023-07-07 18:17:00'),
(569, 'Estefani Evangeli Miler', 'Física', '037.553.680-99', NULL, '(51)99562-4850', '(51)99726-9990', 'Rua Roma Esquina Mazonas', '19', '', 'Tramandai Sul', '2004-06-18', 'Migrado do sistema antigo', 1, '2023-07-14 19:48:13', '2023-07-14 19:48:13'),
(570, 'Augustus Gangary Daniel da Silva', 'Física', '038.041.570-41', NULL, '(51)98112-2199', '', 'Rua 15 de Novembro', '1577', 'Gloria', 'Osório', '2007-07-01', 'Migrado do sistema antigo', 1, '2023-07-17 13:30:34', '2023-07-17 13:30:34'),
(571, 'Adriana Conceição Gonçalves Coelho', 'Física', '007.548.050-86', NULL, '(51)99973-0988', '', 'Rua 15 de Novembro, Apto 507', '519', 'Centro', 'Osório', '1987-12-08', 'Migrado do sistema antigo', 1, '2023-07-17 20:07:16', '2023-07-17 20:07:16'),
(572, 'Sidnei Pelissoli Junior', 'Física', '026.115.840-60', NULL, '(51)98218-5877', '', 'Rua 7 de Setembro', '1331', 'Gloria', 'Osório', '1991-02-03', 'Migrado do sistema antigo', 1, '2023-07-17 20:31:44', '2023-07-17 20:31:44'),
(573, 'Leon Bittencourt', 'Física', '024.783.440-84', NULL, '(51)98031-8868', '', 'Rua Getúlio Vargas, apto 204', '930', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-07-19 18:52:27', '2023-07-19 18:52:27'),
(574, 'Tiago Amaral da Silva', 'Física', '711.205.096-3', NULL, '(51)99544-8234', '', '', '', '', '', '1993-12-26', 'Migrado do sistema antigo', 1, '2023-07-22 13:27:30', '2023-07-22 13:27:30'),
(575, 'Lucia Rodrigues Fagundes', 'Física', '432.789.730-20', NULL, '(51)99336-8747', '', 'Rua Alegrete', '2123', 'Centro', 'Imbé', '1965-03-22', 'Migrado do sistema antigo', 1, '2023-07-26 14:44:44', '2023-07-26 14:44:44'),
(576, 'Marlon Gilmar Soares', 'Física', '300.919.930-91', NULL, '(51)99690-7637', '', 'RS 030, km 74', '4257', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-07-27 12:57:23', '2023-07-27 12:57:23'),
(577, 'Melaine Tresbach', 'Física', '016.981.710-56', NULL, '(51)99539-8679', '', 'Rua Presidente Castelo Branco', '178', 'Caravagio', 'Osório', '1986-09-30', 'Migrado do sistema antigo', 1, '2023-08-01 11:11:33', '2023-08-01 11:11:33'),
(578, 'Jonathan Lin Jun Fan Echamende Pinto', 'Física', '037.491.750-71', NULL, '(51)99852-9275', '', 'Estrada Constante da Silva Acesso Tombadouro', '885', 'Bela Vista', 'Osório', '1992-06-26', 'Migrado do sistema antigo', 1, '2023-08-02 11:16:07', '2023-08-02 11:16:07'),
(579, 'Patricia Miguel', 'Física', '984.936.470-94', NULL, '(51)9818-1663', '', 'Travessa Macanudo', '103', '', 'Osório', '1981-08-09', 'Migrado do sistema antigo', 1, '2023-08-03 11:14:20', '2023-08-03 11:14:20'),
(580, 'Tarcisio Teixeira', 'Física', '001.217.020-81', NULL, '(51)99623-1526', '', 'Rua Manoel José da Silva', '53', 'Engenho', 'Osório', '1981-08-02', 'Migrado do sistema antigo', 1, '2023-08-03 17:20:55', '2023-08-03 17:20:55'),
(581, 'pedro henrique machado dos santos', 'Física', '038.236.720-02', NULL, '(51)99366-2958', '', 'Rua Constituição', '574', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-08-07 12:01:44', '2023-08-07 12:01:44'),
(582, 'Yuri Sousa Colares', 'Física', '011.247.522-18', NULL, '(51)99755-9452', '', 'Rua Independencia', '411', 'Gloria', 'Osório', '1996-09-04', 'Migrado do sistema antigo', 1, '2023-08-08 13:26:21', '2023-08-08 13:26:21'),
(583, 'Gustavo Oliveira Oliveira', 'Física', '033.536.060-25', NULL, '(51)98036-5524', '(51)3601-0050', 'Lateral BR 101 Km 97', '797', 'Centro', 'Osório', '1997-03-25', 'Migrado do sistema antigo', 1, '2023-08-10 13:03:53', '2023-08-10 13:03:53'),
(584, 'Anai Menezes Machado', 'Física', '319.964.930-00', NULL, '(51)98505-4039', '', 'Rua 24 de Maio', '708/201', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-08-12 11:30:29', '2023-08-12 11:30:29'),
(585, 'Jorge Edu de Freitas Fagundes', 'Física', '555.603.340-49', NULL, '(51)99699-4995', '', 'Rua Buzios', '636', 'Atlântida Sul', 'Osório', '1971-10-04', 'Migrado do sistema antigo', 1, '2023-08-14 16:43:24', '2023-08-14 16:43:24'),
(586, 'Ana Maria Souza Calderão', 'Física', '682.584.880-15', NULL, '(51)99933-0601', '', 'Rua Jose Antonio Erick', '428', 'Pinhal', 'Osório', '1951-11-05', 'Migrado do sistema antigo', 1, '2023-08-14 19:13:54', '2023-08-14 19:13:54'),
(587, 'Bernardo Pacheco', 'Física', '027.824.270-75', NULL, '(51)98133-1457', '', 'Av. dos Pinheiros', '135', 'Interlagos', 'Osório', '2002-12-30', 'Migrado do sistema antigo', 1, '2023-08-16 13:05:02', '2023-08-16 13:05:02'),
(588, 'Ivoni Olinda dos Santos', 'Física', '644.406.050-87', NULL, '(51)99961-1823', '', 'Rua Sepe', '2936/10', 'Navegantes', 'Capão da Canoa', '1949-12-22', 'Migrado do sistema antigo', 1, '2023-08-22 13:52:23', '2023-08-22 13:52:23'),
(589, 'Luciana I. Decken', 'Física', '992.405.640-04', NULL, '(51)99195-8470', '', 'Rua Ventos do Sul', '37', 'Serramar', 'Osório', '1980-06-07', 'Migrado do sistema antigo', 1, '2023-08-22 20:13:55', '2023-08-22 20:13:55'),
(590, 'Renata Santos', 'Física', '730.158.220-04', NULL, '(51)98139-4833', '', 'Major João Marques', '729/sal', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-08-23 13:45:53', '2023-08-23 13:45:53'),
(591, 'Giancarlo Dalpiaz de Oliveira', 'Física', '016.764.201-33', NULL, '(51)99629-9197', '', 'Rua 7 de setembro, apto 302', '483', 'Centro', 'Osório', '1988-09-23', 'Migrado do sistema antigo', 1, '2023-08-26 13:43:58', '2023-08-26 13:43:58'),
(592, 'Fabiano Rodrigues Vargas', 'Física', '026.166.560-00', NULL, '(51)98070-6069', '', 'Tolentino Gonçalves correa', '833', 'Primavera', 'Osório', '1990-08-14', 'Migrado do sistema antigo', 1, '2023-08-28 12:34:26', '2023-08-28 12:34:26'),
(593, 'Ederson Machado de Oliveira', 'Física', '933.597.130-87', NULL, '(51)99916-7811', '', 'Rua Firmiano Osório', '1630', 'Centro', 'Osório', '1977-05-06', 'Migrado do sistema antigo', 1, '2023-08-28 17:48:29', '2023-08-28 17:48:29'),
(594, 'Felipe Rocha de Souza', 'Física', '023.167.380-95', NULL, '(51)99818-8036', '', 'Rodovia Domingo Manoel Pires', '13474', 'Palmital', 'Osório', '1991-03-16', 'Migrado do sistema antigo', 1, '2023-08-29 20:44:25', '2023-08-29 20:44:25'),
(595, 'Vilde Ourique Martins', 'Física', '288.080.450-72', NULL, '(51)99983-7902', '', 'Manoel Marques da Rosa', '281', 'Centro', 'Osório', '1953-05-04', 'Migrado do sistema antigo', 1, '2023-08-31 11:09:52', '2023-08-31 11:09:52'),
(596, 'Carol Recepção', 'Física', '123.456.789-11', NULL, '', '', 'Lima & Lucas', '', '', 'Osório', '0000-00-00', 'Migrado do sistema antigo', 1, '2023-09-02 13:57:36', '2023-09-02 13:57:36'),
(597, 'Thais da Silva de Moura', 'Física', '043.202.3801-0', NULL, '(51)99610-6427', '', 'Travessa da Formiga', '112', 'Entrocamento', 'Osório', '2000-12-16', 'Migrado do sistema antigo', 1, '2023-09-06 16:36:41', '2023-09-06 16:36:41'),
(598, 'Marcio de Souza', 'Física', '624.178.439-00', NULL, '(48)99127-6909', '', 'Rua tio dudu', '533', 'Centro', 'Capivari', '1969-10-28', 'Migrado do sistema antigo', 1, '2023-09-08 13:54:59', '2023-09-08 13:54:59'),
(599, 'Zirlau Andreoli dos Santos', 'Física', '165.453.890-68', NULL, '(51)99847-1540', '(51)9924-3713', 'RS30', '934', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-09-15 13:38:36', '2023-09-15 13:38:36'),
(600, 'Norton Kesterig Ferreira Comércio Madeiras', 'Física', '564.654.84', NULL, '(51)3663-2432', '', 'Estrada da Perua', '18', 'Parque Real', 'Osório', '0000-00-00', 'Migrado do sistema antigo', 1, '2023-09-27 17:03:20', '2023-09-27 17:03:20'),
(601, 'Thiago Lampert Godin', 'Física', '023.369.6580-0', NULL, '(51)99365-8265', '', 'Rua tolentino', '1109', 'Caravagio', 'Osório', '2000-06-14', 'Migrado do sistema antigo', 1, '2023-10-04 16:16:48', '2023-10-04 16:16:48'),
(602, 'Thiago Lampert Godinho', 'Física', '033.696.580-03', NULL, '(51)99365-8265', '', 'Rua Tolentino Gonçalves Correira', '1109', 'Caravagio', 'Osório', '2000-07-14', 'Migrado do sistema antigo', 1, '2023-10-04 16:19:25', '2023-10-04 16:19:25'),
(603, 'Elton Muniz da Rocha', 'Física', '997.923.380-04', NULL, '(51)99993-7019', '', 'Rua Cláudio Henrique Albert', '100', 'Bosque de Albatroz', 'Osório', '1983-03-03', 'Migrado do sistema antigo', 1, '2023-10-04 19:54:28', '2023-10-04 19:54:28'),
(604, 'William Brun', 'Física', '003.222.660-84', NULL, '(51)98145-0758', '', 'Rua 15 de novembro', '512', 'Centro', 'Osório', '1985-10-31', 'Migrado do sistema antigo', 1, '2023-10-06 11:45:06', '2023-10-06 11:45:06'),
(605, 'Luiz Carlos Limberger', 'Física', '683.681.860-75', NULL, '(51)99702-8163', '', 'Tenente Penha', '1009', 'Centro', 'Balneario Pinhal', '1969-06-17', 'Migrado do sistema antigo', 1, '2023-10-06 12:58:39', '2023-10-06 12:58:39'),
(606, 'Vanessa Luziana de Melo Borba', 'Física', '026.112.890-69', NULL, '(51)99529-5694', '', 'Av. Osvaldo Passinhos', '87', 'Granja Getulio Vargas', 'Palmares', NULL, 'Migrado do sistema antigo', 1, '2023-10-06 16:50:25', '2023-10-06 16:50:25'),
(607, 'Thiago Augusto dos Santos', 'Física', '007.350.200-68', NULL, '(51)99556-9254', '', 'Av. Saquarema', '1306', 'Atlantida Sul', 'Osório', '1984-09-21', 'Migrado do sistema antigo', 1, '2023-10-09 12:11:33', '2023-10-09 12:11:33'),
(608, 'Vanezio Justin', 'Física', '014.590.680-92', NULL, '(51)99679-8615', '', 'Rua Tenente Mogar', '106', 'Parque do Sol', 'Osório', '1986-04-01', 'Migrado do sistema antigo', 1, '2023-10-10 13:30:45', '2023-10-10 13:30:45'),
(609, 'Marcelo Katunaric Correia', 'Física', '662.338.870-20', NULL, '(51)99910-1530', '', 'Rua major João Marques', '590 sal', '', 'Osoório', '1990-07-09', 'Migrado do sistema antigo', 1, '2023-10-11 11:29:18', '2023-10-11 11:29:18'),
(610, 'Mariane dos Santos Pereira', 'Física', '026.300.180-67', NULL, '(51)99957-0564', '', 'Rua Rincão dos Aguapés', '90', 'Aguapés', 'Osório', '1991-07-07', 'Migrado do sistema antigo', 1, '2023-10-11 19:54:25', '2023-10-11 19:54:25'),
(611, 'Maria Goreti Lessa de Oliveira', 'Física', '412.065.920-87', NULL, '(51)99855-3110', '', 'Rua Boa Ventura de Souza', '637', 'Caravagio', 'Osório', '1965-01-03', 'Migrado do sistema antigo', 1, '2023-10-13 12:30:45', '2023-10-13 12:30:45'),
(612, 'Mauricio Marino Fagundes', 'Física', '569.252.200-49', NULL, '(51)99843-5946', '', 'RS 030 km79', '845', 'Laranjeiras', 'Osório', '1970-06-20', 'Migrado do sistema antigo', 1, '2023-10-16 11:22:45', '2023-10-16 11:22:45'),
(613, 'Ariel Lopes da Silveira', 'Física', '042.538.570-19', NULL, '(51)99720-6440', '', 'Idefonso Simão Lopes', '1548', 'Sul Brasileiro', 'Osório', '1998-02-11', 'Migrado do sistema antigo', 1, '2023-10-19 14:01:34', '2023-10-19 14:01:34'),
(614, 'Giovanna Domingues', 'Física', '033.810.920-06', NULL, '(53)99158-0110', '', 'Rua Independencia', '160/01', 'Sulbrasileiro', 'Osório', '1993-03-06', 'Migrado do sistema antigo', 1, '2023-10-19 19:51:10', '2023-10-19 19:51:10'),
(615, 'Gustavo Inacio de Farias', 'Física', '026.126.950-01', NULL, '(51)99528-2890', '', 'Julio de Castilhos', '1290/70', 'Centro', 'Osório', '1995-10-10', 'Migrado do sistema antigo', 1, '2023-10-20 12:11:50', '2023-10-20 12:11:50'),
(616, 'Tairone Weyh Silva (Marco Antonio Melo de Morais)', 'Física', '854.699.800-06', NULL, '(51)99658-9302', '', 'Getulio Vargas', '1450', 'Centro', 'Osório', '1992-07-10', 'Migrado do sistema antigo', 1, '2023-10-24 17:08:54', '2023-10-24 17:08:54'),
(617, 'AF dos Santos Silva Gas (Gas da Brasil', 'Física', '408.220.240001', NULL, '(51)99595-9776', '', 'Av. Brasil', '980', 'Caravagio', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-10-30 14:49:05', '2023-10-30 14:49:05'),
(618, 'Paula Denise Schuler', 'Física', '765.733.700-15', NULL, '(51)99894-7092', '', 'Rua Tramandai', '279', 'Bairro Medianera', '', '1979-03-31', 'Migrado do sistema antigo', 1, '2023-10-31 12:49:40', '2023-10-31 12:49:40'),
(619, 'João Vitor Nunes da Silva', 'Física', '052.282.180-41', NULL, '(51)99705-2157', '', 'Rua dos Lírios', '51', 'Medianeira', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-11-06 20:18:50', '2023-11-06 20:18:50'),
(620, 'José Luiz', 'Física', '021.444.110-50', NULL, '(51)99505-1339', '', 'Rua dotor Mário Santo dani', '183', '', 'Osório', '1990-07-28', 'Migrado do sistema antigo', 1, '2023-11-08 12:13:51', '2023-11-08 12:13:51'),
(621, 'Ana Paula Nunes Gonçalves', 'Física', '011.345.100-80', NULL, '(51)99524-2198', '', 'Rua Francelino Batista Gomes', '97', 'Porto Lacustre', 'Osório', '1993-08-29', 'Migrado do sistema antigo', 1, '2023-11-08 17:45:01', '2023-11-08 17:45:01'),
(622, 'Loreni de Almeida Alves', 'Física', '013.615.870-67', NULL, '(51)98117-5636', '', 'Rua Vereador Antonio Tresssoldi', '74', 'Parque Real', 'Osório', '1985-09-07', 'Migrado do sistema antigo', 1, '2023-11-09 17:28:30', '2023-11-09 17:28:30'),
(623, 'Bruno Pasa de Lima', 'Física', '020.312.190-22', NULL, '(51)99909-1076', '(51)99912-8104', 'Rua Machado de Assis', '1613', 'Sulbrasileiro', 'Osório', '1997-04-22', 'Migrado do sistema antigo', 1, '2023-11-20 12:14:18', '2023-11-20 12:14:18'),
(624, 'Paulo Cesar dos Santos Teixeira', 'Física', '562.794.850-72', NULL, '(51)99777-6471', '', 'Rua 16 de Dezembro', '778', 'Gloria', 'Osórioo', '1971-10-25', 'Migrado do sistema antigo', 1, '2023-11-21 11:36:34', '2023-11-21 11:36:34'),
(625, 'Jorgina Gomes da Silva', 'Física', '000.813.600-96', NULL, '(51)99567-0067', '', 'Rua Buzios', '725', 'Atlântida Sul', 'Osório', '1983-03-01', 'Migrado do sistema antigo', 1, '2023-11-21 19:56:43', '2023-11-21 19:56:43'),
(626, 'Lucileia Inacio Telles', 'Física', '005.155.280-99', NULL, '(51)99639-5962', '', 'major João Marques', '129', 'Centro', 'Osório', '1981-04-13', 'Migrado do sistema antigo', 1, '2023-11-22 13:27:47', '2023-11-22 13:27:47'),
(627, 'Remo Valim', 'Física', '171.691.600-30', NULL, '(51)98426-0865', '', 'Rua 7 de Setembro', '385/410', 'Centro', 'Osório', '1953-06-14', 'Migrado do sistema antigo', 1, '2023-11-23 18:34:24', '2023-11-23 18:34:24'),
(628, 'João Pedro Ramos', 'Física', '054.101.620-21', NULL, '(51)98139-5747', '', 'Rua Costa Gama', '1200', 'Centro', 'Osório', '2004-10-10', 'Migrado do sistema antigo', 1, '2023-11-27 18:36:41', '2023-11-27 18:36:41'),
(629, 'Mozart Rodrigues da Silva', 'Física', '030.239.620-90', NULL, '(51)99559-4328', '', 'Rua presidente Costa e Silva', '922', 'Primavera', 'Osório', '1992-01-22', 'Migrado do sistema antigo', 1, '2023-11-28 13:30:03', '2023-11-28 13:30:03'),
(630, 'Marcia Marques Teixeira da Silva', 'Física', '894.070.080-53', NULL, '(51)99547-3097', '', 'Rua 7 de Setembro', '672/406', 'Centro', 'Osório', '1986-06-25', 'Migrado do sistema antigo', 1, '2023-11-28 16:38:52', '2023-11-28 16:38:52'),
(631, 'William de Souza Alvares', 'Física', '213.685.768-18', NULL, '(51)99361-8146', '', 'Rodovia Pedro Lino Firme', '2551', 'Borrucia', 'Osório', '1978-10-23', 'Migrado do sistema antigo', 1, '2023-12-05 12:02:53', '2023-12-05 12:02:53'),
(632, 'Edu Agliardi', 'Física', '033.701.170-29', NULL, '(51)98056-6608', '', 'Estrada Osório torres', '17489', '', 'Osório', '1993-07-21', 'Migrado do sistema antigo', 1, '2023-12-05 22:09:57', '2023-12-05 22:09:57'),
(633, 'Jeferson Ricardo Ferri Barbosa', 'Física', '000.968.620-76', NULL, '(51)99788-5856', '', 'Rua Barão do Rio Branco', '2345', '', 'Osório', '1984-08-07', 'Migrado do sistema antigo', 1, '2023-12-11 13:02:25', '2023-12-11 13:02:25'),
(634, 'Tiago Augusto Aliardi Gonçalves', 'Física', '026.152.780-02', NULL, '(51)99717-0019', '', 'Rua Santos Dumont', '693/101', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2023-12-11 17:31:47', '2023-12-11 17:31:47'),
(635, 'Taise Pereira Alburquerque', 'Física', '014.660.070-36', NULL, '(51)99714-5620', '', 'Av. General Osório', '970', 'Sulbrasileiro', 'Osório', '1988-08-18', 'Migrado do sistema antigo', 1, '2023-12-11 19:55:35', '2023-12-11 19:55:35'),
(636, 'João Carlos da Silva Souza', 'Física', '706.442.680-34', NULL, '51 99817-1220', '', 'Rua Paulo Porsch', '41', 'Santa Luzia', 'Capão da Canoa', '1977-02-07', 'Migrado do sistema antigo', 1, '2023-12-18 14:11:39', '2023-12-18 14:11:39'),
(637, 'Sabrini Moller Boes', 'Física', '022.226.340-75', NULL, '(51)99812-8980', '', 'Rua da Servidão,', '1027', 'Agasa', 'Santo Antonio da Patrulha', '1990-05-21', 'Migrado do sistema antigo', 1, '2023-12-19 12:35:22', '2023-12-19 12:35:22'),
(638, 'Paula Pereira (escola Pimpolhos)', 'Física', '004.105.490-37', NULL, '(51)99786-3237', '', 'Alameidas', '30', '', 'Osório', '1982-02-12', 'Migrado do sistema antigo', 1, '2023-12-19 21:31:52', '2023-12-19 21:31:52'),
(639, 'Lucas Policarpo Alminhana', 'Física', '837.143.750-15', NULL, '(51)98226-2574', '', 'General Osorio', '969', 'albatroz', 'osorio', '1987-04-10', 'Migrado do sistema antigo', 1, '2023-12-20 12:09:09', '2023-12-20 12:09:09'),
(640, 'Leonardo Dutra Selau', 'Física', '976.983.850-00', NULL, '(51)99613-7648', '', 'Jorge Dariva', '2632', '', 'Osório', '1978-10-17', 'Migrado do sistema antigo', 1, '2023-12-21 19:42:47', '2023-12-21 19:42:47'),
(641, 'Werner Cesa Bocchese', 'Física', '204.914.840-20', NULL, '(51)99661-9652', '', 'Rua Santos Dumont', '693/201', 'Centro', 'Osório', '1952-01-10', 'Migrado do sistema antigo', 1, '2024-01-03 17:13:15', '2024-01-03 17:13:15'),
(642, 'Evandro de Souza Borba', 'Física', '056.689.260-01', NULL, '(51)99810-7088', '', 'Rua Salgado Filho', '84', 'Albatroz', 'Osório', '2004-11-20', 'Migrado do sistema antigo', 1, '2024-01-04 19:44:04', '2024-01-04 19:44:04'),
(643, 'Wilian de Souza Alvarez Junior', 'Física', '045.298.530-71', NULL, '(51)98973-7531', '', 'Estrada Borrusia', '2551', 'Borussia', 'Osório', '1998-09-26', 'Migrado do sistema antigo', 1, '2024-01-05 17:37:48', '2024-01-05 17:37:48'),
(644, 'Leonardo Pizzolotto', 'Física', '043.454.550-30', NULL, '(51)99947-5579', '', 'Rua Manoel Marques da Rosa', '601', '', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-01-08 17:34:54', '2024-01-08 17:34:54'),
(645, 'Lucilene Calabresi de Souza', 'Física', '015.514.990-37', NULL, '(51)99507-5089', '', 'Estrada Geral da Borrucia', '115', 'Borrucia', 'Osório', '1983-09-16', 'Migrado do sistema antigo', 1, '2024-01-15 18:09:59', '2024-01-15 18:09:59'),
(646, 'Jose Luis Muniz Cadena', 'Física', '494.578.107-91', NULL, '(53)99931-1383', '(51)98139-4833', 'Rua das Laranjeiras', '1307', 'Palmital', 'Osório', '1957-05-19', 'Migrado do sistema antigo', 1, '2024-01-18 13:35:26', '2024-01-18 13:35:26'),
(647, 'Silvana Silva da Cunha', 'Física', '028.093.310-06', NULL, '51 99888-6773', '51 9977-3345', 'BR 101 KM 84', '465', 'Costa Verde', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-01-22 18:22:56', '2024-01-22 18:22:56'),
(648, 'Adao Lopes Barrozo', 'Física', '185.633.920-34', NULL, '(51)99855-5978', '', 'Rua Major Jão Marques', '1910', 'Sul Brasileiro', 'osório', NULL, 'Migrado do sistema antigo', 1, '2024-01-30 12:22:23', '2024-01-30 12:22:23'),
(649, 'Douglas da Silva Puls', 'Física', '034.912.910-03', NULL, '(51)99891-3697', '(51)98026-6880', 'Av. General Osório', '2820', 'Gloria', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-02-01 18:08:09', '2024-02-01 18:08:09'),
(650, 'Giancarlo Dalpiaz de Oliveira', 'Física', '016.764.210-33', NULL, '(51)99629-9197', '', 'Rua Borges de Medeiros', '540', 'Centro', 'Cidreira', '1988-09-23', 'Migrado do sistema antigo', 1, '2024-02-02 13:44:10', '2024-02-02 13:44:10'),
(651, 'Stela Maris Dal Asta', 'Física', '287.872.770-34', NULL, '(54)99693-8201', '', 'Rua Eseadas', '202', 'Atlantida Sul', 'Osório', '1952-06-08', 'Migrado do sistema antigo', 1, '2024-02-03 13:06:31', '2024-02-03 13:06:31'),
(652, 'Guilherme Hammarstrom Dobler', 'Física', '014.179.050-42', NULL, '(51)99109-9684', '', 'Estrada Pico da Borrucia', '109', 'Borrucia', 'Osório', '1991-05-28', 'Migrado do sistema antigo', 1, '2024-02-07 14:07:31', '2024-02-07 14:07:31'),
(653, 'Leandro Bittencourt Motta', 'Física', '632.226.710-87', NULL, '(51)99953-5118', '(47)99242-6887', 'Rua Jolbert de Carvalho', '113', 'Salinas', 'Cidreira', '1974-08-19', 'Migrado do sistema antigo', 1, '2024-02-14 18:54:40', '2024-02-14 18:54:40'),
(654, 'Miguel Angelo Almeida Gomes (Tabelionato de Protes', 'Física', '597.773.890-00', NULL, '(51)99725-6817', '', 'Rua 24 de Maio', '268/304', 'Centro', 'Osório', '1967-11-22', 'Migrado do sistema antigo', 1, '2024-02-15 19:17:40', '2024-02-15 19:17:40'),
(655, 'Marco Antonio Costa', 'Física', '643.074.920-72', NULL, '(51)98013-3807', '', 'Rua Osório Pereira da Silva', '05', 'Granja Vargas', 'Palmares do Sul', NULL, 'Migrado do sistema antigo', 1, '2024-02-15 20:29:33', '2024-02-15 20:29:33'),
(656, 'Rita de Cassia Moraes da Cunha', 'Física', '002.830.280-09', NULL, '(51)99881-7949', '', 'Estrada do Mar Km 5', '1710', 'Varzea do Padre', 'Osório', '1985-07-16', 'Migrado do sistema antigo', 1, '2024-02-21 11:45:50', '2024-02-21 11:45:50'),
(657, 'Alessandra da Silva Oliveira', 'Física', '012.894.960-05', NULL, '(51)98051-9859', '(51)99591-4723', 'Rua Voluntarios da Patria', '354', 'Porto', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-02-22 17:58:28', '2024-02-22 17:58:28'),
(658, 'Augusto Doering', 'Física', '021.805.260-08', NULL, '(51)99525-1735', '', 'Rua 7 de Setembro', '1177', 'Gloria', 'Osório', '1988-10-18', 'Migrado do sistema antigo', 1, '2024-02-26 14:37:26', '2024-02-26 14:37:26'),
(659, 'Daniela', 'Física', '003.184.510-00', NULL, '(51)9551-2347', '(51)99740-1588', 'rua conico Pedro jacobs', '1755', 'Caravajo', 'Osório', '1980-02-22', 'Migrado do sistema antigo', 1, '2024-02-28 13:55:39', '2024-02-28 13:55:39'),
(660, 'Maria Clara Citton', 'Física', '028.880.520-83', NULL, '(51)99699-2530', '', 'Rua Joanim Gamba', '26', 'Caiu do Ceu', 'Osório', '2006-01-23', 'Migrado do sistema antigo', 1, '2024-02-28 17:06:04', '2024-02-28 17:06:04'),
(661, 'José Luiz Barreto da Costa', 'Física', '582.429.290-68', NULL, '(54)99641-9169', '', 'Av. Getilio Vargas', '1124', 'Centro', 'Osório', '1968-08-17', 'Migrado do sistema antigo', 1, '2024-03-01 18:34:41', '2024-03-01 18:34:41');
INSERT INTO `clientes` (`id`, `nome_completo`, `tipo_pessoa`, `documento`, `email`, `telefone_principal`, `telefone_secundario`, `endereco_logradouro`, `endereco_numero`, `endereco_bairro`, `endereco_cidade`, `data_nascimento`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(662, 'Cleidi Moraes Bermudas', 'Física', '015.771.690-26', NULL, '(51)99613-9737', '', 'Rua Barão do Triunfo', '1564', 'Centro', 'Osório', '1986-07-16', 'Migrado do sistema antigo', 1, '2024-03-05 13:17:54', '2024-03-05 13:17:54'),
(663, 'Adriani Oliveira Lopes', 'Física', '021.917.040-18', NULL, '(51)99925-0435', '', 'Rua das Margaridas', '316', 'Primavera', 'Osório', '1988-08-21', 'Migrado do sistema antigo', 1, '2024-03-06 21:54:23', '2024-03-06 21:54:23'),
(664, 'Mariana Arruda Tomasi', 'Física', '032.932.300-83', NULL, '(51)99921-1266', '', 'Alameda A', '286', 'Bosques do Albatroz', 'Osório', '2003-09-24', 'Migrado do sistema antigo', 1, '2024-03-07 12:58:00', '2024-03-07 12:58:00'),
(665, 'Antonia Gomes Hilario', 'Física', '060.148.820-22', NULL, '(51)99910-6423', '', 'Mario santo Dania', '921', 'Centro', 'osório', NULL, 'Migrado do sistema antigo', 1, '2024-03-08 19:34:45', '2024-03-08 19:34:45'),
(666, 'Denise da Silva Nunes', 'Física', '000.331.450-24', NULL, '(51)99748-0959', '', 'Rua Maria Guedes Monteiro', '200', 'Laranjeiras', 'Osório', '1980-07-19', 'Migrado do sistema antigo', 1, '2024-03-11 13:33:01', '2024-03-11 13:33:01'),
(667, 'Irenilda Andrade dos Santos', 'Física', '017.517.820-85', NULL, '(51)99661-9688', '', 'Estrada da Estancia', '6955', 'Estancia Velha', 'Tramandai', NULL, 'Migrado do sistema antigo', 1, '2024-03-11 13:44:03', '2024-03-11 13:44:03'),
(668, 'Willy August Albert Sant Helena Armani', 'Física', '001.061.470-21', NULL, '(51)98109-4328', '(51)98109-4307', 'Rua Camaqua', '33', 'Serra Mar', 'Osório', '1982-02-18', 'Migrado do sistema antigo', 1, '2024-03-11 17:57:53', '2024-03-11 17:57:53'),
(669, 'Andrea Cruz da Silva', 'Física', '693.638.200-25', NULL, '(51)98043-6995', '', 'Rua 21 de Abril', '80', 'Gloria', 'Osório', '1977-01-01', 'Migrado do sistema antigo', 1, '2024-03-13 22:02:40', '2024-03-13 22:02:40'),
(670, 'Marcelo Graciano Ferreira', 'Física', '686.342.180-15', NULL, '(51)99889-9568', '', 'Rua Manuel Marques da Rosa', '601', 'Centro', 'Osório', '1976-10-01', 'Migrado do sistema antigo', 1, '2024-03-15 13:57:04', '2024-03-15 13:57:04'),
(671, 'Janaina Santos Soares', 'Física', '000.319.890-12', NULL, '(51)98417-1774', '', 'Rua Voluntarios da Patria', '357', 'Centro', 'Osório', '1979-07-22', 'Migrado do sistema antigo', 1, '2024-03-19 16:09:34', '2024-03-19 16:09:34'),
(672, 'Daiane de Fraga Silva', 'Física', '934.310.230-53', NULL, '(51)99829-5391', '', 'Rua disseseis de  dezembro', '267', 'Centro', 'Osório', '1979-06-22', 'Migrado do sistema antigo', 1, '2024-03-19 20:22:46', '2024-03-19 20:22:46'),
(673, 'helto da Rosa campos', 'Física', '412.066.060-53', NULL, '(51)99809-2312', '', 'Rua brasilia', '95', 'Encosta da Serra', 'osório', '1965-02-14', 'Migrado do sistema antigo', 1, '2024-03-20 20:55:26', '2024-03-20 20:55:26'),
(674, 'Sandro alex Fontoura Vargas', 'Física', '704.344.330-04', NULL, '(53)99988-4950', '', 'Rua Fernandes Bastos', '7400', 'Cruzeiro', 'Tramandai', '1970-05-22', 'Migrado do sistema antigo', 1, '2024-03-25 18:02:44', '2024-03-25 18:02:44'),
(675, 'Arthur Witt Koeche', 'Física', '058.274.230-76', NULL, '(51)99874-4899', '', 'Rua marechal Floriano', '792/02', 'Centro', 'Osório', '2007-06-05', 'Migrado do sistema antigo', 1, '2024-04-01 13:40:57', '2024-04-01 13:40:57'),
(676, 'Pablo Misael Franco Pires', 'Física', '047.123.600-46', NULL, '(51)98081-8240', '', 'Sidonio Ramos de Oliveira', '481', 'Caravagio', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2024-04-01 20:02:01', '2024-04-01 20:02:01'),
(677, 'Elvis Calabresi Garcia', 'Física', '010.945.650-59', NULL, '(51)99551-1480', '', 'Estrada Geral da Borrucia', '2555', 'Borrucia', 'Osório', '1984-06-12', 'Migrado do sistema antigo', 1, '2024-04-06 14:35:12', '2024-04-06 14:35:12'),
(678, 'Luiz Cristiano Machado', 'Física', '708.736.150-34', NULL, '(51)98954-9722', '', 'Rua Pinheiro Machado', '924/Apt', 'Sulbrasileiro', 'Osório', '1977-01-19', 'Migrado do sistema antigo', 1, '2024-04-08 13:30:58', '2024-04-08 13:30:58'),
(679, 'Josue Silveira dos Santos', 'Física', '957.590.500-87', NULL, '(51)998011-6593', '', 'Rua da lagoa', '238', 'Porto Lacustre', 'Osorio', '1979-07-17', 'Migrado do sistema antigo', 1, '2024-04-09 16:30:52', '2024-04-09 16:30:52'),
(680, 'Paulo Rene Bauduino', 'Física', '222.739.050-68', NULL, '(51)99668-4013', '(51)98322-4856', 'Rua 10 de Novembro', '42', 'Porto', 'Osório', '1954-08-05', 'Migrado do sistema antigo', 1, '2024-04-10 12:30:32', '2024-04-10 12:30:32'),
(681, 'Tainara Ramos Selau', 'Física', '050.826.110-46', NULL, '(51)99896-9011', '', 'Travessa solidao', '4630', '', 'Mostardas', NULL, 'Migrado do sistema antigo', 1, '2024-04-11 13:45:49', '2024-04-11 13:45:49'),
(682, 'Ione Teresinha Lerias Freitas', 'Física', '402.945.870-04', NULL, '(51)99764-4535', '', 'Rua 8', '54', 'Parque Emboaba', 'Tramandai', '1951-08-24', 'Migrado do sistema antigo', 1, '2024-04-15 14:32:07', '2024-04-15 14:32:07'),
(683, 'Vitor Parnoff', 'Física', '038.257.850-33', NULL, '(51)99498-6192', '', 'Rua leopoldo Nunes martins', '102', 'Laranjeiras', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2024-04-15 17:09:57', '2024-04-15 17:09:57'),
(684, 'Alaides Teresinha Dariva de Oliveira', 'Física', '287.020.260-15', NULL, '(51)98972-0660', '', 'Sete de setembro', '593', 'Centro', 'Osório', '1952-06-19', 'Migrado do sistema antigo', 1, '2024-04-15 17:28:42', '2024-04-15 17:28:42'),
(685, 'Igor gomes rodrigues', 'Física', '093.585.039-20', NULL, '(51)98150-8676', '(51)98177-6147', 'Rua barão do triunfo', '1138', 'CDentro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-04-18 17:17:30', '2024-04-18 17:17:30'),
(686, 'Pietro pires pacheco', 'Física', '001.850.480-76', NULL, '(51)98025-1640', '', '15 de novembro', '384', 'Centro', 'Osório', '1971-12-01', 'Migrado do sistema antigo', 1, '2024-04-19 13:36:30', '2024-04-19 13:36:30'),
(687, 'IEL Instaladora', 'Física', '032.494.450001', NULL, '(51)98262-9110', '', 'Mario Santo Dani', '183', 'centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-04-22 13:14:57', '2024-04-22 13:14:57'),
(688, 'Alexandre dos reis lessa', 'Física', '931.289.320-34', NULL, '(51)9925-3641', '', 'Rua Roraima', '745', 'Nova tramandai', '', '1976-08-08', 'Migrado do sistema antigo', 1, '2024-04-24 16:33:57', '2024-04-24 16:33:57'),
(689, 'Marcelo rodrigues madeira', 'Física', '706.893.420-04', NULL, '(53)99125-3077', '', 'Marechal Deodoro', '2478', 'Glória', 'Osório', '1973-02-10', 'Migrado do sistema antigo', 1, '2024-04-25 14:26:29', '2024-04-25 14:26:29'),
(690, 'Antonio Maicon Marques de Matos', 'Física', '022.960.860-47', NULL, '(55)99181-0932', '', 'João Sarmento', '1665', '', 'Osorio', '1990-07-17', 'Migrado do sistema antigo', 1, '2024-05-06 17:36:32', '2024-05-06 17:36:32'),
(691, 'Everton Ricardo Bootz', 'Física', '429.676.270-20', NULL, '(51)99215-8506', '', 'Alameda A', '115', 'Bosques do Albatroz', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-05-06 19:03:24', '2024-05-06 19:03:24'),
(692, 'Robertinho da Silva Escober', 'Física', '275.615.850-04', NULL, '(51)98330-5039', '(51)99332-4831', 'Estrada do Mar Km 6,5', '3435', 'Varzea do Padre', 'Osório', '1956-02-24', 'Migrado do sistema antigo', 1, '2024-05-07 11:59:25', '2024-05-07 11:59:25'),
(693, 'Jair Pelisoli', 'Física', '657.722.104-9', NULL, '(51)99155-3409', '', 'Alameda dos Veledos- Condominio do Interlagos', '370', '', 'Osorio', '1952-03-21', 'Migrado do sistema antigo', 1, '2024-05-07 17:50:30', '2024-05-07 17:50:30'),
(694, 'Nicoli patricia da mota', 'Física', '846.209.860-20', NULL, '(51)98112-9669', '', 'Onolio Silveira Dias', '700', '', 'Porto Alegre', '1995-07-28', 'Migrado do sistema antigo', 1, '2024-05-07 18:01:11', '2024-05-07 18:01:11'),
(695, 'Fernando Alves', 'Física', '033.784.740-17', NULL, '(51)99800-5962', '', 'Rua Lidio Fasto de Souza', '255', 'Albatroz', 'Osorio', '2001-07-04', 'Migrado do sistema antigo', 1, '2024-05-07 18:11:16', '2024-05-07 18:11:16'),
(696, 'Maira da Silveira Cavalheiro', 'Física', '032.720.220-31', NULL, '(51)98267-7369', '', 'Icarai', '1155', '', 'Atlantida Sul', '1995-11-30', 'Migrado do sistema antigo', 1, '2024-05-15 16:53:25', '2024-05-15 16:53:25'),
(697, 'Gislaine Araujo', 'Física', '006.205.1105-5', NULL, '(51)99190-3714', '', 'Rua Castro Alves', '112', 'Mariapulis', 'Osório', '1984-01-24', 'Migrado do sistema antigo', 1, '2024-05-16 17:38:34', '2024-05-16 17:38:34'),
(698, 'Gislaine Araujo', 'Física', '006.205.110-52', NULL, '(51)99190-3714', '', 'Rua Castro Alves', '112', 'Mariapulis', 'Osório', '1984-01-24', 'Migrado do sistema antigo', 1, '2024-05-16 17:40:44', '2024-05-16 17:40:44'),
(699, 'Natanael Rodrigues Monteiro', 'Física', '013.968.920-60', NULL, '(51)99853-4960', '', 'Rua Luiz Manoel Teixeira', '158', 'Chacara Velha', 'Palmares do Sul', '1992-06-22', 'Migrado do sistema antigo', 1, '2024-05-16 19:06:08', '2024-05-16 19:06:08'),
(700, 'Adão da Silva Filho', 'Física', '473.870.250-53', NULL, '(51)99533-5559', '', 'Rua Sete de Setembro', '1097', '', 'Osório', '1966-09-13', 'Migrado do sistema antigo', 1, '2024-05-21 18:15:07', '2024-05-21 18:15:07'),
(701, 'Jairo Aliorio do arte dos Santos', 'Física', '219.787.600-71', NULL, '(51)98401-1278', '', 'Rua Circulução 2 / condomínio Del vilie', '52', '', 'Osório', '1957-02-13', 'Migrado do sistema antigo', 1, '2024-05-21 19:29:23', '2024-05-21 19:29:23'),
(702, 'Pedro Henrique Eduardo Silveira', 'Física', '052.249.520-69', NULL, '(51)98175-8549', '', 'Rua Machado de assis', '1408', 'Sul Brasileira', 'Osório', '2005-07-07', 'Migrado do sistema antigo', 1, '2024-05-22 20:30:38', '2024-05-22 20:30:38'),
(703, 'Hellen Monique da Motta', 'Física', '846.209.780-00', NULL, '(51)99607-4231', '', 'Rua Dom Pedro II', '509', 'Centro', 'Pelotas', '2001-09-01', 'Migrado do sistema antigo', 1, '2024-05-28 14:17:31', '2024-05-28 14:17:31'),
(704, 'Maria Aldina Silva Pereira', 'Física', '996.110.740-34', NULL, '(51)99824-0772', '', 'RS 030 Parada 218', '250', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-05-29 13:39:15', '2024-05-29 13:39:15'),
(705, 'Fabricia Ferri Pelissoli', 'Física', '001.899.160-28', NULL, '(51)99824-6175', '', 'Rua Manoel Hipolito', '109', 'Laranjeiras', 'Osório', '1983-08-02', 'Migrado do sistema antigo', 1, '2024-05-29 13:57:08', '2024-05-29 13:57:08'),
(706, 'Daiane grassi Nunes', 'Física', '007.624.110-66', NULL, '(51)99550-1416', '', 'Trav. Petrolina Firme', '460', 'Borrusia', 'osório', '1986-09-22', 'Migrado do sistema antigo', 1, '2024-05-31 17:21:06', '2024-05-31 17:21:06'),
(707, 'Eduardo Ruppenthal', 'Física', '812.899.950-87', NULL, '(51)99816-3231', '', 'Rua Santos Dommont ap 201', '693', '', 'Osorio', '1981-07-14', 'Migrado do sistema antigo', 1, '2024-06-04 19:40:34', '2024-06-04 19:40:34'),
(708, 'Mariangela Muller Alvez', 'Física', '662.340.340-04', NULL, '(51)99818-6698', '', 'Rua Lidio Fasto de Souza', '255', 'Albatroz', 'Osório', '1972-05-04', 'Migrado do sistema antigo', 1, '2024-06-05 17:54:42', '2024-06-05 17:54:42'),
(709, 'Marcio Alminhana', 'Física', '804.205.830-68', NULL, '(51)98179-0942', '', '15 de novembro', '519/ ap', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2024-06-12 17:54:34', '2024-06-12 17:54:34'),
(710, 'Matheus Quaiatto', 'Física', '053.743.440-21', NULL, '(51)99741-6092', '', 'AV. Saquarema', '1919', '', 'Atlântida Sul', '1999-07-08', 'Migrado do sistema antigo', 1, '2024-06-17 18:14:26', '2024-06-17 18:14:26'),
(711, 'Igor Oliveira da Silva', 'Física', '044.268.660-90', NULL, '(51)98270-9798', '', 'Rua Coronel Reduzino Pacheco', '1357', '', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2024-06-17 19:38:01', '2024-06-17 19:38:01'),
(712, 'Crissiane Lopes Moraes', 'Física', '031.276.140-63', NULL, '(51)99704-6344', '', 'Rua Barão do Rio Branco', '2117', 'Sulbrasileiro', 'Osório', '1992-11-11', 'Migrado do sistema antigo', 1, '2024-06-19 14:23:25', '2024-06-19 14:23:25'),
(715, 'Mateus Da Cunha Santos', 'Física', '019.010.420-14', NULL, '+1 (504) 342-12', '', 'Rua união', '59', 'Porto Lacustre', 'osório', '1990-10-23', 'Migrado do sistema antigo', 1, '2024-06-19 19:28:10', '2024-06-19 19:28:10'),
(716, 'Leticia Laurene de Brito dos Santos', 'Física', '031.568.612-00', NULL, '91 8901-2862', '', 'Rua João Sarmento', '1059/ A', 'Centro', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2024-06-19 23:25:14', '2024-06-19 23:25:14'),
(717, 'Eduardo Silva do Nascimento', 'Física', '002.315.310-52', NULL, '(51)99710-9359', '', 'Rua Claudio E Henrique Albert', '213', 'Bosques do Albatroz', 'Osorio', '1980-08-10', 'Migrado do sistema antigo', 1, '2024-06-24 19:48:27', '2024-06-24 19:48:27'),
(718, 'Luciane Marisa Kern Soares Ladwig', 'Física', '599.538.880-00', NULL, '(51)98413-8255', '', 'Rua Dr Ernesto Silveira Neto', '1065', 'Passinhos', 'Osório', '1964-12-10', 'Migrado do sistema antigo', 1, '2024-07-04 11:41:05', '2024-07-04 11:41:05'),
(719, 'Luiz Felipe Villela Nelsis', 'Física', '452.548.850-68', NULL, '(51)99721-0259', '', 'Rua da Lagoa', '1337', 'Farroupilha', 'Osório', '1964-07-01', 'Migrado do sistema antigo', 1, '2024-07-05 13:00:43', '2024-07-05 13:00:43'),
(720, 'Mariana Nunes Barato', 'Física', '044.999.780-41', NULL, '(51)99701-3395', '', 'Av Sacarema', '1431', 'Atlântida Sul', 'Osorio', '1998-11-02', 'Migrado do sistema antigo', 1, '2024-07-06 14:12:44', '2024-07-06 14:12:44'),
(721, 'Natalia Rapach Pacheco', 'Física', '958.579.740-20', NULL, '(51)99430-1966', '', 'Rua da Lagoa', '1111', 'Farroupilha', 'Osorio', NULL, 'Migrado do sistema antigo', 1, '2024-07-08 16:49:01', '2024-07-08 16:49:01'),
(722, 'Valdir Moura da Mota', 'Física', '346.876.880-04', NULL, '(51)99927-2980', '', 'Major Jon Marques', '640', 'Centro', 'Osório', '1962-04-13', 'Migrado do sistema antigo', 1, '2024-07-10 18:46:04', '2024-07-10 18:46:04'),
(723, 'Pedro Lucas Porfirio Sefrin', 'Física', '028.898.320-31', NULL, '(51)99946-1022', '', 'Estrada do Mar Km 8', '5090', 'Varzea do Padre', 'Osório', '1999-08-19', 'Migrado do sistema antigo', 1, '2024-07-15 13:33:00', '2024-07-15 13:33:00'),
(724, 'Dante Daniel Acosta sosa', 'Física', '841.537.920-04', NULL, '(51)99680-0680', '', '16 de Dezembro', '925', '', 'Osório', '1977-12-12', 'Migrado do sistema antigo', 1, '2024-07-16 12:36:00', '2024-07-16 12:36:00'),
(725, 'Paulo Renato Vicari', 'Física', '022.946.340-12', NULL, '(51)99781-5255', '', 'Rua Sete de Setembro', '1003', 'centro', 'Osório', '1990-12-05', 'Migrado do sistema antigo', 1, '2024-07-17 16:37:34', '2024-07-17 16:37:34'),
(726, 'Lucas Cantanhede Guerra', 'Física', '005.773.512-30', NULL, '(95)99154-9783', '', 'Rua da lagoa', '667', 'Porto Lacustre', 'Osório', '1095-12-21', 'Migrado do sistema antigo', 1, '2024-07-22 11:39:33', '2024-07-22 11:39:33'),
(727, 'Valter Enio Eberhardt', 'Física', '120.644.320-00', NULL, '(51)99681-0463', '', 'Rua Sete de Setembro', '943', 'Centro', 'Osório', '1950-06-20', 'Migrado do sistema antigo', 1, '2024-07-22 17:49:21', '2024-07-22 17:49:21'),
(728, 'Gabriel Tosi Caigaro', 'Física', '031.841.340-08', NULL, '(49)98428-6293', '', 'Rua Pinheiro Machado', '507', 'Glória', 'Osório', '2002-03-06', 'Migrado do sistema antigo', 1, '2024-07-26 17:24:32', '2024-07-26 17:24:32'),
(729, 'Adao Pires de Souza', 'Física', '040.503.980-87', NULL, '(51)99733-7426', '', 'Av. Getulio Vargas', '1108 Ap', 'Centro', 'Osório', '1940-02-03', 'Migrado do sistema antigo', 1, '2024-07-30 17:41:24', '2024-07-30 17:41:24'),
(730, 'Lisiane Silva dos Santos', 'Física', '965.186.930-53', NULL, '(51)99922-7310', '', 'RS- 30', '9600', 'Laranjeira', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-07-30 17:51:25', '2024-07-30 17:51:25'),
(731, 'Tiago  Andre Camini', 'Física', '024.430.470-08', NULL, '(51)99749-0185', '', 'Rua Estancia da Serra', '458', 'Vila da Serra', 'Osório', '1991-09-14', 'Migrado do sistema antigo', 1, '2024-07-31 12:00:28', '2024-07-31 12:00:28'),
(732, 'Camila Cabrera', 'Física', '001.304.330-71', NULL, '(51)99976-4223', '', 'Dez de Novembro', '241', 'Porto Lacustre', 'Osório', '1981-03-20', 'Migrado do sistema antigo', 1, '2024-08-03 12:05:47', '2024-08-03 12:05:47'),
(733, 'Ariane da Silveira Batista', 'Física', '037.754.270-90', NULL, '(51)98058-9925', '', 'Estrada RS 309 Km 5', '', 'Várzea do padre', 'Osório', '1999-09-27', 'Migrado do sistema antigo', 1, '2024-08-05 16:56:17', '2024-08-05 16:56:17'),
(734, 'Luke Sewell', 'Física', '603.236.660-00', NULL, '(51)99150-5196', '', 'Alameda da Serra , Interlagos', '894', '', 'Osório', '1988-03-04', 'Migrado do sistema antigo', 1, '2024-08-05 17:01:47', '2024-08-05 17:01:47'),
(735, 'Nicole Loreno Pereira', 'Física', '036.007.870-20', NULL, '(51)99978-0237', '', 'Rua Marcelino Martins de freitas Ap301', '287', 'Pitangas', '', '2001-07-18', 'Migrado do sistema antigo', 1, '2024-08-05 21:07:31', '2024-08-05 21:07:31'),
(736, 'Jamile Dorneles de Oliveira', 'Física', '042.890.530-76', NULL, '51 99329-8618', '', 'Rua João Sarmento', '909/ AP', 'Centro', 'Osório', '1997-08-11', 'Migrado do sistema antigo', 1, '2024-08-06 16:30:46', '2024-08-06 16:30:46'),
(737, 'Rafael Cherutti Alves', 'Física', '046.692.160-82', NULL, '(51)99921-0931', '', 'Rua Mario Santo Dani', '677', 'Centro', 'Osório', '2004-04-01', 'Migrado do sistema antigo', 1, '2024-08-09 16:00:09', '2024-08-09 16:00:09'),
(738, 'Edson dourine', 'Física', '16679598087', NULL, '(51)98300-2867', '', '', '', 'Gloria', 'Osório', '1956-01-23', 'Migrado do sistema antigo', 1, '2024-08-20 11:12:28', '2024-08-20 11:12:28'),
(739, 'Anildo Messagi', 'Física', '574.149.710-15', NULL, '(51)98200-9654', '', 'Rua Costa Gama', '500 APT', 'Centro', 'Osório', '1971-09-01', 'Migrado do sistema antigo', 1, '2024-08-20 18:56:56', '2024-08-20 18:56:56'),
(740, 'Lucas dos Santos Araujo', 'Física', '041.378.730-38', NULL, '(51)99123-2124', '', '7 Setembro', '8802', 'Centro', 'Osório', '1998-09-23', 'Migrado do sistema antigo', 1, '2024-08-22 17:54:19', '2024-08-22 17:54:19'),
(741, 'Jaqueline Braun da Silva', 'Física', '006.387.110-61', NULL, '(51)98129-5701', '', 'BR 101 KM 74', '13035', 'Sertão', 'Osório', '1984-02-23', 'Migrado do sistema antigo', 1, '2024-08-22 18:52:39', '2024-08-22 18:52:39'),
(742, 'Valmicio Pires', 'Física', '187.404.560-72', NULL, '(51)98184-5661', '', 'Rua Marrocos', '136', 'Recanto da Lagoa', 'Tramandai', NULL, 'Migrado do sistema antigo', 1, '2024-08-22 19:00:38', '2024-08-22 19:00:38'),
(743, 'Ray Bueno de Oliveira', 'Física', '028.806.920-18', NULL, '(51)99526-3109', '', '15 de Novembro', '394 APT', '', 'Osório', '1997-08-12', 'Migrado do sistema antigo', 1, '2024-08-23 20:09:54', '2024-08-23 20:09:54'),
(744, 'Ellen Costa De Lima', 'Física', '029.707.890-94', NULL, '(51)99939-2284', '', 'Estrada do Mar Km 7', '4230', 'Várzea do Padre', 'Osório', '1995-11-20', 'Migrado do sistema antigo', 1, '2024-09-02 16:47:33', '2024-09-02 16:47:33'),
(745, 'Telivi Favin', 'Física', '343.519.650-53', NULL, '(51)99937-6464', '', 'Estrada da figueira grande', '990', 'Borussia', 'OSório', '1957-03-16', 'Migrado do sistema antigo', 1, '2024-09-17 11:47:45', '2024-09-17 11:47:45'),
(746, 'Fabricio da Silveira Goulart', 'Física', '031.658.060-04', NULL, '(51)99619-8272', '', 'Rua Dr Pereira Neto, RS T101', '2201', 'Passinhos', 'Osório', '1995-06-27', 'Migrado do sistema antigo', 1, '2024-09-25 12:39:12', '2024-09-25 12:39:12'),
(747, 'MARILDA BATISTA', 'Física', '012.905.060-19', NULL, '(51)99697-1502', '', 'AV ILDEFONSO SIMOES LOPES', '2068', 'GLORIA', 'OSÓRIO', '1980-01-05', 'Migrado do sistema antigo', 1, '2024-09-26 14:59:29', '2024-09-26 14:59:29'),
(748, 'Carlos Matheus Pelissoli', 'Física', '034.512.770-69', NULL, '(51)99854-1975', '', 'Rua Leopoldo Nunes Martins', '98', 'Laranjeira', 'Osório', '2004-03-24', 'Migrado do sistema antigo', 1, '2024-09-27 19:10:27', '2024-09-27 19:10:27'),
(749, 'Agda Wrochinski', 'Física', '037.908.700-85', NULL, '(51)99688-5859', '', 'Rua Manoel Hipólito', '175', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-10-02 17:40:07', '2024-10-02 17:40:07'),
(750, 'Aline dummel', 'Física', '034.909.700-39', NULL, '(51)98532-6919', '(51)98255-1544', 'Acesso ao tombador', '415', 'Bella Vista', '', '1994-06-07', 'Migrado do sistema antigo', 1, '2024-10-11 12:19:02', '2024-10-11 12:19:02'),
(751, 'Paulo Cesár dos Santos', 'Física', '412.004.890-04', NULL, '(51)9993-2163', '', 'Sete de Setembro', '1008', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2024-10-14 16:59:59', '2024-10-14 16:59:59'),
(752, 'Adelcio de Almeida Peres', 'Física', '453.299.740-20', NULL, '(51)99805-2744', '', 'RS 030 KM 70', '2986', 'Laranjeiras', 'Osório', '1966-01-29', 'Migrado do sistema antigo', 1, '2024-10-15 13:33:02', '2024-10-15 13:33:02'),
(753, 'Rosiane Francisco', 'Física', '559.691.940-34', NULL, '(51)99310-3534', '', 'Rua Sapucaia', '91', 'Nordeste', 'imbé', NULL, 'Migrado do sistema antigo', 1, '2024-10-16 19:17:59', '2024-10-16 19:17:59'),
(754, 'Luccas Lima', 'Física', '025.351.150-00', NULL, '(51)98129-9538', '', 'rua da lagoa', '455', 'Farroupilha', 'Osório', '2000-03-29', 'Migrado do sistema antigo', 1, '2024-10-18 20:12:26', '2024-10-18 20:12:26'),
(755, 'Douglas Nunes Grassi', 'Física', '040.841.850-86', NULL, '(51)99789-8647', '', 'Estrada da Goiabeira', '643', 'Borrucia', 'Osório', '1996-02-26', 'Migrado do sistema antigo', 1, '2024-10-19 13:03:11', '2024-10-19 13:03:11'),
(756, 'Andre Vasconcelos Firmino', 'Física', '914.857.700-68', NULL, '(51)98051-8744', '(51)99244-3213', 'Rua Deoclecio Bastos', '279', 'Caravagio', 'Osório', '1979-04-21', 'Migrado do sistema antigo', 1, '2024-10-31 13:40:04', '2024-10-31 13:40:04'),
(757, 'Rosangela Santos de Souza', 'Física', '662.353.080-00', NULL, '(51)98188-5857', '', 'Marechal Floriano,  ap 304', '810', '', 'Osório', '1981-08-04', 'Migrado do sistema antigo', 1, '2024-11-04 11:44:10', '2024-11-04 11:44:10'),
(758, 'Jadna da Silva Pereira', 'Física', '117.981.729-09', NULL, '48 98863-6380', '', 'Rua Capão da Canoa', '894', 'Courhasa', 'Imbé', '2003-01-17', 'Migrado do sistema antigo', 1, '2024-11-05 20:00:42', '2024-11-05 20:00:42'),
(759, 'Sátira Narinez Costa Garcia', 'Física', '489.863.560-15', NULL, '(51)99600-2903', '', 'Estrada Pico da Borussia', '124', '', 'Osório', '1966-04-17', 'Migrado do sistema antigo', 1, '2024-11-09 12:49:56', '2024-11-09 12:49:56'),
(760, 'Juliano Da Silva Borba', 'Física', '026.140.310-97', NULL, '(51)99790-2424', '', 'Rua Silval Antonio Ribeiro', '624', 'Panoramica', 'Osório', '1990-08-25', 'Migrado do sistema antigo', 1, '2024-11-13 14:15:33', '2024-11-13 14:15:33'),
(761, 'Rafael Duarte', 'Física', '011.430.060-70', NULL, '(55)99969-9504', '', 'Rua Amancio Amaral', 'Ap 301', '', 'Tramandai', '1987-05-19', 'Migrado do sistema antigo', 1, '2024-11-18 17:23:47', '2024-11-18 17:23:47'),
(762, 'Cassia Soares Da Silva', 'Física', '034.347.260-04', NULL, '(51)99622-0167', '', 'Rua Pinheiro Machado', '1880', 'Glória', 'Osório', '1994-07-15', 'Migrado do sistema antigo', 1, '2024-11-21 16:51:40', '2024-11-21 16:51:40'),
(763, 'Maicon Maidana Brum', 'Física', '014.542.4410-4', NULL, '(51)99427-5092', '', 'Av General Osório', '2287', '', 'Osório', '1984-12-04', 'Migrado do sistema antigo', 1, '2024-11-25 11:50:31', '2024-11-25 11:50:31'),
(765, 'Maicon Maidana Brum', 'Física', '015.542.410-48', NULL, '(51)99427-5092', '', 'Av General Osório', '2287', '', 'Osório', '1984-12-04', 'Migrado do sistema antigo', 1, '2024-11-25 11:52:49', '2024-11-25 11:52:49'),
(766, 'Giselle Pereira petrucci da Silva', 'Física', '019.475.680-78', NULL, '(51)99748-0061', '', 'Rua Julio de Castilios', '1290', 'Centro', 'Osório', '1989-08-03', 'Migrado do sistema antigo', 1, '2024-11-25 16:40:26', '2024-11-25 16:40:26'),
(767, 'Justino de Sousa neri Junior', 'Física', '973.437.305-68', NULL, '(71)99630-8386', '', 'Rua Voluntarios da Patria', '233', 'Porto', 'Osório', '1980-10-01', 'Migrado do sistema antigo', 1, '2024-11-26 16:35:03', '2024-11-26 16:35:03'),
(768, 'Matias Pereira de Medeiros', 'Física', '025.112.490-81', NULL, '(51)99813-3109', '', 'Rua Ladislau Vieira', '191', '', 'Osório', '1993-05-03', 'Migrado do sistema antigo', 1, '2024-11-30 12:08:03', '2024-11-30 12:08:03'),
(769, 'Giane Roman Pioner', 'Física', '945.749.440-72', NULL, '(51)99949-5846', '', 'Rua das Palmeira', '227', 'Parque real', 'Osório', '1980-06-26', 'Migrado do sistema antigo', 1, '2024-11-30 12:54:27', '2024-11-30 12:54:27'),
(770, 'Susane Piazzeta Maciel', 'Física', '016.647.320-04', NULL, '(51)98930-2395', '', 'Rua Rainha Ginga Maira Tereza', '630', 'Caravagio', 'Osório', '1989-01-22', 'Migrado do sistema antigo', 1, '2024-12-04 16:21:56', '2024-12-04 16:21:56'),
(771, 'Tiago Adriano Gabatz Langer', 'Física', '018.475.510-76', NULL, '(55)98456-8724', '', 'Getulio Vargas', '1485', 'Centro', 'Osório', '1988-08-08', 'Migrado do sistema antigo', 1, '2024-12-04 19:49:13', '2024-12-04 19:49:13'),
(772, 'Irineu Carmo da Silva', 'Física', '042.940.619-36', NULL, '(51)99898-2053', '', 'Rua Frans Limo Soares dos santos', '2958', 'Costa gama', 'Osório', '1984-08-11', 'Migrado do sistema antigo', 1, '2024-12-05 17:08:50', '2024-12-05 17:08:50'),
(773, 'Araci Gomes Ramos', 'Física', '973.166.360-68', NULL, '(51)99507-3568', '', 'RS 030 Km 89 - Chacara 8511', '', 'Emboaba', 'Osório', '1949-03-29', 'Migrado do sistema antigo', 1, '2024-12-09 17:43:10', '2024-12-09 17:43:10'),
(774, 'Vitor Hugo Silva de Lima', 'Física', '03753965006', NULL, '54999082213', '', 'Rua Marcelino Martins de Freitas', '361', 'pitangas', 'Osório', '1997-09-07', 'Migrado do sistema antigo', 1, '2024-12-09 19:39:35', '2024-12-09 19:39:35'),
(775, 'Aline Rocha Francisco', 'Física', '022.497.990-67', NULL, '(51)99787-4046', '', 'Linha peixoto', '4200', '', 'Osório', '1991-08-11', 'Migrado do sistema antigo', 1, '2024-12-10 13:41:17', '2024-12-10 13:41:17'),
(776, 'Paula da Costa Farias', 'Física', '024.537.060-98', NULL, '(51)98474-0390', '', 'Rua Independencia', '235', 'Sulbrasileiro', 'Osório', '1991-05-10', 'Migrado do sistema antigo', 1, '2024-12-16 11:03:21', '2024-12-16 11:03:21'),
(777, 'Lorenzo Ramon Duarte Meza', 'Física', '070.141.800-10', NULL, '(51)98049-8490', '', 'AV Norte', '415', 'Noiva do Mar', 'Xangri-lá', '1946-08-10', 'Migrado do sistema antigo', 1, '2024-12-19 20:39:21', '2024-12-19 20:39:21'),
(778, 'Rodrigo da Cunha', 'Física', '550.322.650-04', NULL, '(51)99972-1130', '', 'Rua das flores', '6', 'Palmital', 'Osório', '1968-07-17', 'Migrado do sistema antigo', 1, '2025-01-06 12:04:07', '2025-01-06 12:04:07'),
(779, 'Lucas Da Silva Rosner', 'Física', '001.120.330-75', NULL, '(51)98318-3939', '(51)99897-8326', 'Rua Vitoria', '17', 'Caiu do céu', 'Osório', '1983-05-04', 'Migrado do sistema antigo', 1, '2025-01-10 18:55:34', '2025-01-10 18:55:34'),
(780, 'Jean Rocha de Souza', 'Física', '052.787.070-62', NULL, '(51)99620-3278', '', 'Rodovia Domingo Manuel Pires', '13474', 'Palmital', 'Osório', '2006-01-14', 'Migrado do sistema antigo', 1, '2025-01-10 20:12:11', '2025-01-10 20:12:11'),
(781, 'Pedro Eduardo dos Santos Moreira', 'Física', '045.969.290-92', NULL, '(51)99685-9011', '', 'Marechal Floriano', '100', 'Caiu do Ceu', 'Osório', '2001-03-01', 'Migrado do sistema antigo', 1, '2025-01-11 13:35:30', '2025-01-11 13:35:30'),
(782, 'Marino Santos da Silva', 'Física', '447.038.300-78', NULL, '(51)99832-6086', '', 'R. Santa Rosa', '45', 'Serra Mar', 'Osório', '1962-08-30', 'Migrado do sistema antigo', 1, '2025-01-13 19:06:54', '2025-01-13 19:06:54'),
(783, 'Agatha Wastowski Novroth', 'Física', '029.591.810-18', NULL, '(55)99904-6536', '', '15 de Novembro', '765 ap ', 'Centro', 'Osório', '2000-09-17', 'Migrado do sistema antigo', 1, '2025-01-14 14:28:15', '2025-01-14 14:28:15'),
(784, 'Ladis Lisboa', 'Física', '472.481.650-34', NULL, '(51)998046-3626', '', 'Barão do Triunfo', '534', '', 'Osório', '1969-08-30', 'Migrado do sistema antigo', 1, '2025-01-20 14:36:25', '2025-01-20 14:36:25'),
(785, 'Carla freitas', 'Física', '709.930.170-53', NULL, '(51)99131-2142', '', '', '788', '', 'Osório', '1978-02-17', 'Migrado do sistema antigo', 1, '2025-01-20 16:44:39', '2025-01-20 16:44:39'),
(786, 'Fernando da Silva Pires', 'Física', '063.265.650-65', NULL, '(51)98182-4883', '', 'Estrada do Palmital', '11300', 'Palmital', 'Osório', '2006-10-25', 'Migrado do sistema antigo', 1, '2025-01-20 19:59:52', '2025-01-20 19:59:52'),
(787, 'Graziele Martins', 'Física', '016.842.400-26', NULL, '(51)99950-7083', '', 'BR 101', '6705', '', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-01-22 14:21:35', '2025-01-22 14:21:35'),
(788, 'Mirian Raquel da Silva Correa', 'Física', '031.491.230-46', NULL, '(51)99789-2372', '(51)99698-8702', 'Av. Tunel Verde', '593', 'Tunel Verde', 'Balneario Pinhal', '1993-09-21', 'Migrado do sistema antigo', 1, '2025-01-28 18:04:35', '2025-01-28 18:04:35'),
(789, 'Soeli Tereza Nogueira', 'Física', '436.763.550-87', NULL, '(51)98010-3383', '(51)98189-6920', 'Rua Torres', '564', 'Primavera', 'Osório', '1968-01-13', 'Migrado do sistema antigo', 1, '2025-01-29 12:02:54', '2025-01-29 12:02:54'),
(790, 'João Batista Sparrenberger Borges', 'Física', '001.078.870-02', NULL, '(51)98118-4201', '', 'Br 101 Km 81', '', 'Livramento', 'Osório', '1982-11-18', 'Migrado do sistema antigo', 1, '2025-01-29 17:13:02', '2025-01-29 17:13:02'),
(791, 'Cintia Lopes Quintanilha', 'Física', '024.886.540-43', NULL, '(51)99605-5154', '(51)99923-9153', 'Rua Alagoas', '458', 'Tunel Verde', 'Balneario Pinhal', '1993-11-15', 'Migrado do sistema antigo', 1, '2025-01-30 14:15:50', '2025-01-30 14:15:50'),
(792, 'Fernanda Fraga Graboski', 'Física', '046.891.420-09', NULL, '(51)99977-8395', '', 'Rua Palmares do Sul', '731', 'Primavera', 'Osório', '2004-07-03', 'Migrado do sistema antigo', 1, '2025-02-01 12:19:58', '2025-02-01 12:19:58'),
(793, 'Hedigar Eli Gonçalves', 'Física', '028.757.450-42', NULL, '', '', 'Rua Alexandre Renda 160 AP', '102', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-02-08 15:30:39', '2025-02-08 15:30:39'),
(794, 'rafael pereira nielsen', 'Física', '968.465.770-68', NULL, '(51)99647-0984', '', 'rua terra de areia', '267', 'medianeira', 'osório', NULL, 'Migrado do sistema antigo', 1, '2025-02-10 18:07:02', '2025-02-10 18:07:02'),
(795, 'Cristiane Menegali', 'Física', '982.664.460-91', NULL, '(51)98125-6001', '', 'Loja vira vicio', '', 'centro', 'osorio', '1212-12-12', 'Migrado do sistema antigo', 1, '2025-02-10 20:43:26', '2025-02-10 20:43:26'),
(796, 'Rita de Cássia Boeira de Queiróz', 'Física', '693.634.710-04', NULL, '(51)99865-8863', '', 'Rua Arsenílio Pedro de Souza', '334', 'Primavera', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-02-11 12:30:09', '2025-02-11 12:30:09'),
(797, 'Luana Pereira', 'Física', '856.785.890-91', NULL, '(51)98601-0054', '', 'Rua Benjamin Constante', '75', 'Glória', 'Osório', '2008-11-10', 'Migrado do sistema antigo', 1, '2025-02-17 13:06:50', '2025-02-17 13:06:50'),
(798, 'Guilhermi de Maidano Camargo', 'Física', '037.594.050-29', NULL, '(51)99229-3791', '', 'Rua Osvaldo Aranha', '420', 'Bom Fim', 'Osório', '2004-03-11', 'Migrado do sistema antigo', 1, '2025-02-18 18:01:02', '2025-02-18 18:01:02'),
(799, 'Mariane Bertoli', 'Física', '021.767.630-84', NULL, '(51)99272-9942', '', 'Rua Pedrolina Firme', '2988', 'Borússia', 'Osório', '1989-10-29', 'Migrado do sistema antigo', 1, '2025-02-18 20:03:41', '2025-02-18 20:03:41'),
(800, 'Joni Antônio Rodrigues', 'Física', '001.409.484-30', NULL, '(51)99144-3283', '', 'Rua 7 de Setembro', '1497', 'Centro', 'Tramandaí', '1949-02-16', 'Migrado do sistema antigo', 1, '2025-02-20 12:26:36', '2025-02-20 12:26:36'),
(801, 'Francieli Lima Angélico', 'Física', '011.531.330-32', NULL, '(51)99521-8995', '', 'Rua Nelson Silveira de Souza', '963 Ap.', 'Centro', 'Osório', '1991-05-02', 'Migrado do sistema antigo', 1, '2025-02-20 17:20:24', '2025-02-20 17:20:24'),
(802, 'Iasmina Nunes', 'Física', '608.275.050-00', NULL, '(51)99998-0648', '', 'Major João Marques', '771', 'Centro', 'Osório', '1970-10-08', 'Migrado do sistema antigo', 1, '2025-02-20 20:57:44', '2025-02-20 20:57:44'),
(803, 'Gustavo Pena', 'Física', '957.568.330-72', NULL, '(51)99245-9206', '', 'Rua da Lagoa', '1111, c', 'Vila da Serra', 'Osório', '1978-01-02', 'Migrado do sistema antigo', 1, '2025-02-24 13:17:54', '2025-02-24 13:17:54'),
(804, 'Rodrigo Machado', 'Física', '958.402.730-15', NULL, '(51)99757-2005', '', 'Rua 07 de Setembro', '1184', 'Glória', 'Osório', '1978-06-09', 'Migrado do sistema antigo', 1, '2025-02-24 14:15:31', '2025-02-24 14:15:31'),
(805, 'Luis Paulo Fernandes', 'Física', '966.126.490-20', NULL, '(51)99800-4616', '', 'Rua Ceci Bastos', '59', 'Gloria', 'Osório', '1980-07-28', 'Migrado do sistema antigo', 1, '2025-03-05 12:11:10', '2025-03-05 12:11:10'),
(806, 'Bernardo Diehl', 'Física', '005.517.370-50', NULL, '(51)99327-7620', '', 'Getulio Vargas', '1450 / ', 'Centro', 'Osório', '1984-10-09', 'Migrado do sistema antigo', 1, '2025-03-05 16:39:55', '2025-03-05 16:39:55'),
(807, 'Marcio dos Santos Julio', 'Física', '786.599.300-53', NULL, '(51)99551-1978', '', 'Rua estancia da Serra', '73', 'Vila da serra', 'Osório', '1978-05-07', 'Migrado do sistema antigo', 1, '2025-03-06 17:27:55', '2025-03-06 17:27:55'),
(808, 'João Grechi', 'Física', '493.319.650-87', NULL, '(51)99104-7517', '', 'Rua da Igreja', '705', 'Porto Lacustre', 'Osório', '1967-06-23', 'Migrado do sistema antigo', 1, '2025-03-10 12:58:49', '2025-03-10 12:58:49'),
(809, 'Erenice de Oliveira Trisch', 'Física', '007.462.160-25', NULL, '(51)99879-2578', '', 'Rua barão do rio branco', '2090', 'Sul brasileiro', 'Osório', '1985-11-26', 'Migrado do sistema antigo', 1, '2025-03-10 13:18:29', '2025-03-10 13:18:29'),
(810, 'Joarez Barros', 'Física', '171.096.630-00', NULL, '(51)99975-9579', '', 'Condomínio Interlagos', '', '', 'Osório', '1954-04-16', 'Migrado do sistema antigo', 1, '2025-03-11 18:37:36', '2025-03-11 18:37:36'),
(811, 'Tabajar Gross', 'Física', '447.038.050-49', NULL, '(51)99981-5917', '', 'Rua 24 de Maio', '750', 'Centro', 'Osório', '1963-12-10', 'Migrado do sistema antigo', 1, '2025-03-14 12:22:00', '2025-03-14 12:22:00'),
(812, 'Juliana Vargas Palar', 'Física', '026.844.870-14', NULL, '(51)992250-920', '', 'Rua Dr.Nelson Silveira de Souza', '318, ca', 'Caiu do Céu', 'Osório', '1996-09-20', 'Migrado do sistema antigo', 1, '2025-03-19 17:31:27', '2025-03-19 17:31:27'),
(813, 'Fabiano Armichi', 'Física', '020.681.490-95', NULL, '(51)99543-1092', '', 'Rua Terra de Areia', '460', 'Medianeira', 'Osório', '1989-04-30', 'Migrado do sistema antigo', 1, '2025-03-22 11:22:49', '2025-03-22 11:22:49'),
(814, 'Sonia Mara Osio', 'Física', '747.253.190-15', NULL, '(51)98209-8162', '', 'Machado de Assis', '985', 'Centro', 'Osório', '1974-07-20', 'Migrado do sistema antigo', 1, '2025-03-24 16:49:05', '2025-03-24 16:49:05'),
(815, 'Marcieli Dalpiaz', 'Física', '042.921.940-76', NULL, '(51)99608-3163', '', 'Rua Boa Ventura Machado', '909', 'Centro', 'Maquine', '2000-05-05', 'Migrado do sistema antigo', 1, '2025-03-25 11:23:48', '2025-03-25 11:23:48'),
(816, 'Vanderlei Zacher', 'Física', '828.722.280-04', NULL, '(51)99856-8088', '', 'Av. Alvaro Alves Camargo', '728', 'Centro', 'Palmares do Sul', '1982-05-29', 'Migrado do sistema antigo', 1, '2025-03-25 22:10:01', '2025-03-25 22:10:01'),
(817, 'Vagner Staudt', 'Física', '029.368.370-06', NULL, '(55)99647-1779', '', 'Rua Costa Gama', '1023', 'Centro', 'Osório', '1998-07-26', 'Migrado do sistema antigo', 1, '2025-03-26 17:20:16', '2025-03-26 17:20:16'),
(818, 'Claudio Faggionatto de Lima', 'Física', '407.845.090-34', NULL, '(51)99937-9600', '(51)99732-4100', 'Av Independência', '814', 'Independência', 'Porto Alegre', NULL, 'Migrado do sistema antigo', 1, '2025-03-26 18:07:19', '2025-03-26 18:07:19'),
(819, 'Vera Lucia Silveira Soares', 'Física', '451.862.940-04', NULL, '(51)99919-6078', '(51)99151-3036', 'Estrada RS-101 Km 139', '', 'Trilhos', 'Palmares do Sul', NULL, 'Migrado do sistema antigo', 1, '2025-03-27 11:27:28', '2025-03-27 11:27:28'),
(820, 'Ian Souza Garcia', 'Física', '880.025.350-49', NULL, '(51)99362-1435', '', 'Rua 7 de Setembro', '307, ap', 'Centro', 'Osório', '2001-04-15', 'Migrado do sistema antigo', 1, '2025-03-27 17:00:05', '2025-03-27 17:00:05'),
(821, 'Jonas Monticeli da Silva', 'Física', '026.138.070-24', NULL, '(51)99571-3008', '', 'Estrada do Vicente', 'não tem', 'Fraga', 'Caará', '1993-01-24', 'Migrado do sistema antigo', 1, '2025-03-29 11:03:28', '2025-03-29 11:03:28'),
(822, 'Wagner Flor da Silveira', 'Física', '024.038.130-01', NULL, '5199021648', '', 'RS 030 Parada 218', '', '', 'Arroio Grande', '1993-03-03', 'Migrado do sistema antigo', 1, '2025-04-02 18:36:07', '2025-04-02 18:36:07'),
(823, 'Bianca Machado Padilha Braga', 'Física', '034.081.800-08', NULL, '(51)98149-6279', '', 'Rua Firmiano Osório', '9', 'Caiu do Céu', 'Osório', '1994-12-09', 'Migrado do sistema antigo', 1, '2025-04-08 16:48:53', '2025-04-08 16:48:53'),
(824, 'Gizeli Muniz Meregalli', 'Física', '035.509.030-90', NULL, '(51)98166-4528', '', 'Voluntários da pátria', '171', 'Porto Lacustre', 'Osório', '1994-12-17', 'Migrado do sistema antigo', 1, '2025-04-10 12:02:38', '2025-04-10 12:02:38'),
(825, 'Henrique Pinheiro da Rocha', 'Física', '050.018.750-99', NULL, '(51)99930-9176', '', 'Rua Capão da canoa', '510', 'Medianeira', 'Osório', '2005-04-24', 'Migrado do sistema antigo', 1, '2025-04-11 11:47:23', '2025-04-11 11:47:23'),
(826, 'Daiane dos Santos Salgado da Silveira', 'Física', '001.063.440-13', NULL, '(51)99683-2151', '', 'Estrada da Goiabeira', '65', 'Borússia', 'Osório', '1979-09-10', 'Migrado do sistema antigo', 1, '2025-04-11 20:48:14', '2025-04-11 20:48:14'),
(827, 'Enilson Souza Oliveira', 'Física', '002.177.620-26', NULL, '(51)99939-9162', '', 'Rua Garibaldi', '995', 'Glória', 'Osório', '1978-05-02', 'Migrado do sistema antigo', 1, '2025-04-15 16:36:32', '2025-04-15 16:36:32'),
(828, 'Nelson Adroaldo da Silva', 'fisica', '13.838.385-004', '', '(51) 99124-9258', '', 'Sepetiba', '600', 'Centro', 'Atlântida Sul', '1954-08-01', '', 1, '2025-04-15 18:18:22', '2025-04-15 18:18:22'),
(829, 'Ocenira dos Santos Nunes', 'Física', '320.531.000-44', NULL, '(51)99575-9534', '', 'Rua Passinhos', '144', 'Medianeira', 'Osório', '1963-08-16', 'Migrado do sistema antigo', 1, '2025-04-16 16:26:26', '2025-04-16 16:26:26'),
(830, 'Vitória Bittencourt', 'Física', '033.957.860-21', NULL, '(51)99248-0931', '', '16 de Dezembro', '277', 'Centro', 'Osório', '1999-12-04', 'Migrado do sistema antigo', 1, '2025-04-17 18:54:43', '2025-04-17 18:54:43'),
(831, 'Silviane Lima', 'Física', '613.231.440-72', NULL, '(51)99966-0552', '', 'Rua Barão do Rio Branco', '485 Ap ', 'Centro', 'Osório', '1970-05-18', 'Migrado do sistema antigo', 1, '2025-04-22 20:06:32', '2025-04-22 20:06:32'),
(832, 'Lucimar Lessa Guatimosim', 'Física', '447.044.100-72', NULL, '(51)98132-5146', '', 'Major João Marques', '515/05', 'RS', 'Osório', '1966-01-26', 'Migrado do sistema antigo', 1, '2025-04-25 14:02:51', '2025-04-25 14:02:51'),
(833, 'Alberto Cardoso Nekrasius', 'Física', '107.667.738-09', NULL, '(11)97753-8146', '(11)93024-1159', '', '', '', '', '1968-12-21', 'Migrado do sistema antigo', 1, '2025-05-02 13:03:41', '2025-05-02 13:03:41'),
(834, 'Wladimir Ramos Marques', 'Física', '380.882.580-49', NULL, '(51)99683-9631', '', 'Mario Silveira', '890', 'Caiu do Céu', 'Osório', '1961-03-25', 'Migrado do sistema antigo', 1, '2025-05-02 16:56:42', '2025-05-02 16:56:42'),
(835, 'João Rosario', 'Física', '027.047.560-58', NULL, '51 9599-7404', '', 'Rua 24 de Maio', '750', 'Centro', 'Osório', '1999-05-16', 'Migrado do sistema antigo', 1, '2025-05-02 19:32:51', '2025-05-02 19:32:51'),
(836, 'Matheus Nascimento Evangelista', 'Física', '008.491.902-74', NULL, '(51)98126-1249', '', 'Av. Jorge Dariva', '1720', 'Centro', 'Osório', '2004-07-22', 'Migrado do sistema antigo', 1, '2025-05-07 18:49:36', '2025-05-07 18:49:36'),
(837, 'Neimar Pedroso de Oliveira', 'Física', '26741601015', NULL, '(51)98017-1787', '', 'Rua Entre  Lagos ', '255', 'Santa Rita', 'Osório', '1957-07-04', 'Migrado do sistema antigo', 1, '2025-05-07 22:46:14', '2025-05-07 22:46:14'),
(838, 'Gleiciane Machado Matos', 'Física', '85922790030', NULL, '(51)98019-2072', '', 'Rua da Santinha', '100', 'Agasa', 'Santo Antônio da Patrulha', '1999-07-11', 'Migrado do sistema antigo', 1, '2025-05-09 11:43:06', '2025-05-09 11:43:06'),
(839, 'Talison Gonzatto (Escola Adventista)', 'Física', '03286127078', NULL, '(51)99996-1618', '', 'Padre Réus', '270', 'Centro', 'Osório', '1963-06-20', 'Migrado do sistema antigo', 1, '2025-05-09 12:08:05', '2025-05-09 12:08:05'),
(840, 'Janaina de Souza Galvão Monteiro', 'Física', '02278059092', NULL, '(51)99505-7316', '', 'Rua Beco do Leivino', '207', 'Frei Sebastião', 'Palmares do Sul', '1991-04-12', 'Migrado do sistema antigo', 1, '2025-05-13 14:19:30', '2025-05-13 14:19:30'),
(842, 'Renan Paszinski Medeiros', 'Física', '03273447095', NULL, '(51)99643-6037', '', 'Major João Marques', '3860', 'Albatroz', 'Osório', '2003-06-13', 'Migrado do sistema antigo', 1, '2025-05-14 13:24:24', '2025-05-14 13:24:24'),
(843, 'Rafael Cristiano da Rosa', 'Física', '00003696081', NULL, '(51)99855-6400', '', 'Rua Antonio Stenzel', '209', 'Albatroz', 'Osório', '1982-05-23', 'Migrado do sistema antigo', 1, '2025-05-15 14:16:00', '2025-05-15 14:16:00'),
(844, 'Djeser Henrique Nunes Paranhos', 'Física', '02203808055', NULL, '(51)99580-8455', '(51)99495-1284', 'Rua Pinheiro Machado', '507/101', 'Sulbrasileiro', 'Osório', '1991-06-27', 'Migrado do sistema antigo', 1, '2025-05-17 12:03:43', '2025-05-17 12:03:43'),
(845, 'Willian Cristian dos Santos Saucedo', 'Física', '03790893005', NULL, '(51)99177-8173', '', 'BR 101 Km 86', '3815', '', 'Osório', '1994-11-16', 'Migrado do sistema antigo', 1, '2025-06-02 14:23:11', '2025-06-02 14:23:11'),
(846, 'Comercial villa serco (CNPJ05869545000152)', 'Física', '033.910.880-01', NULL, '(51)999700-1317', '', 'Rua dotor Pereira neto', '2201', '', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-06-04 11:43:36', '2025-06-04 11:43:36'),
(847, 'Neda Lopes Duarte', 'Física', '66053005053', NULL, '(51)99870-5047', '', 'Torvalino Jacob', '2474', 'Cotovelo', 'Terra de areia', '1973-04-16', 'Migrado do sistema antigo', 1, '2025-06-14 13:07:16', '2025-06-14 13:07:16'),
(848, 'Cecilia Andrade Guimarães', 'Física', '27968618053', NULL, '(51)99226-0188', '', 'Rua Angra dos Reis', '209', 'Atlantida Sul', 'Osório', '1955-01-30', 'Migrado do sistema antigo', 1, '2025-06-17 18:06:36', '2025-06-17 18:06:36'),
(849, 'Vera Lúcia Evaristo de Souza', 'Física', '16832957572', NULL, '(71)99988-6818', '(71)98730-3302', 'Augusto Frederico Schmith', '139', '', 'Salvador', '1957-12-17', 'Migrado do sistema antigo', 1, '2025-06-25 13:16:05', '2025-06-25 13:16:05'),
(850, 'Tharsis da silva paz', 'Física', '04275182022', NULL, '(51)99900-5927', '', 'Rua Luiz Bernadino da Silva neto', '352', 'Caravaje', 'Osório', '1997-08-18', 'Migrado do sistema antigo', 1, '2025-06-25 16:57:40', '2025-06-25 16:57:40'),
(851, 'Rafael Bemfica Killes', 'Física', '00025693026', NULL, '(51)99897-0689', '', 'Rua Firmiano Osório', '684', 'Centro', 'Osório', '1982-08-25', 'Migrado do sistema antigo', 1, '2025-06-26 17:09:48', '2025-06-26 17:09:48'),
(852, 'Roberto Sessin do Amaral', 'Física', '44342438020', NULL, '(48)99176-5090', '', 'Rua ventos do sul', '1415', 'Serramar', 'Osório', '1994-02-24', 'Migrado do sistema antigo', 1, '2025-06-30 17:43:39', '2025-06-30 17:43:39'),
(853, 'Amauri Somer', 'Física', '48120057015', NULL, '(51)99592-3956', '', 'Rua Major João Marques', '1048', '', 'Osório', '1969-01-28', 'Migrado do sistema antigo', 1, '2025-07-07 13:44:52', '2025-07-07 13:44:52'),
(854, 'Sergio Luis Macedo Da Silva', 'Física', '40893960004', NULL, '(51)99782-6589', '', 'Rua lagoa vermelha', '1558', 'Centro', 'Imbé', '1965-10-02', 'Migrado do sistema antigo', 1, '2025-07-08 19:39:36', '2025-07-08 19:39:36'),
(855, 'Ana Paula Santos dos Santos', 'Física', '02315535018', NULL, '(51)9529-8239', '', 'Valdir Silveira Rangel', '372', 'Laranjeiras', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-07-10 12:07:54', '2025-07-10 12:07:54'),
(856, 'Vitoria Araujo', 'Física', '04346527094', NULL, '(51)99545-4402', '', 'Rua Alzira Sarconi', '93', '', 'Osório', '2008-12-21', 'Migrado do sistema antigo', 1, '2025-07-10 20:39:25', '2025-07-10 20:39:25'),
(857, 'Mariana Hermo', 'Física', '22762255805', NULL, '(54)9116410-510', '', '', '', '', '', NULL, 'Migrado do sistema antigo', 1, '2025-07-14 17:12:42', '2025-07-14 17:12:42'),
(858, 'Bianca Fernandes Canela', 'Física', '08409542986', NULL, '(51)98276-3600', '', 'Getulio Vargas', '2117', 'Parque do Sol', 'Osório/RS', '1993-02-20', 'Migrado do sistema antigo', 1, '2025-07-15 18:50:57', '2025-07-15 18:50:57'),
(859, 'Giana Galimberti ', 'Física', '02141529018', NULL, '(51)99280-7965', '', 'Manoel Marques da Rosa', '601/807', 'Centro', 'Osório/RS', '1989-11-08', 'Migrado do sistema antigo', 1, '2025-07-16 13:05:30', '2025-07-16 13:05:30'),
(860, 'Paulo Rogerio da Silva Quintanilha', 'Física', '38092840091', NULL, '(51)99581-7984', '', 'Rua Osvaldo Bastos', '275', 'Gloria', 'Osório', '1963-05-11', 'Migrado do sistema antigo', 1, '2025-07-18 12:53:50', '2025-07-18 12:53:50'),
(861, 'Nivia Oliveira de Souza', 'Física', '71806776049', NULL, '51 9941-8855', '', 'Rua Deolino Magio', '105', 'Centro', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-07-21 11:43:33', '2025-07-21 11:43:33'),
(862, 'Igor da SIlva Correa', 'Física', '01239357095', NULL, '(51)99709-5826', '', 'Rua 7 de Setemnbro', '', 'Glória', 'Osório', '1985-08-15', 'Migrado do sistema antigo', 1, '2025-07-22 14:28:27', '2025-07-22 14:28:27'),
(863, 'Marcia Graton Medola', 'Física', '00179393057', NULL, '(51)99836-8961', '', 'Rua João Pessoa', '471', 'Porto', 'Osório', '1958-08-18', 'Migrado do sistema antigo', 1, '2025-07-22 16:56:38', '2025-07-22 16:56:38'),
(864, 'Heleno de Borba Santos', 'Física', '90284208000', NULL, '(51)99837-5217', '', 'Av Bosque de albatroz', '1071', '', 'Osório', '1975-01-30', 'Migrado do sistema antigo', 1, '2025-07-22 20:49:52', '2025-07-22 20:49:52'),
(865, 'Raul Agliardi', 'Física', '25662546053', NULL, '(51)98424-7045', '', 'BR 101 Km 86,5', '670', 'Centro', 'Osório', '1955-10-24', 'Migrado do sistema antigo', 1, '2025-07-31 13:05:00', '2025-07-31 13:05:00'),
(866, 'Angélica Gomes de Oliveira', 'Física', '03677301079', NULL, '(51)99396-5413', '', 'R. Major Jon Marques', '1243', 'Sul Brasileiro', 'Osório', '2001-07-06', 'Migrado do sistema antigo', 1, '2025-07-31 18:14:30', '2025-07-31 18:14:30'),
(867, 'Alexandra Paulon', 'Física', '71050337034', NULL, '(51)99945-8883', '', 'Rua Manoel Braz de Lima', '3990', 'Salinas', 'Cidreira', '1972-08-28', 'Migrado do sistema antigo', 1, '2025-08-01 17:08:28', '2025-08-01 17:08:28'),
(868, 'Adriana Calabrezi Colombo Lopes', 'Física', '47077913015', NULL, '(51)99562-7292', '', 'Rua Dr. Joaquim Pozzo Junior', '138', 'Parque do Sol', 'Osório', '1969-11-12', 'Migrado do sistema antigo', 1, '2025-08-05 16:32:40', '2025-08-05 16:32:40'),
(869, 'William Santana Bittencourt', 'Física', '04607769088', NULL, '(51)98033-7780', '', 'Rua Benedito Felix Soarez', '66', 'Caiu do céu', 'Osório', '2005-02-01', 'Migrado do sistema antigo', 1, '2025-08-06 12:00:21', '2025-08-06 12:00:21'),
(870, 'Vanessa Cardoso de Almeida', 'Física', '02857717016', NULL, '(51)9542-9042', '', 'Rua Albatroz', '545', 'Albatroz', 'Osório', '1990-07-07', 'Migrado do sistema antigo', 1, '2025-08-07 11:38:25', '2025-08-07 11:38:25'),
(871, 'Daniela Pedroso Muller', 'Física', '80532101049', NULL, '(54)99905-9758', '', 'BR 101 Km 84', '3810', 'Costa Verde', 'Osório', '1978-11-17', 'Migrado do sistema antigo', 1, '2025-08-07 20:53:06', '2025-08-07 20:53:06'),
(872, 'Luis Carlos Boeira da Silva', 'Física', '67393209004', NULL, '(51)99838-8712', '(51)99922-7073', 'major joao marques', '3718', 'albatroz', 'Osório', '1971-12-14', 'Migrado do sistema antigo', 1, '2025-08-11 12:44:40', '2025-08-11 12:44:40'),
(873, 'Scheila Mendes', 'Física', '00441973060', NULL, '(51)98915-5510', '(51)9123-4732', 'RS-030', '3935', 'Laranjeiras', 'Osório', '1983-03-04', 'Migrado do sistema antigo', 1, '2025-08-12 17:06:08', '2025-08-12 17:06:08'),
(874, 'DÉBORA GOMES DA SILVA ', 'Física', '04518951000', NULL, '+55 51 9791-084', '', ': Avenida Marechal Floriano Peixoto', '1776', '', 'o', NULL, 'Migrado do sistema antigo', 1, '2025-08-13 11:41:08', '2025-08-13 11:41:08'),
(875, 'Scheila De Oliveira Lemes', 'Física', '00471367079', NULL, '(51)9925-1015', '(51)99685-9407', 'Rua Palmares do Sul ', '550', 'Primavera', 'Osório', '1985-01-16', 'Migrado do sistema antigo', 1, '2025-08-13 17:27:10', '2025-08-13 17:27:10'),
(876, 'Sandra Maria Giannastasio', 'Física', '22113592053', NULL, '(51)99961-0341', '(51)99374-3218', 'Estrada Angelina Colombo Da Silveira', '1870', 'Borrúsia', 'Osório', '1948-09-21', 'Migrado do sistema antigo', 1, '2025-08-14 13:20:40', '2025-08-14 13:20:40'),
(877, 'Donel Ulysse', 'Física', '01321254989', NULL, '(51)99528-3198', '', 'Pinheiro Machado', '651', 'Sulbrasileiro', 'Osório', '1968-02-15', 'Migrado do sistema antigo', 1, '2025-08-19 14:59:52', '2025-08-19 14:59:52'),
(878, 'Vera Lucia Pereira Marques', 'Física', '00268956090', NULL, '(51)99807-4946', '(51)99800-6200', 'Leão Rodrigues Madalena', '411', 'Pitangas', 'Osório ', '1970-06-22', 'Migrado do sistema antigo', 1, '2025-08-21 13:29:41', '2025-08-21 13:29:41'),
(879, 'Alex Jeferson Zimmer ', 'Física', '01049331001', NULL, '(51)99721-0075', '', 'Rua Barra do Ouro ', '169', 'Medianeira', 'Osório', '1986-12-31', 'Migrado do sistema antigo', 1, '2025-08-25 18:04:07', '2025-08-25 18:04:07'),
(880, 'Lenadro Lopes Correia', 'Física', '00003030032', NULL, '(51)99681-6388', '', 'Rs 030', '475', 'Laranjeiras', 'Osório', '1981-09-08', 'Migrado do sistema antigo', 1, '2025-08-27 11:26:46', '2025-08-27 11:26:46'),
(881, 'Mateus Oliveira dos Santos', 'Física', '02299256008', NULL, '(51)99860-9095', '', 'Rua gintil', '1030', '', 'Osório', '1988-09-05', 'Migrado do sistema antigo', 1, '2025-09-01 11:11:03', '2025-09-01 11:11:03');
INSERT INTO `clientes` (`id`, `nome_completo`, `tipo_pessoa`, `documento`, `email`, `telefone_principal`, `telefone_secundario`, `endereco_logradouro`, `endereco_numero`, `endereco_bairro`, `endereco_cidade`, `data_nascimento`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(882, 'Nicolas Padilha', 'Física', '03880703078', NULL, '(51)99583-4163', '', 'Av. Costa Gama', '1276', 'Centro', 'Osório', '2004-10-07', 'Migrado do sistema antigo', 1, '2025-09-01 12:41:51', '2025-09-01 12:41:51'),
(883, 'Lúcio Alves dos Santos', 'Física', '03381582003', NULL, '(51)98276-9703', '', '', '95', '', 'Osório', '1997-05-28', 'Migrado do sistema antigo', 1, '2025-09-03 13:55:57', '2025-09-03 13:55:57'),
(884, 'Jovelina Vergara', 'Física', '62399675053', NULL, '(51)98105-1724', '', 'Rua marechal ', '505', 'Centro', 'Osório', '1953-08-24', 'Migrado do sistema antigo', 1, '2025-09-04 19:36:34', '2025-09-04 19:36:34'),
(885, 'Lucia Helena Ferreira', 'Física', '45435847320', NULL, '(85)99407-3321', '', 'Travessa vila borussia', '451', 'Borussia', 'Osorio', '1998-05-26', 'Migrado do sistema antigo', 1, '2025-09-05 12:10:06', '2025-09-05 12:10:06'),
(886, 'Anderson da Silveira Casser', 'Física', '98258524020', NULL, '(51)99665-6321', '', 'Rua Edegar Brum', '30', 'Aguapes', 'Osório', '1980-08-05', 'Migrado do sistema antigo', 1, '2025-09-05 17:01:29', '2025-09-05 17:01:29'),
(887, 'Elisabete Braum da Silva', 'Física', '91573467041', NULL, '(51)99613-3033', '', 'Rua Travessa FOrmagio ', '109', '', 'Osório', '1965-04-26', 'Migrado do sistema antigo', 1, '2025-09-09 11:50:21', '2025-09-09 11:50:21'),
(888, 'Ana Glaucia Tressoldi', 'Física', '46661271000', NULL, '(51)99742-1241', '', 'Francelino Batista Gomes', '6', 'Porto', 'Osório', '1966-09-06', 'Migrado do sistema antigo', 1, '2025-09-10 16:32:15', '2025-09-10 16:32:15'),
(889, 'Marcela Barrufi', 'Física', '93643861087', NULL, '(51)99927-0211', '', 'Rua Anfiloquio marques', '114 sal', '', 'Osório', '1988-11-26', 'Migrado do sistema antigo', 1, '2025-09-10 17:06:10', '2025-09-10 17:06:10'),
(890, 'Margarete Maria Castro', 'Física', '00103866027', NULL, '(51)98666-6298', '(51)99105-1425', 'Reduzino Pacheco, ap 105', '843', 'Centro', 'Osório', '1959-10-15', 'Migrado do sistema antigo', 1, '2025-09-11 14:23:22', '2025-09-11 14:23:22'),
(891, 'Cheila Helena dos Santos', 'Física', '02011055008', NULL, '(51)98208-2168', '', 'Estrada do mar km6', '', 'Varzea do padre', 'Osório', '1990-10-18', 'Migrado do sistema antigo', 1, '2025-09-11 16:31:47', '2025-09-11 16:31:47'),
(892, 'Jucileia Brusch da Silva', 'Física', '03019327008', NULL, '51997336043', '', 'Estrada Invernada', '600', 'Borussia', 'Osório', '1992-11-17', 'Migrado do sistema antigo', 1, '2025-09-11 17:34:32', '2025-09-11 17:34:32'),
(893, 'Michelle Dominique Sant Ana', 'Física', '00591183005', NULL, '(51)99570-4659', '', 'Marechal Teodoro ', '1110', 'Gloria', 'Osorio', '1985-05-30', 'Migrado do sistema antigo', 1, '2025-09-12 16:33:16', '2025-09-12 16:33:16'),
(894, 'Jucelia Nunes Rocha', 'Física', '03958912052', NULL, '51 9787-4636', '', 'BR101 - km 86', '1859', '', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-09-12 20:33:39', '2025-09-12 20:33:39'),
(895, 'Delmar Soares Veiga', 'Física', '44905750091', NULL, '(51)99247-8624', '', 'Alameda Capororoca', '148', 'Bosques do Albatroz', 'Osório', '1964-08-14', 'Migrado do sistema antigo', 1, '2025-09-19 14:59:06', '2025-09-19 14:59:06'),
(896, 'Emily Gabriely', 'Física', '11159436932', NULL, '(47)99221-2023', '', 'Rua tolentino', '1225', 'Caravagio', 'Osório', '2005-02-26', 'Migrado do sistema antigo', 1, '2025-09-19 16:50:00', '2025-09-19 16:50:00'),
(897, 'Eduardo de Oliveira  Fischer', 'Física', '43587771049', NULL, '(51)98315-7047', '', 'Rua barao do rio branco', '443', 'Osorio', 'Centro', '1990-03-08', 'Migrado do sistema antigo', 1, '2025-09-22 17:08:44', '2025-09-22 17:08:44'),
(898, 'Priscila Pinheiro da Silva', 'Física', '02745661094', NULL, '51 983051065', '', 'Rua Capão da Canoa', '900', 'Medianeira', 'Osório', NULL, 'Migrado do sistema antigo', 1, '2025-10-01 11:08:25', '2025-10-01 11:08:25'),
(899, 'Ademar Milk', 'Física', '30935075020', NULL, '(51)99331-8474', '', 'Arãobaia', '301', '', 'Atlântida Sul', '1959-02-02', 'Migrado do sistema antigo', 1, '2025-10-03 18:53:23', '2025-10-03 18:53:23'),
(900, 'Leticia Fraga da Silva', 'Física', '04975833050', NULL, '(51)99581-6542', '', 'RS 030 Km 79', '845', 'Laranjeiras', 'Osório', '2004-04-24', 'Migrado do sistema antigo', 1, '2025-10-09 16:43:29', '2025-10-09 16:43:29'),
(901, 'Yasmin Inchinco Deken', 'Física', '03894217063', NULL, '(51)99995-8470', '', 'Rua ventos do sul', '37', 'Serra Mar', 'Osorio', '2006-12-12', 'Migrado do sistema antigo', 1, '2025-10-10 20:36:46', '2025-10-10 20:36:46'),
(902, 'Pedro ', 'Física', '04353807021', NULL, '(51)99532-3261', '', 'Rua 16 de dezembro', '63', '', 'Osório', '1999-10-12', 'Migrado do sistema antigo', 1, '2025-10-14 11:30:41', '2025-10-14 11:30:41'),
(903, 'Cristiano Fideliz', 'Física', '02083643070', NULL, '(51)98551-2009', '', 'Rua Machado de Assis', '1612', '', 'Osório', '1987-11-14', 'Migrado do sistema antigo', 1, '2025-10-22 16:53:12', '2025-10-22 16:53:12'),
(904, 'Vitória Camargo da silva motta', 'Física', '04830275022', NULL, '(51)99600-6338', '', 'Recanto do paraiso rs', '', '', '', '2003-04-05', 'Migrado do sistema antigo', 1, '2025-10-24 17:06:50', '2025-10-24 17:06:50'),
(905, 'Hiago Augustinho Borges', 'Física', '04340054011', NULL, '(51)99739-4574', '', 'Rua virgilino', '1365', 'Caravagio', 'Osório', '2006-01-02', 'Migrado do sistema antigo', 1, '2025-10-28 11:50:10', '2025-10-28 11:50:10'),
(906, 'Manuela Braga do Santos Alves', 'Física', '04718442095', NULL, '(51)99601-2018', '', 'a', 'a', 'a', 'a', '1990-11-11', 'Migrado do sistema antigo', 1, '2025-10-31 13:21:54', '2025-10-31 13:21:54'),
(907, 'Gian Gaspar Ribeiro', 'Física', '04211342088', NULL, '(51)9791-3951', '', 'Rua Emilio francisco da silva', 'casa72', '', 'Osório', '2001-11-28', 'Migrado do sistema antigo', 1, '2025-11-01 11:24:51', '2025-11-01 11:24:51'),
(908, 'Anna Caroline Nunes dos Santos', 'Física', '01489140042', NULL, '(51)99934-0080', '', 'BR 101 (Antiga Unesul)', '1100', 'Centro', 'Osório', '1995-02-25', 'Migrado do sistema antigo', 1, '2025-11-03 18:51:34', '2025-11-03 18:51:34'),
(909, 'Chiaki Sugimoto', 'Física', '18638155034', NULL, '(51)99649-2125', '', 'RUa voluntarios da patria', '445', 'Porto lacustre', 'Osorio', '1952-10-04', 'Migrado do sistema antigo', 1, '2025-11-03 20:13:33', '2025-11-03 20:13:33'),
(910, 'Gilmar Antonio Scalco', 'Física', '13320386034', NULL, '(51)99193-2929', '', 'Rua Victorino P. Batista', '100', 'Caiu do Ceu', 'Osório', '1952-09-05', 'Migrado do sistema antigo', 1, '2025-11-06 18:41:09', '2025-11-06 18:41:09'),
(911, 'Dimas Mombach de Oliueira', 'Física', '00263738078', NULL, '(51)99339-0369', '', 'Estada Municipal Pereira neto', '1510', 'Passinhos', 'Osório', '1983-12-30', 'Migrado do sistema antigo', 1, '2025-11-11 19:52:55', '2025-11-11 19:52:55'),
(912, 'Rok Martins Moro cnpj 41741152000172', 'Física', '06865606981', NULL, '(48)99143-2191', '', 'Passinhos', '2141', '', 'Osório', '1987-09-03', 'Migrado do sistema antigo', 1, '2025-11-17 13:30:51', '2025-11-17 13:30:51'),
(913, 'Guilherme Scherer', 'Física', '03830449011', NULL, '(51)99789-6090', '', 'Rua Lony Batista da Silva', '443', 'Caiu do Ceu', 'Osório', '2006-07-18', 'Migrado do sistema antigo', 1, '2025-11-19 21:37:09', '2025-11-19 21:37:09'),
(914, 'Muhammad Rizwan Sarwar', 'Física', '23721166817', NULL, '(51)99526-9047', '(51)99557-2606', 'Rua Leoncio Luiz MArques ', '200', 'Caiu do céu', 'Osório', '1987-02-12', 'Migrado do sistema antigo', 1, '2025-11-25 17:41:40', '2025-11-25 17:41:40'),
(915, 'Raiane dos Santos Yeixeira', 'Física', '03009906005', NULL, '51 9202-3345', '', 'Rs 030 km 79 ', '210', '', 'Osórioo', '1991-05-13', 'Migrado do sistema antigo', 1, '2025-12-02 12:51:01', '2025-12-02 12:51:01'),
(916, 'Fabio Cristiano Marisco Cardoso', 'Física', '70246360097', NULL, '(51)99381-4766', '(51)99369-8374', 'Joao SArmento', ' 697', 'Centro', 'Osório', '1972-11-06', 'Migrado do sistema antigo', 1, '2025-12-02 19:07:42', '2025-12-02 19:07:42'),
(917, 'Rossini Duarte Model', 'Física', '01763968022', NULL, '(51)99545-6489', '', 'Rua costa Gama', '1427 ap', '', 'Osório', '1987-07-01', 'Migrado do sistema antigo', 1, '2025-12-03 13:20:20', '2025-12-03 13:20:20'),
(918, 'Luisa Oliveira de Souza', 'Física', '06300682080', NULL, '(51)99987-1168', '', 'Rua Parana ', '144', '', 'Capivari', '2006-01-07', 'Migrado do sistema antigo', 1, '2025-12-09 14:43:15', '2025-12-09 14:43:15'),
(919, 'Sidnei Oliveira Maciel', 'Física', '50486659020', NULL, '(51)99906-8980', '(51)3187-0666', 'BR 101 KM 83,8', '', '', 'Osorio', '1967-08-25', 'Migrado do sistema antigo', 1, '2025-12-10 17:50:03', '2025-12-10 17:50:03'),
(920, 'Inacio José Reinehr', 'Física', '59982144049', NULL, '(54)98119-0417', '(51)99793-8201', 'R. das Enseadas', ' 196', 'Atlântida do Sul', 'Osório', '1949-01-30', 'Migrado do sistema antigo', 1, '2025-12-12 13:01:01', '2025-12-12 13:01:01'),
(921, 'Marilene Gomes Blank', 'Física', '60717211053', NULL, '(61)99657-9747', '(51)99918-8070', 'Almeda da figueira  - COndominio interlagos', '508', '', 'Osorio', '1969-10-18', 'Migrado do sistema antigo', 1, '2025-12-12 13:35:11', '2025-12-12 13:35:11'),
(922, 'David Agliardi Monticelli (Restaurante Monticelli)', 'Física', '01754636039', NULL, '(51)99543-8181', '', 'Rua 7 de Setembro', '162/22', 'Centro', 'Osório', '2002-11-28', 'Migrado do sistema antigo', 1, '2025-12-18 13:34:45', '2025-12-18 13:34:45'),
(923, 'Matheus Abrozzi Borges', 'fisica', '03.969.910-099', '', '(51) 99987-7413', '', 'Rua Capão da Canoa', '816', 'Primaverera', 'Osório', '1996-06-14', '', 1, '2026-01-05 19:15:25', NULL),
(924, 'Guilherme Neto', 'fisica', '02.540.413-021', '', '(51) 99808-2804', '', '', '', '', '', NULL, 'Dono da Ballon Em frente a Rodoviária Velha', 1, '2026-01-07 11:26:07', NULL),
(925, 'Scheila Simone Nunes Espindola', 'fisica', '65.154.894-049', 'scheilapsico@hotmail.com', '(51) 98502-9379', '', 'Rua Tiradentes', '830', 'Gloria', 'Osório', NULL, '', 1, '2026-01-07 17:45:58', NULL),
(926, 'Dr.morozini Primoir de Camargo Morozini', 'juridica', '03.612.458/0001-90', '', '(51) 8441-1719', '(51) 3663-1719', ' R. Machado de Assis, 407', '407', 'Centro', 'Osorio', NULL, 'Clinica de radiologia, cuidamos dos computadores, notebooks e impressoras', 1, '2026-01-08 17:00:59', NULL),
(927, 'Plural', 'juridica', '48.748.704/0001-61', '', '(51) 99567-0082', '', '', '', '', '', NULL, '', 1, '2026-01-08 19:46:42', NULL),
(928, 'Elsa Teresinha de Oliveira', 'fisica', '22.900.578-000', '', '(51) 99133-1844', '', 'Rua Bento Gonçalves', '270', 'Caio do Seu', 'Osório', '1952-10-20', 'Deixou a maleta com tudo do netebook', 1, '2026-01-10 12:00:08', NULL),
(929, 'Myranda', 'juridica', '13.558.678/0001-36', '', '', '', 'Teste', '123', 'Centro', 'Osório', NULL, '', 1, '2026-01-11 23:24:34', NULL),
(930, 'Vester Confeccoes Ltda', 'juridica', '04.230.838/0001-22', '', '', '', 'Rua Santos Dumont ', '2814', 'Albatroz', 'Osório', NULL, 'Dono da Empresa: Orivaldo e Juci, Responsável : Renato', 1, '2026-01-11 23:31:05', NULL),
(931, 'ITAPEVA COMERCIAL AGRICOLA LTDA', 'fisica', '00.088.648/0001-61', 'itapeva@terra.com.br', '', '', 'RS 30', '', 'Laranjeiras', 'Osório', '1994-06-13', '', 1, '2026-01-11 23:40:13', NULL),
(932, 'Sandro da Silveira Niederauer', 'juridica', '27.672.274/0001-83', '', '', '', '', '', '', '', NULL, '', 1, '2026-01-13 20:12:07', NULL),
(933, 'Monice Vargas Marques', 'fisica', '00.460.519-026', 'monicevargas1@gmail.com', '(51) 99610-4816', '', 'Rua Conego Pedro', '345', 'Caravajo', 'Osório', NULL, 'O filho foi aluno aqui. ', 1, '2026-01-14 18:53:36', NULL),
(934, 'Pastelaria Litorial (Deise)', 'juridica', '07.916.808/0001-62', '', '(51) 99955-4167', '', 'Sentido Ida para Praia, Ao lado doces maquine.', '', '', 'Ośorio', NULL, '', 1, '2026-01-15 13:50:34', NULL),
(935, 'Tania Machado Lutz', 'fisica', '02.407.651-090', 'taniaaliceeamanda@yahoo.com', '(51) 99617-8866', '', 'Rua Constituição', '815', 'Glória', 'Osório', NULL, '', 1, '2026-01-16 11:12:58', NULL),
(936, 'Elizeu Selamar Barbosa Terra', 'fisica', '00.400.421-038', 'elizeuterravig@gmail.com', '(51) 99240-1197', '', 'Rua Osvaldo Passinhos', '3140', 'Granja Vargas', 'Osório', '1980-09-09', '', 1, '2026-01-19 13:06:44', NULL),
(937, 'Clinica Odontológica Implantes', 'juridica', '14.132.404/0001-43', '', '(51) 8400-7588', '', 'AV JORGE DARIVA', '1153/sala ', 'Centro', 'Osório', NULL, '', 1, '2026-01-19 17:09:14', NULL),
(938, 'Pastelaria Litoral Robson', 'juridica', '0.791.680/8000-62', '', '', '', 'Volta da Praia', '', '', 'Osório', NULL, '', 1, '2026-01-20 12:44:58', NULL),
(939, 'Fernanda Borges Santos', 'fisica', '68.379.323-068', '', '(51) 99366-0504', '', 'Rua alvita Alves de oliveira', '62', 'Parque do Sol', 'Osório', '1970-05-17', '', 1, '2026-01-20 13:15:34', NULL),
(940, 'Lima Lucas Administradora de Imóveis Ltda ', 'juridica', '18.739.877/0001-62', '', '', '', '', '', '', 'Osório', NULL, '', 1, '2026-01-20 13:50:11', NULL),
(941, 'Robinson Silvani Pacheco', 'fisica', '00.168.034-000', '', '(51) 98049-4572', '', 'Rua 7 de setembro apt 401', '672', 'Centro', 'Osorio', '1981-01-14', '', 1, '2026-01-20 14:21:01', NULL),
(942, 'Paulo Valmor Giacomelli', 'fisica', '23.455.691-072', '', '(51) 99237-0112', '', 'Rua Sewte de Setewmbro  Sala 408', '385', 'Centro', 'Osorio', '0000-00-00', '', 1, '2026-01-20 17:17:00', NULL),
(943, 'Filipe Vitola Peixoto', 'fisica', '02.884.476-032', 'filipevpeixoto@hotmail.com', '(51) 98130-4060', '', 'Rua Barão do Rio Branco', '770', 'Centro', 'Osório', NULL, '', 1, '2026-01-20 19:42:34', NULL),
(944, 'Escola Albatroz', 'juridica', '', '', '', '', 'Santos Dumont', '', '', 'Osório', NULL, 'Aline Diretora, Anildo Contabilidade', 1, '2026-01-21 12:31:20', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_gerais`
--

CREATE TABLE `configuracoes_gerais` (
  `id` int NOT NULL,
  `chave` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` longtext COLLATE utf8mb4_unicode_ci,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes_gerais`
--

INSERT INTO `configuracoes_gerais` (`id`, `chave`, `valor`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 'porcentagem_venda', '60', 'Percentual aplicado sobre o custo para calcular o valor de venda', '2025-12-29 16:21:36', '2026-01-06 20:19:38'),
(16, 'pagamentos_config', '{\"maquinas\":[{\"nome\":\"TOM\",\"habilitada\":true,\"formas\":[\"debito\",\"credito\",\"pix\"],\"bandeiras\":[\"Visa\",\"Mastercard\",\"Elo\",\"American Express\"],\"debito_grupos\":{\"visa_master\":1.4,\"elo_amex\":2.69},\"credito_taxas\":{\"visa_master\":{\"1\":3.3,\"2\":7.37,\"3\":8.04,\"4\":8.92,\"5\":9.79,\"6\":11.87,\"7\":12.87,\"8\":13.05,\"9\":13.06,\"10\":13.07,\"11\":13.08,\"12\":13.09},\"elo_amex\":{\"1\":4.59,\"2\":8.81,\"3\":9.48,\"4\":10.36,\"5\":11.23,\"6\":13.31,\"7\":14.31,\"8\":14.49,\"9\":14.5,\"10\":14.66,\"11\":15.33,\"12\":15.38}}},{\"nome\":\"Mercado Pago\",\"habilitada\":false,\"formas\":[\"debito\",\"credito\",\"pix\"],\"bandeiras\":[\"Visa\",\"Mastercard\",\"Elo\",\"American Express\"],\"debito_grupos\":{\"visa_master\":null,\"elo_amex\":null},\"credito_taxas\":{\"visa_master\":[],\"elo_amex\":[]}},{\"nome\":\"Moderninha\",\"habilitada\":false,\"formas\":[\"debito\",\"credito\",\"pix\"],\"bandeiras\":[\"Visa\",\"Mastercard\",\"Elo\",\"American Express\"],\"debito_grupos\":{\"visa_master\":null,\"elo_amex\":null},\"credito_taxas\":{\"visa_master\":[],\"elo_amex\":[]}}]}', 'Configuração de máquinas, bandeiras e taxas de pagamento (JSON)', '2026-01-22 12:31:41', '2026-01-22 13:07:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `despesas`
--

CREATE TABLE `despesas` (
  `id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `data_despesa` date NOT NULL,
  `status_pagamento` enum('pendente','pago','parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `metodo_pagamento` enum('dinheiro','cartao','pix','transferencia','boleto','outro') COLLATE utf8mb4_unicode_ci DEFAULT 'outro',
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `despesas`
--

INSERT INTO `despesas` (`id`, `categoria_id`, `usuario_id`, `descricao`, `valor`, `data_despesa`, `status_pagamento`, `metodo_pagamento`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Positivo', 2000.00, '2026-01-27', 'pago', 'outro', '', 1, '2026-01-28 00:53:39', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `despesas_categorias`
--

CREATE TABLE `despesas_categorias` (
  `id` int NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `despesas_categorias`
--

INSERT INTO `despesas_categorias` (`id`, `nome`, `descricao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Drogas', NULL, 1, '2026-01-28 00:53:39', NULL);

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
(1, 1, NULL, 'Notebook', 'Acer', '2154CM', '45sdfg56dfg6', 'LULAS2', '', 1, NULL, 1),
(2, 65, NULL, 'Notebook', 'Acer', '15m5', '', '', 'deixou pen drive', 0, NULL, 1),
(3, 336, NULL, 'Notebook', 'acer', 'acer', '232323', '232323', 'mouse', 1, NULL, 1),
(4, 92, NULL, 'Notebook', 'Dell', 'Dell', '65HWFV2', '', 'Nenhum', 0, NULL, 1),
(5, 923, NULL, 'Desktop', 'Rise', '', '', '1406', '', 0, NULL, 1),
(6, 92, NULL, 'Notebook', 'Dell', 'P75F', '4W3QD53', '1711', 'SERIAL FONTE - BR 00RRYYY', 1, NULL, 1),
(7, 924, NULL, 'Desktop', 'C3TECh', '', '', '', 'Usa no 220w', 0, NULL, 1),
(8, 737, NULL, 'Desktop', 'T-Force', '', '', '', '', 0, NULL, 1),
(9, 925, NULL, 'Notebook', 'HP', 'HP G4', 'BRQ204FX4B', '', 'SN fonte: f12921108045186 , paninho da tela', 1, NULL, 1),
(10, 926, NULL, 'Impressora', 'Epson', 'L3210', '', '', 'Cabo fonte', 0, NULL, 1),
(11, 926, NULL, 'Impressora', 'Epson', 'L380', '', '', 'Cabo de força e usb', 0, NULL, 1),
(12, 927, NULL, 'Desktop', 'C3 TECH', 'Repção', '', '', '', 0, '', 1),
(13, 927, NULL, 'All in One', 'LG', 'Sala MED', '', '', 'Fonte', 1, 'BB20-SO19-B2', 1),
(14, 927, NULL, 'Desktop', 'SEM MARCA', 'Auditório', '', '', 'Usa no 220w', 0, '', 1),
(15, 828, NULL, 'Notebook', 'VSAP', '', 'VNJH1402', '4274', 'DEIXOU 2 MOUSE PARA CONECTAR', 1, 'SO241D1200VE', 1),
(16, 912, NULL, 'Notebook', 'Dell', '', '', '', '', 1, '', 1),
(17, 928, NULL, 'Notebook', 'Italtec', '', '407459777111', '', 'Maleta', 1, '', 1),
(18, 932, NULL, 'Desktop', 'PC Antigo Sandro', '', '', '', '', 0, '', 1),
(19, 932, NULL, 'Desktop', 'PC 001', 'MYRANDA', '', '', 'Computador do meio', 0, '', 1),
(20, 923, NULL, 'Outro', 'PEN DRIVE', 'MYRANDA', '', '', '', 0, '', 1),
(21, 927, NULL, 'Desktop', 'PC Sala de Baixo', '', '', '', '', 0, '', 1),
(22, 933, NULL, 'Desktop', 'Kabum F5', '', '', '160124', '', 0, '220', 1),
(23, 934, NULL, 'Notebook', 'Samsung ', 'np7300ED', '', '1727', '', 0, '', 1),
(24, 935, NULL, 'Notebook', 'Positivo', 'Motion Red Q464C', '4A9698D35', 'Nao lembra', '', 0, '', 1),
(25, 428, NULL, 'Impressora', 'HP', '', 'BRGF6D6V092', '', '', 0, '', 1),
(26, 936, NULL, 'Notebook', 'Positivo Stilo', 'xc3634', '4A4122T3W', '', '', 0, '', 1),
(27, 936, NULL, 'Desktop', 'HP', 'Compaq', 'BRG042FYMB', '', '', 0, '220w', 1),
(28, 937, NULL, 'Desktop', '', 'Financeiro', '', '', '', 0, '', 1),
(29, 938, NULL, 'Desktop', 'Myranda Caixa 01', '', '', '', '', 0, '', 1),
(30, 939, NULL, 'Monitor', 'AOC', '', 'TI9AABNQ6WA1A001746', '', '', 0, '', 1),
(31, 939, NULL, 'Monitor', 'Samsung', '', '', '', '', 0, '', 1),
(32, 941, NULL, 'Notebook', 'Lenovo', 'Ideapad 330', '', '', '', 1, '8s54', 1),
(33, 935, NULL, 'Notebook', 'ASUS VIVIBOOK', '', 'MO2480', '041223', '', 0, '', 1),
(34, 942, NULL, 'Notebook', 'Acer', '', '', '0001', '', 0, '', 1),
(35, 943, NULL, 'Notebook', 'Lenovo ', 'Gamming 3 ', '15imm05', '135790', 'Acompanha base teclado + touch oad + teclado ', 1, 'adl135nlc3a', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_ordem_servico`
--

CREATE TABLE `itens_ordem_servico` (
  `id` int NOT NULL,
  `ordem_servico_id` int DEFAULT NULL,
  `atendimento_externo_id` int DEFAULT NULL,
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
  `desconto` decimal(10,2) DEFAULT '0.00',
  `porcentagem_margem` decimal(10,2) DEFAULT '0.00',
  `comprar_peca` tinyint(1) DEFAULT '0',
  `link_fornecedor` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_ordem_servico`
--

INSERT INTO `itens_ordem_servico` (`id`, `ordem_servico_id`, `atendimento_externo_id`, `tipo_item`, `descricao`, `quantidade`, `custo`, `valor_unitario`, `valor_total`, `status`, `ativo`, `created_at`, `valor_custo`, `valor_mao_de_obra`, `desconto`, `porcentagem_margem`, `comprar_peca`, `link_fornecedor`) VALUES
(1, 11, NULL, 'produto', 'SSD 240ddddd', 1.00, 150.00, 225.00, 515.00, 'pendente', 1, '2026-01-02 03:50:33', 0.00, 290.00, 0.00, 0.00, 0, NULL),
(2, 11, NULL, 'servico', 'FORMATAçãO', 1.00, 0.00, 220.00, 220.00, 'pendente', 0, '2026-01-02 03:50:47', 0.00, 0.00, 0.00, 0.00, 0, NULL),
(3, 11, NULL, 'servico', 'FORMATAçãO', 1.00, 0.00, 220.00, 220.00, 'pendente', 0, '2026-01-02 03:51:24', 0.00, 0.00, 0.00, 0.00, 0, NULL),
(4, 11, NULL, 'servico', 'FORMATAçãO', 1.00, 0.00, 220.00, 220.00, 'pendente', 1, '2026-01-02 04:13:21', 0.00, 0.00, 0.00, 0.00, 0, ''),
(5, 11, NULL, 'produto', 'FONTE', 1.00, 95.00, 142.50, 172.50, 'pendente', 1, '2026-01-02 04:20:17', 0.00, 30.00, 0.00, 0.00, 0, ''),
(6, 11, NULL, 'produto', 'SSD 240', 1.00, 200.00, 300.00, 300.00, 'pendente', 1, '2026-01-02 17:48:17', 0.00, 0.00, 0.00, 0.00, 1, ''),
(7, 12, NULL, 'produto', 'SSD 240', 1.00, 1800.00, 2790.00, 3390.00, 'pendente', 1, '2026-01-03 02:13:22', 0.00, 600.00, 0.00, 0.00, 0, ''),
(8, 12, NULL, 'servico', 'FORMATAçãO', 1.00, 0.00, 780.00, 780.00, 'pendente', 1, '2026-01-03 02:14:09', 0.00, 0.00, 0.00, 0.00, 0, ''),
(9, 12, NULL, 'produto', 'FONTE', 1.00, 800.00, 1240.00, 1270.00, 'pendente', 1, '2026-01-03 02:14:21', 0.00, 30.00, 0.00, 0.00, 0, ''),
(10, 12, NULL, 'servico', 'Troca de teclado notebook', 1.00, 200.00, 0.00, 0.00, 'pendente', 0, '2026-01-03 02:22:54', 0.00, 0.00, 0.00, 0.00, 0, ''),
(11, 12, NULL, 'servico', 'Troca de teclado notebook', 1.00, 200.00, 0.00, 0.00, 'pendente', 0, '2026-01-03 02:22:55', 0.00, 0.00, 0.00, 0.00, 0, ''),
(12, 12, NULL, 'servico', 'Formatacao sistema operacional', 1.00, 0.00, 1200.00, 1200.00, 'pendente', 1, '2026-01-03 02:23:14', 0.00, 0.00, 0.00, 0.00, 0, ''),
(13, 12, NULL, 'servico', 'Limpeza interna computador', 1.00, 0.00, 1800.00, 1800.00, 'pendente', 1, '2026-01-03 02:24:37', 0.00, 0.00, 0.00, 0.00, 0, ''),
(14, 12, NULL, 'produto', 'Fonte ATX 200 W', 17.00, 100.00, 0.00, 0.00, 'pendente', 1, '2026-01-04 01:14:27', 0.00, 0.00, 0.00, 0.00, 0, ''),
(15, 2002, NULL, 'servico', 'Formatacao sistema operacional', 1.00, 0.00, 190.00, 190.00, 'pendente', 0, '2026-01-05 18:22:15', 0.00, 0.00, 0.00, 0.00, 0, ''),
(16, 2002, NULL, 'servico', 'Troca de teclado notebook', 1.00, 100.00, 200.00, 350.00, 'pendente', 0, '2026-01-05 18:24:14', 0.00, 150.00, 0.00, 0.00, 1, 'https://os.myrandainformatica.com.br/ordens/view?id=2002'),
(17, 2005, NULL, 'produto', 'SSD SATA 240 GB', 1.00, 210.00, 340.00, 499.00, 'pendente', 1, '2026-01-06 20:05:28', 0.00, 159.00, 0.00, 0.00, 0, ''),
(18, 2005, NULL, 'servico', 'Limpeza interna notebook', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-06 20:06:27', 0.00, 200.00, 0.00, 0.00, 0, ''),
(19, 2003, NULL, 'servico', 'Atualizacao de software', 1.00, 0.00, 0.00, 150.00, 'pendente', 1, '2026-01-06 20:08:29', 0.00, 150.00, 0.00, 0.00, 0, ''),
(20, 2003, NULL, 'servico', 'Limpeza interna notebook', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-06 20:08:55', 0.00, 200.00, 0.00, 0.00, 0, ''),
(21, 2003, NULL, 'produto', 'Memoria DDR4 8 GB', 1.00, 320.00, 520.00, 520.00, 'pendente', 1, '2026-01-06 20:16:41', 0.00, 0.00, 0.00, 0.00, 0, ''),
(22, 2004, NULL, 'servico', 'Laudo técnico', 1.00, 0.00, 0.00, 100.00, 'pendente', 0, '2026-01-06 20:20:34', 0.00, 100.00, 0.00, 0.00, 0, ''),
(23, 2004, NULL, 'servico', 'Laudo técnico', 1.00, 0.00, 0.00, 100.00, 'pendente', 1, '2026-01-06 20:23:08', 0.00, 100.00, 0.00, 0.00, 0, ''),
(24, 2005, NULL, 'produto', 'Bateria Notebook', 1.00, 200.00, 399.90, 399.90, 'pendente', 1, '2026-01-06 20:27:10', 0.00, 0.00, 0.00, 0.00, 0, ''),
(25, 2006, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-07 17:00:39', 0.00, 190.00, 0.00, 0.00, 0, ''),
(26, 2006, NULL, 'servico', 'DESLOCAMENTO', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-07 17:05:58', 0.00, 0.00, 0.00, 0.00, 0, ''),
(27, 2006, NULL, 'produto', 'Fonte ATX 200 W', 1.00, 47.00, 130.00, 130.00, 'pendente', 1, '2026-01-07 17:07:43', 0.00, 0.00, 0.00, 0.00, 0, ''),
(28, 2009, NULL, 'produto', 'FONTE IMPRESSORA', 1.00, 70.00, 290.00, 390.00, 'pendente', 0, '2026-01-08 17:33:03', 0.00, 100.00, 0.00, 0.00, 0, ''),
(29, 2009, NULL, 'produto', 'FONTE IMPRESSORA', 1.00, 70.00, 290.00, 390.00, 'pendente', 0, '2026-01-08 17:33:03', 0.00, 100.00, 0.00, 0.00, 0, ''),
(30, 2009, NULL, 'produto', 'FONTE IMPRESSORA', 1.00, 70.00, 290.00, 390.00, 'pendente', 1, '2026-01-08 17:34:14', 0.00, 100.00, 0.00, 0.00, 1, 'https://www.mercadolivre.com.br/fonte-epson-l3210-l3250-l1250-3210-3250-original-nova/up/MLBU1457141359?pdp_filters=item_id:MLB3790600319#is_advertising=true&searchVariation=MLBU1457141359&backend_model=search-backend&position=1&search_layout=grid&type=pad&tracking_id=ecb8f91f-2471-4891-b439-de540e08f697&ad_domain=VQCATCORE_LST&ad_position=1&ad_click_id=Yjk3YzAxZmYtNTM3YS00NjFiLTk3MWEtMDgzZjVjZjdkZWE0'),
(31, 2010, NULL, 'produto', 'FONTE IMPRESSORA', 1.00, 90.00, 290.00, 390.00, 'pendente', 1, '2026-01-08 17:34:49', 0.00, 100.00, 0.00, 0.00, 1, 'https://www.mercadolivre.com.br/fonte-impressora-l355-l365-l375-l395-l380-l396-original/up/MLBU3345160228?pdp_filters=item_id:MLB5540631570#is_advertising=true&searchVariation=MLBU3345160228&backend_model=search-backend&position=1&search_layout=grid&type=pad&tracking_id=054aece6-2706-4c98-8fd5-1a58b5675416&ad_domain=VQCATCORE_LST&ad_position=1&ad_click_id=N2Y0M2FjMzYtZGI2OS00MGE3LWEwNTQtMjFlM2E1M2M0NThm'),
(32, 2010, NULL, 'servico', 'Limpeza interna impressora', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-08 17:35:11', 0.00, 200.00, 0.00, 0.00, 0, ''),
(33, 2009, NULL, 'servico', 'Limpeza interna impressora', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-08 17:35:51', 0.00, 200.00, 0.00, 0.00, 0, ''),
(34, 2010, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 0, '2026-01-08 19:09:31', 0.00, 190.00, 0.00, 0.00, 0, ''),
(35, 2005, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 0, '2026-01-08 19:13:09', 0.00, 190.00, 0.00, 0.00, 1, 'https://os.myrandainformatica.com.br/ordens/view?id=2005'),
(36, 2011, NULL, 'produto', 'Fonte ATX 200 W', 1.00, 50.00, 200.00, 260.00, 'pendente', 1, '2026-01-08 19:47:58', 0.00, 100.00, 40.00, 0.00, 0, ''),
(37, 2007, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 250.00, 'pendente', 1, '2026-01-10 12:35:39', 0.00, 250.00, 0.00, 0.00, 0, ''),
(38, 2015, NULL, 'servico', 'Limpeza interna notebook', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-12 11:17:43', 0.00, 200.00, 0.00, 0.00, 0, ''),
(39, 2015, NULL, 'servico', 'Atualização do Sistema', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-12 11:18:07', 0.00, 190.00, 0.00, 0.00, 0, ''),
(40, 2012, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-12 11:20:17', 0.00, 230.00, 30.00, 0.00, 0, ''),
(41, 2012, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-12 11:20:27', 0.00, 250.00, 60.00, 0.00, 0, ''),
(42, 2013, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 200.00, 'pendente', 0, '2026-01-12 11:21:11', 0.00, 200.00, 0.00, 0.00, 0, ''),
(43, 2013, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 0, '2026-01-12 11:21:21', 0.00, 190.00, 0.00, 0.00, 0, ''),
(44, 2016, NULL, 'servico', 'Limpeza interna notebook', 1.00, 0.00, 0.00, 150.00, 'pendente', 1, '2026-01-12 14:00:50', 0.00, 150.00, 0.00, 0.00, 0, ''),
(45, 2016, NULL, 'servico', 'Atualização do Sistema', 1.00, 0.00, 0.00, 150.00, 'pendente', 1, '2026-01-12 14:01:10', 0.00, 150.00, 0.00, 0.00, 0, ''),
(46, 2008, NULL, 'servico', 'Limpeza interna notebook', 1.00, 0.00, 0.00, 150.00, 'pendente', 1, '2026-01-12 14:17:29', 0.00, 150.00, 0.00, 0.00, 0, ''),
(47, 2008, NULL, 'produto', 'Bateria Notebook', 1.00, 140.00, 230.00, 400.00, 'pendente', 1, '2026-01-12 14:21:53', 0.00, 170.00, 0.00, 0.00, 0, ''),
(48, 2008, NULL, 'produto', 'Memoria DDR4 4 GB', 1.00, 0.00, 100.00, 100.00, 'pendente', 1, '2026-01-12 14:22:28', 0.00, 0.00, 0.00, 0.00, 0, ''),
(49, 2008, NULL, 'produto', '', 1.00, 100.00, 100.00, 200.00, 'pendente', 0, '2026-01-12 14:23:27', 0.00, 100.00, 0.00, 0.00, 0, ''),
(50, 2008, NULL, 'produto', '', 1.00, 100.00, 100.00, 200.00, 'pendente', 1, '2026-01-12 14:24:13', 0.00, 100.00, 0.00, 0.00, 0, ''),
(51, 2008, NULL, 'produto', 'SSD SATA 240 GB', 1.00, 188.00, 299.90, 499.90, 'pendente', 1, '2026-01-12 18:52:50', 0.00, 200.00, 0.00, 0.00, 0, ''),
(52, 2014, NULL, 'servico', 'Instalação de Software', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-13 13:24:07', 0.00, 200.00, 0.00, 0.00, 0, ''),
(53, 2014, NULL, 'servico', 'Atualização do Sistema', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-13 13:24:26', 0.00, 190.00, 0.00, 0.00, 0, ''),
(54, 2017, NULL, 'produto', 'FONTE ATX 200 W', 1.00, 50.00, 200.00, 300.00, 'pendente', 1, '2026-01-13 20:15:26', 0.00, 100.00, 0.00, 0.00, 0, ''),
(55, 2017, NULL, 'produto', 'Memoria DDR4 8 GB', 1.00, 100.00, 290.00, 290.00, 'pendente', 1, '2026-01-13 20:16:19', 0.00, 0.00, 0.00, 0.00, 0, ''),
(56, 2018, NULL, 'produto', 'PC COMPLETO', 1.00, 1080.00, 2190.00, 2190.00, 'pendente', 1, '2026-01-13 20:20:43', 0.00, 0.00, 0.00, 0.00, 0, ''),
(57, 2014, NULL, 'produto', 'MOUSE USB', 1.00, 10.00, 30.00, 30.00, 'pendente', 1, '2026-01-13 20:47:47', 0.00, 0.00, 0.00, 0.00, 0, ''),
(58, 2019, NULL, 'produto', 'PEN DRIVE 32GB', 1.00, 28.00, 60.00, 160.00, 'pendente', 1, '2026-01-14 12:04:35', 0.00, 100.00, 0.00, 0.00, 0, ''),
(59, 2020, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 0, '2026-01-14 12:05:51', 0.00, 190.00, 0.00, 0.00, 0, ''),
(60, 2020, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-14 12:06:12', 0.00, 250.00, 60.00, 0.00, 0, ''),
(61, 2020, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-14 12:06:35', 0.00, 230.00, 30.00, 0.00, 0, ''),
(62, 2020, NULL, 'servico', 'DESLOCAMENTO', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-14 12:06:53', 0.00, 35.00, 35.00, 0.00, 0, ''),
(63, 2013, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-14 12:08:51', 0.00, 250.00, 60.00, 0.00, 0, ''),
(64, 2013, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 200.00, 'pendente', 1, '2026-01-14 12:09:03', 0.00, 230.00, 30.00, 0.00, 0, ''),
(65, 2013, NULL, 'servico', 'DESLOCAMENTO', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-14 12:09:18', 0.00, 35.00, 35.00, 0.00, 0, ''),
(66, 2012, NULL, 'servico', 'DESLOCAMENTO', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-14 12:10:20', 0.00, 35.00, 35.00, 0.00, 0, ''),
(67, 2011, NULL, 'servico', 'DESLOCAMENTO', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-14 12:11:05', 0.00, 35.00, 35.00, 0.00, 0, ''),
(68, 2021, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 220.00, 'pendente', 1, '2026-01-15 13:28:26', 0.00, 220.00, 0.00, 0.00, 0, ''),
(69, 2022, NULL, 'servico', 'Atualização do Sistema', 1.00, 0.00, 0.00, 250.00, 'pendente', 1, '2026-01-16 11:34:00', 0.00, 350.00, 100.00, 0.00, 0, ''),
(70, 2022, NULL, 'servico', 'Limpeza interna notebook', 1.00, 0.00, 0.00, 50.00, 'pendente', 0, '2026-01-16 11:54:43', 0.00, 100.00, 50.00, 0.00, 0, ''),
(71, 2022, NULL, 'servico', 'PARTICIONAR DISCO', 1.00, 0.00, 0.00, 140.00, 'pendente', 1, '2026-01-16 19:53:48', 0.00, 180.00, 40.00, 0.00, 0, ''),
(72, 2022, NULL, 'servico', 'DESLOCAMENTO', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-16 19:54:37', 0.00, 50.00, 50.00, 0.00, 0, ''),
(73, 2023, NULL, 'servico', 'Laudo técnico', 1.00, 0.00, 0.00, 100.00, 'pendente', 1, '2026-01-19 13:23:46', 0.00, 100.00, 0.00, 0.00, 0, ''),
(74, 2027, NULL, 'servico', 'ATENDIMENTO TECNICO', 1.00, 0.00, 150.00, 130.00, 'pendente', 1, '2026-01-19 17:15:14', 0.00, 0.00, 20.00, 0.00, 0, ''),
(75, 2027, NULL, 'produto', 'ADAPTADOR WIFI', 1.00, 59.90, 179.90, 149.90, 'pendente', 1, '2026-01-19 17:15:44', 0.00, 0.00, 30.00, 0.00, 0, ''),
(76, 2025, NULL, 'servico', 'Troca de tela notebook', 1.00, 235.00, 490.00, 490.00, 'pendente', 1, '2026-01-19 18:19:46', 0.00, 100.00, 100.00, 0.00, 0, ''),
(77, 2025, NULL, 'servico', 'FORMATACAO SISTEMA OPERACIONAL', 1.00, 0.00, 0.00, 190.00, 'pendente', 0, '2026-01-19 18:21:55', 0.00, 190.00, 0.00, 0.00, 0, ''),
(78, 2024, NULL, 'servico', 'Limpeza interna impressora', 1.00, 0.00, 0.00, 290.00, 'pendente', 1, '2026-01-20 12:20:07', 0.00, 290.00, 0.00, 0.00, 0, ''),
(79, 2033, NULL, 'servico', 'Instalação de Software', 1.00, 0.00, 0.00, 190.00, 'pendente', 1, '2026-01-20 18:01:48', 0.00, 270.00, 80.00, 0.00, 0, ''),
(80, 2025, NULL, 'produto', 'FLAT HD /SSD NOTEBOOK', 1.00, 60.00, 150.00, 150.00, 'pendente', 0, '2026-01-20 19:06:36', 0.00, 0.00, 0.00, 0.00, 0, ''),
(81, 2025, NULL, 'produto', 'SSD SATA 120 GB', 1.00, 124.00, 219.90, 399.90, 'pendente', 1, '2026-01-20 19:08:15', 0.00, 250.00, 70.00, 0.00, 0, ''),
(82, 2025, NULL, 'produto', 'FLAT HD /SSD NOTEBOOK', 1.00, 60.00, 150.00, 150.00, 'pendente', 1, '2026-01-20 19:10:11', 0.00, 70.00, 70.00, 0.00, 1, 'https://www.mercadolivre.com.br/cabo-do-hd-notebook-positivo-xc3620-xci3620-xc3630-xci3630-xc3634-xci3634-xc3650-xci3650-xc5631-xci5631-xc5634-xci5634-xc5650-xci5650-xc7660-xci7660-s14bw0x-ciclo-digital/p/MLB29284025#polycard_client=search-desktop&search_layout=grid&position=2&type=product&tracking_id=727fe51b-91f2-4692-878d-4e40e6292b1a&wid=MLB3830760921&sid=search'),
(83, 2028, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 299.00, 'pendente', 1, '2026-01-20 19:15:18', 0.00, 350.00, 51.00, 0.00, 0, ''),
(84, 2032, NULL, 'servico', 'REPARO ENTRADA DA FONTE', 1.00, 0.00, 0.00, 0.00, 'pendente', 1, '2026-01-21 13:13:07', 0.00, 150.00, 150.00, 0.00, 0, ''),
(85, 2032, NULL, 'produto', 'FONTE NOTEBOOK LENOVO', 1.00, 70.00, 219.90, 219.90, 'pendente', 1, '2026-01-21 13:19:11', 0.00, 0.00, 0.00, 0.00, 0, ''),
(86, 2026, NULL, 'produto', '', 1.00, 0.00, 0.02, 0.02, 'pendente', 0, '2026-01-21 16:40:51', 0.00, 0.00, 0.00, 0.00, 0, ''),
(87, 2026, NULL, 'produto', 'MEMORIA DDR3', 1.00, 0.00, 500.00, 400.00, 'pendente', 0, '2026-01-21 16:43:45', 0.00, 0.00, 100.00, 0.00, 0, ''),
(88, 2026, NULL, 'produto', 'PROCESSADOR INTEL I5', 1.00, 0.00, 300.00, 200.00, 'pendente', 0, '2026-01-21 19:04:13', 0.00, 0.00, 100.00, 0.00, 0, ''),
(89, 2026, NULL, 'produto', 'PLACA MãE INTEL 1155', 1.00, 0.00, 0.00, 500.00, 'pendente', 0, '2026-01-21 19:05:39', 0.00, 600.00, 100.00, 0.00, 0, ''),
(90, 2026, NULL, 'servico', 'LIMPEZA INTERNA COMPUTADOR', 1.00, 0.00, 0.00, 100.00, 'pendente', 0, '2026-01-21 19:05:58', 0.00, 200.00, 100.00, 0.00, 0, ''),
(91, 2026, NULL, 'produto', 'PC COMPLETO', 1.00, 1080.00, 1690.00, 1490.00, 'pendente', 1, '2026-01-21 19:21:31', 0.00, 0.00, 200.00, 0.00, 0, ''),
(92, 2035, NULL, 'servico', 'REMONTAGEM', 1.00, 0.00, 0.00, 250.00, 'pendente', 1, '2026-01-21 19:28:07', 0.00, 350.00, 100.00, 0.00, 0, ''),
(93, 2026, NULL, 'produto', 'MONITOR 20&#39;', 1.00, 0.00, 800.00, 600.00, 'pendente', 1, '2026-01-21 19:56:14', 0.00, 0.00, 200.00, 0.00, 0, ''),
(94, 2033, NULL, 'produto', 'Fonte ATX 500 W', 1.00, 0.00, 150.00, 130.00, 'pendente', 1, '2026-01-22 16:26:17', 0.00, 0.00, 20.00, 0.00, 0, ''),
(95, NULL, 8, 'produto', 'Fonte ATX 400 W', 1.00, 50.00, 300.00, 320.00, 'pendente', 0, '2026-01-27 23:22:49', 0.00, 20.00, 0.00, 0.00, 0, NULL),
(96, NULL, 8, 'produto', 'Fonte ATX 500 W', 1.00, 50.00, 250.00, 230.00, 'pendente', 1, '2026-01-27 23:24:27', 0.00, 0.00, 20.00, 0.00, 0, NULL);

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
  `dados_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `dados_novos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `referencia`, `ip_address`, `user_agent`, `dados_anteriores`, `dados_novos`, `created_at`) VALUES
(1, NULL, 'Tentativa de login falhou', 'E-mail: admin@admin.com', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-04 19:44:52'),
(2, 1, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-04 19:44:59'),
(3, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-04 21:00:41'),
(4, 2, 'Realizou login no sistema', NULL, '143.0.229.212', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 11:45:15'),
(5, 2, 'Criou nova Ordem de Serviço', 'OS #1', '143.0.229.212', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 11:46:40'),
(6, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 15:08:55'),
(7, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 16:40:21'),
(8, 2, 'Criou nova Ordem de Serviço', 'OS #2000', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 17:52:31'),
(9, 8, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:00:17'),
(10, 8, 'Criou nova Ordem de Serviço', 'OS #2001', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:03:04'),
(11, 8, 'Atualizou Ordem de Serviço', 'OS #2001', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:05:01'),
(12, 8, 'Atualizou Ordem de Serviço', 'OS #2001', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:07:00'),
(13, 8, 'Atualizou Ordem de Serviço', 'OS #1', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:09:30'),
(14, NULL, 'Tentativa de login falhou', 'E-mail: alan@zoka.com', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:11:50'),
(15, NULL, 'Tentativa de login falhou', 'E-mail: alan@zoca.com', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:12:04'),
(16, 9, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:13:47'),
(17, 9, 'Criou nova Ordem de Serviço', 'OS #2002', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:15:58'),
(18, 9, 'Atualizou Ordem de Serviço', 'OS #2002', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:18:18'),
(19, 9, 'Atualizou Ordem de Serviço', 'OS #2002', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:20:40'),
(20, NULL, 'Tentativa de login falhou', 'E-mail: alan@zoka.com.br', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:31:44'),
(21, NULL, 'Tentativa de login falhou', 'E-mail: alan@zoka.com', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:31:54'),
(22, 9, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:32:02'),
(23, 7, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:37:50'),
(24, 7, 'Atualizou Ordem de Serviço', 'OS #2002', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:41:55'),
(25, 7, 'Atualizou Ordem de Serviço', 'OS #2002', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 18:48:52'),
(26, 9, 'Criou nova Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 19:08:35'),
(27, 8, 'Criou novo cliente', 'Cliente #923 - Matheus Abrozzi Borges', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 19:15:25'),
(28, 8, 'Criou nova Ordem de Serviço', 'OS #2004', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-05 19:17:26'),
(29, 9, 'Criou nova Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 11:28:59'),
(30, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 11:33:25'),
(31, 9, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 17:04:07'),
(32, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 17:11:24'),
(33, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 17:11:45'),
(34, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 17:12:04'),
(35, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 18:20:46'),
(36, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 18:22:34'),
(37, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 18:22:42'),
(38, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 18:54:55'),
(39, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 18:55:03'),
(40, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 18:55:08'),
(41, 8, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 19:40:02'),
(42, 8, 'Atualizou Ordem de Serviço', 'OS #2004', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 19:40:51'),
(43, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 19:58:34'),
(44, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:08:02'),
(45, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:13:42'),
(46, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:13:54'),
(47, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:15:03'),
(48, NULL, 'Atualizou produto/serviço', 'Item #12 - FORMATACAO SISTEMA OPERACIONAL', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":12,\"nome\":\"Formatacao sistema operacional\",\"tipo\":\"servico\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":null}', '{\"nome\":\"FORMATACAO SISTEMA OPERACIONAL\",\"tipo\":\"servico\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"190.00\"}', '2026-01-06 20:18:23'),
(49, NULL, 'Atualizou produto/serviço', 'Item #10 - LIMPEZA INTERNA COMPUTADOR', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":10,\"nome\":\"Limpeza interna computador\",\"tipo\":\"servico\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":null}', '{\"nome\":\"LIMPEZA INTERNA COMPUTADOR\",\"tipo\":\"servico\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"200.00\"}', '2026-01-06 20:18:54'),
(50, NULL, 'Cadastrou novo produto/serviço', 'Item #58 - LIMPEZA DESKTOP GAMER', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"LIMPEZA DESKTOP GAMER\",\"tipo\":\"servico\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":\"350.00\"}', '2026-01-06 20:19:25'),
(51, NULL, 'Alterou configuração de porcentagem de venda', 'Nova porcentagem: 55%', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:19:33'),
(52, NULL, 'Alterou configuração de porcentagem de venda', 'Nova porcentagem: 60%', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:19:38'),
(53, 2, 'Atualizou Ordem de Serviço', 'OS #2000', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:22:32'),
(54, 9, 'Atualizou Ordem de Serviço', 'OS #2004', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:22:58'),
(55, 2, 'Atualizou Ordem de Serviço', 'OS #2001', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:23:10'),
(56, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-06 20:25:47'),
(57, NULL, 'Atualizou produto/serviço', 'Item #26 - SSD SATA 1 TB', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":26,\"nome\":\"SSD SATA 1 TB\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":null}', '{\"nome\":\"SSD SATA 1 TB\",\"tipo\":\"produto\",\"custo\":\"640.00\",\"valor_venda\":\"1024.00\",\"mao_de_obra\":\"0.00\"}', '2026-01-06 20:28:28'),
(58, NULL, 'Atualizou produto/serviço', 'Item #24 - SSD SATA 240 GB', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":24,\"nome\":\"SSD SATA 240 GB\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":null}', '{\"nome\":\"SSD SATA 240 GB\",\"tipo\":\"produto\",\"custo\":\"220.0\",\"valor_venda\":\"352.00\",\"mao_de_obra\":\"150.00\"}', '2026-01-06 20:29:22'),
(59, 2, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 11:24:06'),
(60, 2, 'Atualizou Ordem de Serviço', 'OS #2005', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 11:24:28'),
(61, 2, 'Criou novo cliente', 'Cliente #924 - Guilherme Neto', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 11:26:07'),
(62, 2, 'Criou nova Ordem de Serviço', 'OS #2006', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 11:26:53'),
(63, 2, 'Realizou logout do sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 11:26:57'),
(64, 9, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 13:10:59'),
(65, 9, 'Criou nova Ordem de Serviço', 'OS #2007', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 13:14:25'),
(66, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 16:57:26'),
(67, 2, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 16:57:34'),
(68, 2, 'Atualizou Ordem de Serviço', 'OS #2006', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:00:23'),
(69, NULL, 'Cadastrou novo produto/serviço', 'Item #59 - DESLOCAMENTO', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"DESLOCAMENTO\",\"tipo\":\"servico\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-07 17:01:05'),
(70, 2, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:01:12'),
(71, NULL, 'Cadastrou novo produto/serviço', 'Item #60 - DESLOCAMENTO', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"DESLOCAMENTO\",\"tipo\":\"servico\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":\"30.00\"}', '2026-01-07 17:05:29'),
(72, 2, 'Realizou logout do sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:11:58'),
(73, NULL, 'Tentativa de login falhou', 'E-mail: hedigar.tecmyranda@gmail.com', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:43:29'),
(74, NULL, 'Tentativa de login falhou', 'E-mail: hedigar.tecmyranda@gmail.com', '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:43:41'),
(75, 2, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:44:18'),
(76, 2, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:44:42'),
(77, 2, 'Criou novo cliente', 'Cliente #925 - Scheila Simone Nunes Espindola', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:45:58'),
(78, 2, 'Criou nova Ordem de Serviço', 'OS #2008', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 17:49:36'),
(79, 2, 'Atualizou Ordem de Serviço', 'OS #2006', '143.0.229.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 20:19:57'),
(80, 2, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-07 20:27:12'),
(81, 6, 'Realizou login no sistema', NULL, '143.0.229.33', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', NULL, NULL, '2026-01-07 20:27:48'),
(82, 1, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 12:34:10'),
(83, NULL, 'Atualizou produto/serviço', 'Item #24 - SSD SATA 240 GB', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":24,\"nome\":\"SSD SATA 240 GB\",\"tipo\":\"produto\",\"custo\":\"220.00\",\"valor_venda\":\"352.00\",\"mao_de_obra\":\"150.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":\"2026-01-06 20:29:22\"}', '{\"nome\":\"SSD SATA 240 GB\",\"tipo\":\"produto\",\"custo\":\"188.00\",\"valor_venda\":\"299.90\",\"mao_de_obra\":0}', '2026-01-08 12:35:48'),
(84, NULL, 'Atualizou produto/serviço', 'Item #24 - SSD SATA 240 GB', '177.129.25.35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '{\"id\":24,\"nome\":\"SSD SATA 240 GB\",\"tipo\":\"produto\",\"custo\":\"188.00\",\"valor_venda\":\"299.90\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":\"2026-01-08 12:35:48\"}', '{\"nome\":\"SSD SATA 240 GB\",\"tipo\":\"produto\",\"custo\":\"188.00\",\"valor_venda\":\"299.90\",\"mao_de_obra\":0}', '2026-01-08 12:36:11'),
(85, NULL, 'Atualizou produto/serviço', 'Item #25 - SSD SATA 480 GB', '177.129.25.35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '{\"id\":25,\"nome\":\"SSD SATA 480 GB\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":null}', '{\"nome\":\"SSD SATA 480 GB\",\"tipo\":\"produto\",\"custo\":\"366.00\",\"valor_venda\":\"599.90\",\"mao_de_obra\":\"0.00\"}', '2026-01-08 12:37:16'),
(86, NULL, 'Atualizou produto/serviço', 'Item #23 - SSD SATA 120 GB', '177.129.25.35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '{\"id\":23,\"nome\":\"SSD SATA 120 GB\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":null}', '{\"nome\":\"SSD SATA 120 GB\",\"tipo\":\"produto\",\"custo\":\"124\",\"valor_venda\":\"199.90\",\"mao_de_obra\":\"0.00\"}', '2026-01-08 12:39:42'),
(87, NULL, 'Atualizou produto/serviço', 'Item #23 - SSD SATA 120 GB', '177.129.25.35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '{\"id\":23,\"nome\":\"SSD SATA 120 GB\",\"tipo\":\"produto\",\"custo\":\"124.00\",\"valor_venda\":\"199.90\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-03 02:21:36\",\"updated_at\":\"2026-01-08 12:39:42\"}', '{\"nome\":\"SSD SATA 120 GB\",\"tipo\":\"produto\",\"custo\":\"124.00\",\"valor_venda\":\"219.900\",\"mao_de_obra\":\"0.00\"}', '2026-01-08 12:40:19'),
(88, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 16:54:18'),
(89, 9, 'Criou novo cliente', 'Cliente #926 - Dr.morozini Primoir de Camargo Morozini', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 17:00:59'),
(90, 9, 'Excluiu cliente', 'Cliente #1 - Luciano', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 17:01:11'),
(91, 9, 'Criou nova Ordem de Serviço', 'OS #2009', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 17:22:12'),
(92, 9, 'Criou nova Ordem de Serviço', 'OS #2010', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 17:26:31'),
(93, NULL, 'Cadastrou novo produto/serviço', 'Item #61 - FONTE IMPRESSORA', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"FONTE IMPRESSORA\",\"tipo\":\"produto\",\"custo\":\"70\",\"valor_venda\":\"290.00\",\"mao_de_obra\":\"100.00\"}', '2026-01-08 17:32:29'),
(94, 2, 'Realizou logout do sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:04:57'),
(95, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:05:12'),
(96, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com.br', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:37:32'),
(97, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:37:42'),
(98, 2, 'Excluiu Ordem de Serviço', 'OS #1', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:37:59'),
(99, 2, 'Excluiu Ordem de Serviço', 'OS #2000', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:38:02'),
(100, 2, 'Excluiu Ordem de Serviço', 'OS #2001', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:38:09'),
(101, 2, 'Excluiu Ordem de Serviço', 'OS #2002', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:38:13'),
(102, 2, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:38:43'),
(103, 2, 'Atualizou Ordem de Serviço', 'OS #2003', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:40:38'),
(104, 2, 'Atualizou Ordem de Serviço', 'OS #2004', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:41:27'),
(105, 2, 'Atualizou Ordem de Serviço', 'OS #2006', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:42:09'),
(106, 7, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:53:04'),
(107, 2, 'Realizou logout do sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:57:33'),
(108, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 18:57:51'),
(109, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 19:09:11'),
(110, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 19:43:42'),
(111, 2, 'Criou novo cliente', 'Cliente #927 - Plural', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 19:46:42'),
(112, 2, 'Criou nova Ordem de Serviço', 'OS #2011', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 19:47:25'),
(113, 2, 'Criou nova Ordem de Serviço', 'OS #2012', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 19:49:41'),
(114, 2, 'Criou nova Ordem de Serviço', 'OS #2013', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 19:50:35'),
(115, 2, 'Realizou logout do sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-08 20:11:21'),
(116, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 01:33:27'),
(117, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 11:22:09'),
(118, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 11:24:46'),
(119, 9, 'Atualizou Ordem de Serviço', 'OS #2009', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 11:29:31'),
(120, 9, 'Atualizou Ordem de Serviço', 'OS #2010', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 11:31:38'),
(121, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 17:42:04'),
(122, 8, 'Atualizou dados do cliente', 'Cliente #828 - Nelson Adroaldo da Silva', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 17:49:10'),
(123, 8, 'Criou nova Ordem de Serviço', 'OS #2014', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 17:53:23'),
(124, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 17:59:31'),
(125, 7, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 18:10:10'),
(126, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 18:15:17'),
(127, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 18:16:09'),
(128, 9, 'Atualizou Ordem de Serviço', 'OS #2008', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 18:21:37'),
(129, 9, 'Atualizou Ordem de Serviço', 'OS #2008', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-09 18:22:03'),
(130, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 11:39:27'),
(131, 2, 'Atualizou Ordem de Serviço', 'OS #2009', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 11:39:56'),
(132, 9, 'Atualizou Ordem de Serviço', 'OS #2007', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 11:40:10'),
(133, 9, 'Criou nova Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 11:42:08'),
(134, 9, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 11:49:42'),
(135, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 11:55:35'),
(136, 8, 'Criou novo cliente', 'Cliente #928 - Elsa Teresinha de Oliveira', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:00:08'),
(137, 8, 'Criou nova Ordem de Serviço', 'OS #2016', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:01:57'),
(138, 2, 'Criou novo atendimento externo', 'Atendimento #1', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:19:54'),
(139, 2, 'Atualizou atendimento externo', 'Atendimento #1', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:20:03'),
(140, 2, 'Atualizou atendimento externo', 'Atendimento #1', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:20:18'),
(141, 2, 'Atualizou atendimento externo', 'Atendimento #1', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:21:49'),
(142, 9, 'Atualizou Ordem de Serviço', 'OS #2007', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 12:34:49'),
(143, 9, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 13:43:05'),
(144, 9, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 13:46:53'),
(145, 9, 'Atualizou Ordem de Serviço', 'OS #2012', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 13:47:13'),
(146, 9, 'Atualizou Ordem de Serviço', 'OS #2013', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 13:47:19'),
(147, 9, 'Atualizou Ordem de Serviço', 'OS #2010', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-10 13:49:12'),
(148, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:16:37'),
(149, 2, 'Excluiu atendimento externo', 'Atendimento #1', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:20:45'),
(150, 2, 'Criou novo cliente', 'Cliente #929 - Myranda', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:24:34'),
(151, 2, 'Criou novo cliente', 'Cliente #930 - Vester Confeccoes Ltda', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:31:05'),
(152, 2, 'Criou novo atendimento externo', 'Atendimento #2', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:35:57'),
(153, 2, 'Atualizou atendimento externo', 'Atendimento #2', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:36:20'),
(154, 2, 'Criou novo atendimento externo', 'Atendimento #3', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:37:25'),
(155, 2, 'Criou novo cliente', 'Cliente #931 - ITAPEVA COMERCIAL AGRICOLA LTDA', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:40:13'),
(156, 2, 'Criou novo atendimento externo', 'Atendimento #4', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-11 23:41:59'),
(157, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 10:54:26'),
(158, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:12:34'),
(159, 9, 'Atualizou Ordem de Serviço', 'OS #2005', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:15:04'),
(160, 9, 'Atualizou Ordem de Serviço', 'OS #2003', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:15:21'),
(161, NULL, 'Tentativa de login falhou', 'E-mail: pedro@leilio.com', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:17:33'),
(162, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:17:45'),
(163, 9, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:18:14'),
(164, 8, 'Atualizou Ordem de Serviço', 'OS #2006', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:18:26'),
(165, 9, 'Atualizou Ordem de Serviço', 'OS #2012', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:20:42'),
(166, 9, 'Atualizou Ordem de Serviço', 'OS #2012', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:20:47'),
(167, 9, 'Atualizou Ordem de Serviço', 'OS #2013', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:21:04'),
(168, 9, 'Atualizou Ordem de Serviço', 'OS #2013', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:21:53'),
(169, 7, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:35:40'),
(170, 9, 'Atualizou Ordem de Serviço', 'OS #2014', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:38:02'),
(171, 9, 'Atualizou Ordem de Serviço', 'OS #2008', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:38:15'),
(172, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 11:45:51'),
(173, 9, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 12:23:24'),
(174, 9, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 12:23:31'),
(175, 7, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 12:29:23'),
(176, 2, 'Criou novo atendimento externo', 'Atendimento #5', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 12:42:38'),
(177, 2, 'Excluiu atendimento externo', 'Atendimento #5', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 12:42:56'),
(178, 9, 'Atualizou Ordem de Serviço', 'OS #2016', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 14:00:33'),
(179, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 16:21:05'),
(180, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 17:03:58'),
(181, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 17:48:51'),
(182, 9, 'Atualizou Ordem de Serviço', 'OS #2016', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 17:50:35'),
(183, 9, 'Atualizou Ordem de Serviço', 'OS #2014', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 17:51:20'),
(184, 9, 'Atualizou Ordem de Serviço', 'OS #2008', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 17:56:11'),
(185, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-12 19:13:50'),
(186, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 00:34:27'),
(187, 2, 'Atualizou Ordem de Serviço', 'OS #2013', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 00:34:49'),
(188, 2, 'Atualizou Ordem de Serviço', 'OS #2012', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 00:35:02'),
(189, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-13 01:35:22'),
(190, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 09:38:28'),
(191, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 10:41:05'),
(192, NULL, 'Atualizou produto/serviço', 'Item #37 - FONTE ATX 200 W', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":37,\"nome\":\"Fonte ATX 200 W\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-02 23:21:36\",\"updated_at\":null}', '{\"nome\":\"FONTE ATX 200 W\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"100.00\"}', '2026-01-13 10:59:51'),
(193, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 12:03:34'),
(194, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 13:18:08'),
(195, 9, 'Atualizou Ordem de Serviço', 'OS #2014', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 13:23:50'),
(196, 7, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 14:15:29'),
(197, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 14:36:50'),
(198, 2, 'Realizou logout do sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:18:26'),
(199, NULL, 'Tentativa de login falhou', 'E-mail: hedigar1@gmail.com', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:18:48'),
(200, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:18:58'),
(201, 2, 'Realizou logout do sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:19:17'),
(202, NULL, 'Tentativa de login falhou', 'E-mail: admin@admin.com', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:19:27'),
(203, NULL, 'Tentativa de login falhou', 'E-mail: admin@admin.com', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:19:38'),
(204, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:19:42'),
(205, 2, 'Realizou logout do sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:19:51'),
(206, NULL, 'Tentativa de login falhou', 'E-mail: admin@admin.com', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:19:56'),
(207, 1, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:20:02'),
(208, 1, 'Realizou logout do sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:20:23'),
(209, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:20:33'),
(210, 2, 'Realizou logout do sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:20:53'),
(211, NULL, 'Tentativa de login falhou', 'E-mail: admin@admin.com', '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:21:01'),
(212, 1, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:21:06');
INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `referencia`, `ip_address`, `user_agent`, `dados_anteriores`, `dados_novos`, `created_at`) VALUES
(213, 1, 'Realizou logout do sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 16:21:14'),
(214, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 19:35:27'),
(215, 8, 'Atualizou Ordem de Serviço', 'OS #2014', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 19:36:16'),
(216, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 20:08:25'),
(217, NULL, 'Atualizou produto/serviço', 'Item #49 - MOUSE USB', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":49,\"nome\":\"Mouse USB\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-02 23:21:36\",\"updated_at\":null}', '{\"nome\":\"MOUSE USB\",\"tipo\":\"produto\",\"custo\":\"10\",\"valor_venda\":\"30.00\",\"mao_de_obra\":\"0.00\"}', '2026-01-13 20:08:55'),
(218, 2, 'Criou novo cliente', 'Cliente #932 - Sandro da Silveira Niederauer', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 20:12:07'),
(219, 2, 'Criou nova Ordem de Serviço', 'OS #2017', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 20:14:51'),
(220, 2, 'Criou nova Ordem de Serviço', 'OS #2018', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 20:19:28'),
(221, NULL, 'Cadastrou novo produto/serviço', 'Item #62 - PC COMPLETO', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PC COMPLETO\",\"tipo\":\"produto\",\"custo\":\"1080\",\"valor_venda\":\"2190.00\",\"mao_de_obra\":0}', '2026-01-13 20:20:19'),
(222, 2, 'Criou novo atendimento', 'Atendimento #6', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 20:24:21'),
(223, 2, 'Realizou login no sistema', NULL, '2804:18:10f4:92ec:6c7e:fdff:fe06:200c', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-13 20:34:47'),
(224, 2, 'Criou nova Ordem de Serviço', 'OS #2019', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 12:01:35'),
(225, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 12:03:11'),
(226, NULL, 'Cadastrou novo produto/serviço', 'Item #63 - PEN DRIVE 32GB', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PEN DRIVE 32GB\",\"tipo\":\"produto\",\"custo\":\"28.00\",\"valor_venda\":\"59.90\",\"mao_de_obra\":0}', '2026-01-14 12:03:44'),
(227, 2, 'Criou nova Ordem de Serviço', 'OS #2020', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 12:05:39'),
(228, 7, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 12:20:43'),
(229, NULL, 'Tentativa de login falhou', 'E-mail: hedigar.tecmyranda@gmail.com', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 12:45:40'),
(230, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 12:45:55'),
(231, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 13:26:48'),
(232, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 13:33:40'),
(233, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 16:44:26'),
(234, 9, 'Atualizou Ordem de Serviço', 'OS #2008', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 16:45:01'),
(235, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 18:17:04'),
(236, 8, 'Criou novo cliente', 'Cliente #933 - Monice Vargas Marques', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 18:53:36'),
(237, 8, 'Criou nova Ordem de Serviço', 'OS #2021', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 18:56:25'),
(238, 8, 'Atualizou Ordem de Serviço', 'OS #2021', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 18:58:15'),
(239, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 20:21:14'),
(240, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 20:45:37'),
(241, 9, 'Atualizou Ordem de Serviço', 'OS #2021', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 20:46:17'),
(242, 9, 'Atualizou Ordem de Serviço', 'OS #2016', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-14 20:47:05'),
(243, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 11:44:36'),
(244, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 13:28:08'),
(245, 2, 'Criou novo cliente', 'Cliente #934 - Pastelaria Litorial (Deise)', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 13:50:34'),
(246, 2, 'Criou nova Ordem de Serviço', 'OS #2022', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 13:52:32'),
(247, 2, 'Realizou logout do sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 14:00:07'),
(248, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 16:34:08'),
(249, 9, 'Atualizou Ordem de Serviço', 'OS #2022', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-15 16:35:52'),
(250, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:11:56'),
(251, 2, 'Criou novo cliente', 'Cliente #935 - Tania Machado Lutz', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:12:58'),
(252, 2, 'Criou nova Ordem de Serviço', 'OS #2023', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:16:06'),
(253, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:26:23'),
(254, 9, 'Atualizou Ordem de Serviço', 'OS #2021', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:27:30'),
(255, 9, 'Atualizou Ordem de Serviço', 'OS #2023', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:32:37'),
(256, 9, 'Atualizou Ordem de Serviço', 'OS #2022', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:33:43'),
(257, 9, 'Atualizou Ordem de Serviço', 'OS #2022', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:54:53'),
(258, 9, 'Atualizou Ordem de Serviço', 'OS #2019', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 11:55:45'),
(259, 7, 'Atualizou Ordem de Serviço', 'OS #2020', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 13:47:50'),
(260, 7, 'Atualizou Ordem de Serviço', 'OS #2013', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 13:48:09'),
(261, 7, 'Atualizou Ordem de Serviço', 'OS #2012', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 13:48:18'),
(262, 7, 'Atualizou Ordem de Serviço', 'OS #2011', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 13:48:31'),
(263, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 14:16:02'),
(264, 9, 'Criou nova Ordem de Serviço', 'OS #2024', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 14:18:59'),
(265, 9, 'Atualizou Ordem de Serviço', 'OS #2024', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 14:25:03'),
(266, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 16:42:23'),
(267, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 19:51:40'),
(268, NULL, 'Cadastrou novo produto/serviço', 'Item #64 - PARTICIONAR DISCO', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PARTICIONAR DISCO\",\"tipo\":\"servico\",\"custo\":0,\"valor_venda\":\"120.00\",\"mao_de_obra\":0}', '2026-01-16 19:53:17'),
(269, 2, 'Atualizou Ordem de Serviço', 'OS #2022', '177.129.25.35', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 20:06:12'),
(270, 2, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 20:16:40'),
(271, 2, 'Realizou logout do sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 20:17:08'),
(272, 7, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 20:29:26'),
(273, 7, 'Atualizou Ordem de Serviço', 'OS #2015', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-16 20:30:00'),
(274, 9, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-17 12:01:27'),
(275, 9, 'Atualizou Ordem de Serviço', 'OS #2023', '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-17 13:33:29'),
(276, 8, 'Realizou login no sistema', NULL, '177.129.25.35', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-17 14:39:14'),
(277, 2, 'Realizou login no sistema', NULL, '143.0.229.56', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-17 16:55:23'),
(278, 2, 'Atualizou Ordem de Serviço', 'OS #2022', '143.0.229.56', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-17 16:55:56'),
(279, 7, 'Realizou login no sistema', NULL, '2804:2984:99a9:8f00:744a:5d54:3e64:7fd7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-17 23:33:47'),
(280, 7, 'Realizou login no sistema', NULL, '2804:2984:99a9:8f00:3f87:710c:b2d1:d850', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-19 01:46:14'),
(281, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:04:45'),
(282, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:05:15'),
(283, 9, 'Atualizou Ordem de Serviço', 'OS #2023', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:05:52'),
(284, 2, 'Criou novo cliente', 'Cliente #936 - Elizeu Selamar Barbosa Terra', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:06:44'),
(285, 2, 'Criou nova Ordem de Serviço', 'OS #2025', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:08:53'),
(286, 2, 'Criou nova Ordem de Serviço', 'OS #2026', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:11:43'),
(287, 9, 'Atualizou Ordem de Serviço', 'OS #2024', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:20:24'),
(288, 2, 'Realizou logout do sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:21:11'),
(289, 8, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:21:26'),
(290, 8, 'Atualizou Ordem de Serviço', 'OS #2023', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:22:22'),
(291, 8, 'Realizou logout do sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 13:56:52'),
(292, 2, 'Realizou login no sistema', NULL, '143.0.228.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 16:22:41'),
(293, 8, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 17:03:47'),
(294, 8, 'Criou novo cliente', 'Cliente #937 - Clinica Odontológica Implantes', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 17:09:14'),
(295, 8, 'Atualizou dados do cliente', 'Cliente #937 - Clinica Odontológica Implantes', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 17:10:11'),
(296, NULL, 'Cadastrou novo produto/serviço', 'Item #65 - ADAPTADOR WIFI', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"ADAPTADOR WIFI\",\"tipo\":\"produto\",\"custo\":\"59.90\",\"valor_venda\":\"149.90\",\"mao_de_obra\":0}', '2026-01-19 17:11:40'),
(297, NULL, 'Cadastrou novo produto/serviço', 'Item #66 - ATENDIMENTO TECNICO', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"ATENDIMENTO TECNICO\",\"tipo\":\"servico\",\"custo\":0,\"valor_venda\":\"150.00\",\"mao_de_obra\":0}', '2026-01-19 17:12:17'),
(298, NULL, 'Atualizou produto/serviço', 'Item #50 - ADAPTADOR USB WI-FI', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":50,\"nome\":\"Adaptador USB Wi-Fi\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-02 23:21:36\",\"updated_at\":null}', '{\"nome\":\"ADAPTADOR USB WI-FI\",\"tipo\":\"produto\",\"custo\":\"49.90\",\"valor_venda\":\"149.90\",\"mao_de_obra\":\"0.00\"}', '2026-01-19 17:12:41'),
(299, 8, 'Criou nova Ordem de Serviço', 'OS #2027', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 17:14:53'),
(300, 2, 'Atualizou Ordem de Serviço', 'OS #2027', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 17:16:50'),
(301, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 17:24:57'),
(302, 2, 'Realizou login no sistema', NULL, '177.127.160.141', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 19:30:36'),
(303, 2, 'Realizou logout do sistema', NULL, '177.127.160.141', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-19 19:31:05'),
(304, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com.br', '143.0.228.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 10:39:42'),
(305, 2, 'Realizou login no sistema', NULL, '143.0.228.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 10:39:49'),
(306, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-20 11:14:48'),
(307, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 11:19:48'),
(308, 8, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 11:39:18'),
(309, 2, 'Realizou logout do sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 11:52:01'),
(310, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 11:52:09'),
(311, 7, 'Atualizou Ordem de Serviço', 'OS #2017', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 11:59:25'),
(312, 7, 'Atualizou Ordem de Serviço', 'OS #2018', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:00:07'),
(313, 7, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-20 12:01:44'),
(314, 7, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-20 12:03:30'),
(315, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:15:24'),
(316, 2, 'Atualizou Ordem de Serviço', 'OS #2024', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:19:53'),
(317, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:30:19'),
(318, 8, 'Realizou logout do sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:41:57'),
(319, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:43:40'),
(320, 2, 'Criou novo cliente', 'Cliente #938 - Pastelaria Litoral Robson', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:44:58'),
(321, 2, 'Atualizou dados do cliente', 'Cliente #938 - Pastelaria Litoral Robson', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:45:11'),
(322, 2, 'Criou nova Ordem de Serviço', 'OS #2028', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:46:16'),
(323, 2, 'Criou nova Ordem de Serviço', 'OS #2029', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:47:22'),
(324, 2, 'Realizou logout do sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:57:05'),
(325, 8, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 12:57:18'),
(326, 8, 'Criou novo cliente', 'Cliente #939 - Fernanda Borges Santos', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:15:34'),
(327, 8, 'Criou nova Ordem de Serviço', 'OS #2030', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:17:09'),
(328, 8, 'Criou nova Ordem de Serviço', 'OS #2031', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:19:01'),
(329, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:40:10'),
(330, 2, 'Criou novo cliente', 'Cliente #940 - Lima &#38; Lucas Administradora de Imóveis Ltda ', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:50:11'),
(331, 2, 'Atualizou dados do cliente', 'Cliente #940 - Lima &#38; Lucas Administradora de Imóveis Ltda ', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:50:27'),
(332, 2, 'Atualizou dados do cliente', 'Cliente #940 - Lima Lucas Administradora de Imóveis Ltda ', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:50:38'),
(333, 2, 'Criou novo atendimento', 'Atendimento #7', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:52:16'),
(334, 2, 'Atualizou atendimento', 'Atendimento #4', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 13:56:08'),
(335, 2, 'Criou novo cliente', 'Cliente #941 - Robinson Silvani Pacheco', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 14:21:01'),
(336, 2, 'Busca cliente OS', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"termo\":\"00.168.034-000\",\"count\":1}', '2026-01-20 14:21:24'),
(337, 2, 'Criou nova Ordem de Serviço', 'OS #2032', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 14:25:12'),
(338, 8, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 16:30:45'),
(339, 8, 'Busca cliente OS', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"termo\":\"Tania\",\"count\":2}', '2026-01-20 16:30:53'),
(340, 8, 'Criou nova Ordem de Serviço', 'OS #2033', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 16:33:07'),
(341, 8, 'Criou novo cliente', 'Cliente #942 - Paulo Valmor Giacomelli', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 17:17:00'),
(342, 8, 'Busca cliente OS', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"termo\":\"paulo\",\"count\":10}', '2026-01-20 17:17:22'),
(343, 8, 'Busca cliente OS', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"termo\":\"paulo valmor\",\"count\":1}', '2026-01-20 17:17:44'),
(344, 8, 'Criou nova Ordem de Serviço', 'OS #2034', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 17:18:51'),
(345, NULL, 'Cadastrou novo produto/serviço', 'Item #67 - FLAT HD /SSD NOTEBOOK', '138.118.87.8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '{\"nome\":\"FLAT HD \\/SSD NOTEBOOK\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-20 17:20:46'),
(346, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 17:55:25'),
(347, 9, 'Atualizou Ordem de Serviço', 'OS #2027', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 17:56:05'),
(348, 9, 'Atualizou Ordem de Serviço', 'OS #2033', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 18:40:18'),
(349, 9, 'Atualizou Ordem de Serviço', 'OS #2033', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 18:40:21'),
(350, 9, 'Atualizou Ordem de Serviço', 'OS #2033', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:01:01'),
(351, 9, 'Atualizou Ordem de Serviço', 'OS #2025', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:09:31'),
(352, 9, 'Atualizou Ordem de Serviço', 'OS #2026', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:11:58'),
(353, 9, 'Atualizou Ordem de Serviço', 'OS #2026', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:12:05'),
(354, 9, 'Atualizou Ordem de Serviço', 'OS #2028', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:12:30'),
(355, 9, 'Atualizou Ordem de Serviço', 'OS #2029', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:16:09'),
(356, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:41:29'),
(357, 2, 'Criou novo cliente', 'Cliente #943 - Filipe Vitola Peixoto', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:42:34'),
(358, 2, 'Criou nova Ordem de Serviço', 'OS #2035', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 19:45:07'),
(359, 9, 'Atualizou Ordem de Serviço', 'OS #2033', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 20:02:41'),
(360, 9, 'Atualizou Ordem de Serviço', 'OS #2034', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 20:03:16'),
(361, 9, 'Atualizou Ordem de Serviço', 'OS #2032', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 20:03:51'),
(362, 9, 'Atualizou Ordem de Serviço', 'OS #2035', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 20:04:12'),
(363, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 20:53:20'),
(364, 9, 'Atualizou Ordem de Serviço', 'OS #2033', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-20 20:54:12'),
(365, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 11:07:31'),
(366, 9, 'Atualizou Ordem de Serviço', 'OS #2031', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 11:09:00'),
(367, 9, 'Atualizou Ordem de Serviço', 'OS #2030', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 11:09:06'),
(368, 2, 'Criou novo cliente', 'Cliente #944 - Escola Albatroz', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 12:31:20'),
(369, 2, 'Criou novo atendimento', 'Atendimento #8', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 12:36:42'),
(370, NULL, 'Cadastrou novo produto/serviço', 'Item #68 - REPARO ENTRADA DA FONTE', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"REPARO ENTRADA DA FONTE\",\"tipo\":\"servico\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":\"150.00\"}', '2026-01-21 13:02:56'),
(371, NULL, 'Atualizou produto/serviço', 'Item #47 - FONTE NOTEBOOK LENOVO', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":47,\"nome\":\"Fonte notebook universal\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-02 23:21:36\",\"updated_at\":null}', '{\"nome\":\"FONTE NOTEBOOK LENOVO\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\"}', '2026-01-21 13:16:56'),
(372, NULL, 'Cadastrou novo produto/serviço', 'Item #69 - FONTE NOTEBOOK DELL', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"FONTE NOTEBOOK DELL\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 13:17:08'),
(373, NULL, 'Cadastrou novo produto/serviço', 'Item #70 - FONTE NOTEBOOK POSITIVO', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"FONTE NOTEBOOK POSITIVO\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 13:17:29'),
(374, NULL, 'Cadastrou novo produto/serviço', 'Item #71 - FONTE NOTEBOOK HP', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"FONTE NOTEBOOK HP\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 13:17:48'),
(375, NULL, 'Cadastrou novo produto/serviço', 'Item #72 - FONTE NOTEBOOK ACER', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"FONTE NOTEBOOK ACER\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 13:17:55'),
(376, NULL, 'Cadastrou novo produto/serviço', 'Item #73 - FONTE NOTEBOOK SAMSUNG', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"FONTE NOTEBOOK SAMSUNG\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 13:18:11'),
(377, 9, 'Atualizou Ordem de Serviço', 'OS #2032', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 13:20:49'),
(378, 9, 'Atualizou Ordem de Serviço', 'OS #2032', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 13:36:02'),
(379, 9, 'Atualizou Ordem de Serviço', 'OS #2025', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 13:42:36'),
(380, NULL, 'Cadastrou novo produto/serviço', 'Item #74 - MEMORIA DDR3', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"MEMORIA DDR3\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 13:59:00'),
(381, 9, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 16:30:28'),
(382, NULL, 'Cadastrou novo produto/serviço', 'Item #75 - REMONTAGEM', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"REMONTAGEM\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 16:58:44'),
(383, NULL, 'Atualizou produto/serviço', 'Item #75 - REMONTAGEM', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"id\":75,\"nome\":\"REMONTAGEM\",\"tipo\":\"produto\",\"custo\":\"0.00\",\"valor_venda\":\"0.00\",\"mao_de_obra\":\"0.00\",\"ativo\":1,\"created_at\":\"2026-01-21 13:58:44\",\"updated_at\":null}', '{\"nome\":\"REMONTAGEM\",\"tipo\":\"servico\",\"custo\":\"0.00\",\"valor_venda\":\"250.00\",\"mao_de_obra\":\"350.00\"}', '2026-01-21 16:59:01'),
(384, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-21 17:01:19'),
(385, 9, 'Atualizou Ordem de Serviço', 'OS #2035', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 17:58:55'),
(386, 9, 'Atualizou Ordem de Serviço', 'OS #2031', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 17:59:15'),
(387, 9, 'Atualizou Ordem de Serviço', 'OS #2030', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 17:59:19'),
(388, 9, 'Atualizou Ordem de Serviço', 'OS #2025', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 18:01:16'),
(389, 9, 'Atualizou Ordem de Serviço', 'OS #2026', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 18:01:27'),
(390, NULL, 'Cadastrou novo produto/serviço', 'Item #76 - PLACA MãE INTEL 1155', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PLACA M\\u00e3E INTEL 1155\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 18:11:35'),
(391, NULL, 'Cadastrou novo produto/serviço', 'Item #77 - PROCESSADOR INTEL I3', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PROCESSADOR INTEL I3\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 18:11:53'),
(392, NULL, 'Cadastrou novo produto/serviço', 'Item #78 - PROCESSADOR INTEL I5', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PROCESSADOR INTEL I5\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 18:12:05'),
(393, NULL, 'Cadastrou novo produto/serviço', 'Item #79 - PROCESSADOR INTEL I7', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '{\"nome\":\"PROCESSADOR INTEL I7\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 18:12:16'),
(394, 9, 'Atualizou Ordem de Serviço', 'OS #2026', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 18:27:05'),
(395, 9, 'Atualizou Ordem de Serviço', 'OS #2032', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 18:27:20'),
(396, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 19:42:27'),
(397, NULL, 'Tentativa de login falhou', 'E-mail: hedigar9@gmail.com', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 19:42:36'),
(398, 2, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 19:42:43'),
(399, NULL, 'Cadastrou novo produto/serviço', 'Item #80 - MONITOR 18\'', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '{\"nome\":\"MONITOR 18\'\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 19:43:24'),
(400, NULL, 'Cadastrou novo produto/serviço', 'Item #81 - MONITOR 19\'', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '{\"nome\":\"MONITOR 19\'\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 19:43:38'),
(401, NULL, 'Cadastrou novo produto/serviço', 'Item #82 - MONITOR 20\'', '143.0.229.6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '{\"nome\":\"MONITOR 20\'\",\"tipo\":\"produto\",\"custo\":0,\"valor_venda\":0,\"mao_de_obra\":0}', '2026-01-21 19:43:54'),
(402, NULL, 'Tentativa de login falhou', 'E-mail: pedro@lelilio.com', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 19:47:01'),
(403, NULL, 'Tentativa de login falhou', 'E-mail: pedro@leilio.com', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 19:47:16'),
(404, 8, 'Realizou login no sistema', NULL, '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 19:47:32'),
(405, 9, 'Atualizou Ordem de Serviço', 'OS #2035', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 20:02:16'),
(406, 9, 'Atualizou Ordem de Serviço', 'OS #2035', '143.0.229.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 20:03:27'),
(407, 2, 'Realizou login no sistema', NULL, '143.0.228.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-21 23:00:40'),
(408, 2, 'Realizou login no sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 10:42:44'),
(409, 2, 'Realizou logout do sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 10:42:55'),
(410, 2, 'Realizou login no sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 10:43:00'),
(411, 2, 'Realizou login no sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 10:44:46'),
(412, 2, 'Realizou logout do sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 10:45:23'),
(413, 2, 'Realizou login no sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 10:45:27'),
(414, 2, 'Excluiu transação de pagamento', 'Transação #3', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":3,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"debito\",\"bandeira\":\"Visa\",\"parcelas\":9,\"taxa_percentual\":\"1.40\",\"valor_bruto\":\"15000.00\",\"valor_taxa\":\"210.00\",\"valor_liquido\":\"14790.00\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 10:05:51\"}', '{\"ativo\":0}', '2026-01-22 13:06:31'),
(415, 2, 'Excluiu transação de pagamento', 'Transação #4', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":4,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"credito\",\"bandeira\":\"Visa\",\"parcelas\":9,\"taxa_percentual\":\"13.07\",\"valor_bruto\":\"15000.00\",\"valor_taxa\":\"1960.50\",\"valor_liquido\":\"13039.50\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 10:06:51\"}', '{\"ativo\":0}', '2026-01-22 13:07:36'),
(416, 2, 'Excluiu transação de pagamento', 'Transação #5', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":5,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"credito\",\"bandeira\":\"Visa\",\"parcelas\":12,\"taxa_percentual\":\"13.09\",\"valor_bruto\":\"1890.00\",\"valor_taxa\":\"247.40\",\"valor_liquido\":\"1642.60\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 10:08:20\"}', '{\"ativo\":0}', '2026-01-22 13:09:02'),
(417, 2, 'Excluiu transação de pagamento', 'Transação #6', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":6,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"credito\",\"bandeira\":\"Visa\",\"parcelas\":12,\"taxa_percentual\":\"13.09\",\"valor_bruto\":\"1200.00\",\"valor_taxa\":\"157.08\",\"valor_liquido\":\"1042.92\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 10:09:18\"}', '{\"ativo\":0}', '2026-01-22 13:09:53');
INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `referencia`, `ip_address`, `user_agent`, `dados_anteriores`, `dados_novos`, `created_at`) VALUES
(418, 2, 'Excluiu transação de pagamento', 'Transação #2', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":2,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"credito\",\"bandeira\":\"Visa\",\"parcelas\":3,\"taxa_percentual\":\"8.04\",\"valor_bruto\":\"150.00\",\"valor_taxa\":\"12.06\",\"valor_liquido\":\"137.94\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 09:32:34\"}', '{\"ativo\":0}', '2026-01-22 13:09:56'),
(419, 2, 'Excluiu transação de pagamento', 'Transação #7', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":7,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"credito\",\"bandeira\":\"Visa\",\"parcelas\":12,\"taxa_percentual\":\"13.09\",\"valor_bruto\":\"1200.00\",\"valor_taxa\":\"157.08\",\"valor_liquido\":\"1042.92\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 10:10:27\"}', '{\"ativo\":0}', '2026-01-22 13:11:18'),
(420, 2, 'Excluiu transação de pagamento', 'Transação #8', '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":8,\"tipo_origem\":\"os\",\"origem_id\":2035,\"maquina\":\"TOM\",\"forma\":\"credito\",\"bandeira\":\"Mastercard\",\"parcelas\":6,\"taxa_percentual\":\"11.87\",\"valor_bruto\":\"2090.00\",\"valor_taxa\":\"248.08\",\"valor_liquido\":\"1841.92\",\"usuario_id\":2,\"ativo\":1,\"created_at\":\"2026-01-22 10:11:48\"}', '{\"ativo\":0}', '2026-01-22 13:14:14'),
(421, 2, 'Realizou logout do sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 23:12:12'),
(422, 2, 'Realizou login no sistema', NULL, '192.168.0.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-22 23:12:16'),
(423, 2, 'Realizou login no sistema', NULL, '192.168.0.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-24 19:26:47'),
(424, 2, 'Realizou login no sistema', NULL, '192.168.0.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-26 11:58:31'),
(425, 2, 'Realizou login no sistema', NULL, '100.111.196.102', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-26 12:31:39'),
(426, 2, 'Realizou login no sistema', NULL, '100.109.146.66', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', NULL, NULL, '2026-01-26 13:34:38'),
(427, 2, 'Realizou login no sistema', NULL, '192.168.0.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-26 14:12:19'),
(428, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-27 23:19:29'),
(429, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-27 23:22:18'),
(430, 2, 'Excluiu transação de pagamento', 'Transação #12', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"id\":12,\"tipo_origem\":\"atendimento\",\"origem_id\":8,\"maquina\":\"TOM\",\"forma\":\"debito\",\"bandeira\":\"Mastercard\",\"parcelas\":1,\"taxa_percentual\":\"1.40\",\"valor_bruto\":\"320.00\",\"valor_taxa\":\"4.48\",\"valor_liquido\":\"315.52\",\"usuario_id\":2,\"verificado\":0,\"verificado_at\":null,\"verificado_usuario_id\":null,\"ativo\":1,\"created_at\":\"2026-01-27 20:23:01\"}', '{\"ativo\":0}', '2026-01-27 23:23:30'),
(431, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-27 23:55:00'),
(432, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-27 23:55:22'),
(433, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-27 23:55:27'),
(434, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 00:04:44'),
(435, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 00:16:46'),
(436, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 00:16:58'),
(437, 2, 'Criou despesa', 'Despesa #1', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 00:53:39'),
(438, 2, 'Atualizou Ordem de Serviço', 'OS #2035', '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:08:09'),
(439, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:17:11'),
(440, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:19:44'),
(441, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:21:47'),
(442, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:22:28'),
(443, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:22:40'),
(444, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:22:42'),
(445, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:24:05'),
(446, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:24:06'),
(447, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:24:29'),
(448, 2, 'Realizou logout do sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:30:25'),
(449, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 01:30:31'),
(450, 2, 'Realizou login no sistema', NULL, '192.168.0.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-01-28 10:37:50');

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
  `valor_desconto` decimal(10,2) DEFAULT '0.00',
  `status_atual_id` int NOT NULL,
  `status_pagamento` enum('pendente','pago','parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `status_entrega` enum('nao_entregue','entregue') COLLATE utf8mb4_unicode_ci DEFAULT 'nao_entregue',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ordens_servico`
--

INSERT INTO `ordens_servico` (`id`, `cliente_id`, `equipamento_id`, `defeito_relatado`, `laudo_tecnico`, `valor_total_produtos`, `valor_total_servicos`, `valor_total_os`, `valor_desconto`, `status_atual_id`, `status_pagamento`, `status_entrega`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Não funciona jogos de tiro', '', 0.00, 0.00, 0.00, 0.00, 5, 'pendente', 'nao_entregue', 0, '2026-01-05 11:46:40', NULL),
(2000, 1, 1, 'ddddddddd', '', 0.00, 0.00, 0.00, 0.00, 5, 'pendente', 'nao_entregue', 0, '2026-01-05 17:52:31', NULL),
(2001, 65, 2, 'Não liga', '', 0.00, 0.00, 0.00, 0.00, 5, 'pendente', 'nao_entregue', 0, '2026-01-05 18:03:04', NULL),
(2002, 336, 3, 'quebrado', '', 0.00, 0.00, 0.00, 0.00, 5, 'pendente', 'nao_entregue', 0, '2026-01-05 18:15:58', NULL),
(2003, 92, 4, 'Precisa fazer uma limpeza geral, Reinstalar AUTOCAD 2016 (Portugues),  Limpeza e atualização no Windows sem formatar. ', 'Realizada limpeza, Instalação do AUTOCAD 2016 (Portugues-trial), Remoção de vírus, instalação Pacote Office (trial) e Atualização de Sistema. Sistema apenas com 4GB de memória, recomendável expansão.', 520.00, 350.00, 870.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-05 19:08:35', NULL),
(2004, 923, 5, 'Suspeita controladora, apaga os leds ', 'Testado controladora hub de fans, apresentou defeito após 2 horas de uso, recomendamos a troca da peça.', 0.00, 100.00, 100.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-05 19:17:26', NULL),
(2005, 92, 6, ' Bateria está durando pouco', 'Precisa ser trocada bateria, Troca do SSD 240GB e Realizada Limpeza', 898.90, 200.00, 1098.90, 0.00, 5, 'pago', 'entregue', 1, '2026-01-06 11:28:59', NULL),
(2006, 924, 7, 'Fica de desligando.', 'Apresenta falha na fonte, placa mãe estava travada, muito sujo, pasta térmica muito velha, apresentava CPU throttling. Após reset e limpeza a troca da fonte, e pasta térmica. Solucionado os problemas.  ', 130.00, 190.00, 320.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-07 11:26:53', NULL),
(2007, 737, 8, 'Computador não está ligando, falta protetor de parafusos da placa de vídeo, computador está com poeira', 'Computador estava muito empoeirado, após limpeza a limpeza voltou a funcionar normalmente', 0.00, 250.00, 250.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-07 13:14:25', NULL),
(2008, 925, 9, 'Muito lento, algumas teclas não funciona, fica bipando as vezes quando liga, ja deu tela azul, apresenta bastante oscilação, de 7 meses a 1 ano parado. ', 'Bateria não está funcionando, notebook muito empoeirado e com pontos de oxidação, HD precisa ser trocado, apenas 4GB de memória recomendável expansão para 8GB, alto falantes com defeito', 1199.90, 150.00, 1349.90, 0.00, 5, 'pendente', 'nao_entregue', 1, '2026-01-07 17:49:36', NULL),
(2009, 926, 10, 'Teve problema na elétrica do prédio e a fonte queimou', 'Fonte queimada, vazou fluido de fonte na impressora, a carcaça precisa ser limpa ', 390.00, 200.00, 590.00, 0.00, 12, 'pendente', 'nao_entregue', 1, '2026-01-08 17:22:12', NULL),
(2010, 926, 11, 'Teve problema na elétrica do prédio e a fonte queimou', 'Fonte queimada, vazou fluido de fonte na impressora, a carcaça precisa ser limpa ', 390.00, 200.00, 590.00, 0.00, 5, 'pendente', 'entregue', 1, '2026-01-08 17:26:31', NULL),
(2011, 927, 12, 'Computador parou de funcionar', 'Troca da fonte', 260.00, 0.00, 260.00, 75.00, 5, 'pago', 'entregue', 1, '2026-01-08 19:47:25', NULL),
(2012, 927, 13, 'Fazer limpeza geral, formtar SEM BACKUP', 'Limpeza e formatação', 0.00, 390.00, 390.00, 125.00, 5, 'pago', 'entregue', 1, '2026-01-08 19:49:41', NULL),
(2013, 927, 14, 'Limpeza geral, formatar sem backup. ', 'Limpeza e formatação', 0.00, 390.00, 390.00, 125.00, 5, 'pago', 'entregue', 1, '2026-01-08 19:50:35', NULL),
(2014, 828, 15, 'ESTALÇÃO DOS PROGRAMAS BÁSICOS, SOLIDE WORKS E SCKETSHUP 2015', 'Instalação e configuração de programas básicos, Instalação de Solid Works TRIAL e SketchUp TRIAL', 30.00, 390.00, 420.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-09 17:53:23', NULL),
(2015, 912, 16, 'Cliente informou que notebook travou, aparentemente está esquentando demais, cliente acha que a ventilação está com defeito', 'Notebook bastante empoeirado e com Bug no Windows. Foi realizado Limpeza Interna, Troca de Pasta Térmica do Processador e feito a Configuração do Sistema Operacional', 0.00, 390.00, 390.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-10 11:42:08', NULL),
(2016, 928, 17, 'Não está ligando', 'Realizada limpeza e recuperação do sistema', 0.00, 300.00, 300.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-10 12:01:57', NULL),
(2017, 932, 18, 'COmputador apresentou falha', 'Problema na Memoria e fonte', 590.00, 0.00, 590.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-13 20:14:51', NULL),
(2018, 932, 19, 'PC novo', 'PC Completo monitor teclado e mouse.', 2190.00, 0.00, 2190.00, 0.00, 5, 'parcial', 'entregue', 1, '2026-01-13 20:19:28', NULL),
(2019, 923, 20, 'Pen drive de boot', 'Configuração do pen drive', 160.00, 0.00, 160.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-14 12:01:35', NULL),
(2020, 927, 21, 'Limpeza e Formatação sem backup', 'Formatado e limpo', 0.00, 390.00, 390.00, 125.00, 5, 'pago', 'entregue', 1, '2026-01-14 12:05:39', NULL),
(2021, 933, 22, 'Formatar passar pro modo UEFI, deixar habilitado o secure boot. autorizado. ', '', 0.00, 220.00, 220.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-14 18:56:25', NULL),
(2022, 934, 23, 'Cliente precisa ter Unidade D, ver a possibilidade de adicionar mais um SSD ou particionar.', 'Não tem slot de expansão, apenas um slot nvme. NVME particionado em 2 e instalação e configuração do sistema', 0.00, 390.00, 390.00, 190.00, 5, 'pago', 'entregue', 1, '2026-01-15 13:52:32', NULL),
(2023, 935, 24, 'Notebook não liga, mesmo na tomada. Dobradiça Esqueda quebrada, falta borrachinha ( Cuidado Ao abrir) ', 'Tela quebrada, dobradiça, moldura e tampa quebrado, instabilidade na placa mãe', 0.00, 100.00, 100.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-16 11:16:06', NULL),
(2024, 428, 25, 'Trancou uma folha dentro, cliente puxou e a impressora deu erro e parou de imprimir', 'Limpeza E Configuração', 0.00, 290.00, 290.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-16 14:18:59', NULL),
(2025, 936, 26, 'Tela quebrada, Foi aberto e esta descontado a tela. ', 'Necessária troca da tela para funcionamento correto do notebook. Para uma melhor experiência recomendável a instalação de um SSD.', 549.90, 490.00, 1039.90, 240.00, 9, 'pendente', 'nao_entregue', 1, '2026-01-19 13:08:53', NULL),
(2026, 936, 27, 'Não esta ligando, fazer orçamento. ', 'Placa mãe com defeito, peças de primeira geração. não há peças para reposição. Recomendável realizar a troca por peças mais atuais.', 2090.00, 0.00, 2090.00, 400.00, 9, 'pendente', 'nao_entregue', 1, '2026-01-19 13:11:43', NULL),
(2027, 937, 28, 'Problema na conexão /pc Financeiro', 'Instalação do adaptador Wifi para o Cliente.', 149.90, 130.00, 279.90, 50.00, 5, 'pago', 'entregue', 1, '2026-01-19 17:14:53', NULL),
(2028, 938, 29, 'Limpeza Interna e externa', 'Limpeza Interna e externa', 0.00, 299.00, 299.00, 51.00, 5, 'pendente', 'entregue', 1, '2026-01-20 12:46:16', NULL),
(2029, 924, 7, 'Voltou a apresentar o mesmo problema, cliente usou por 3 dias, ontem começou a apresentar a mesma coisa. ', '', 0.00, 0.00, 0.00, 0.00, 2, 'pendente', 'nao_entregue', 1, '2026-01-20 12:47:22', NULL),
(2030, 939, 30, 'Não está ligando', '', 0.00, 0.00, 0.00, 0.00, 5, 'pendente', 'nao_entregue', 1, '2026-01-20 13:17:09', NULL),
(2031, 939, 31, 'Não está ligando', '', 0.00, 0.00, 0.00, 0.00, 5, 'pendente', 'nao_entregue', 1, '2026-01-20 13:19:01', NULL),
(2032, 941, 32, 'Ponta do carregador quebrou dentro do notebook, notebook com marcas de uso e mancha na tampa', 'Remoção da ponta do carregador da entrada da fonte. Carregador danificado, necessário a troca.', 219.90, 0.00, 219.90, 150.00, 5, 'pago', 'entregue', 1, '2026-01-20 14:25:12', NULL),
(2033, 935, 33, 'INSTALAR OS PROGRAMAS PADRÕES', 'Instalação de aplicativos essenciais, incluindo navegação na internet, leitura de documentos, compactação de arquivos, acesso remoto, videoconferência e reprodução de mídia', 130.00, 190.00, 320.00, 100.00, 5, 'pago', 'entregue', 1, '2026-01-20 16:33:07', NULL),
(2034, 942, 34, 'Precisa da instalação do programa PJE Shodo', 'Instalado PJE Shodo', 0.00, 0.00, 0.00, 0.00, 5, 'pago', 'entregue', 1, '2026-01-20 17:18:51', NULL),
(2035, 943, 35, 'Cliente estava com problema no teclado, levou em outra assistência, após a troca do teclado o teclado foi resolvido mas apresentou problema no touchpad.  ', 'Desmontagem e remontagem do equipamento', 0.00, 250.00, 250.00, 100.00, 9, 'pago', 'nao_entregue', 1, '2026-01-20 19:45:07', NULL);

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

--
-- Despejando dados para a tabela `ordens_servico_status_historico`
--

INSERT INTO `ordens_servico_status_historico` (`id`, `ordem_servico_id`, `status_id`, `usuario_id`, `observacao`, `created_at`) VALUES
(1, 2001, 10, NULL, 'Cliente ficou de passa dia 10', '2026-01-05 18:05:01'),
(2, 2001, 11, NULL, 'Cliente fez o pagamento', '2026-01-05 18:07:00'),
(3, 1, 5, NULL, '', '2026-01-05 18:09:30'),
(4, 2002, 7, NULL, 'precisa enviar para poa suspeita de placa mãe ', '2026-01-05 18:18:18'),
(5, 2002, 8, NULL, 'Cliente autorizou envio para Porto Alegre', '2026-01-05 18:20:40'),
(6, 2002, 13, NULL, 'foi enviado para POA', '2026-01-05 18:41:55'),
(7, 2002, 5, NULL, '', '2026-01-05 18:48:52'),
(8, 2005, 1, NULL, 'NÚMERO FFABRICIO MT (TROUXE OS NOTEBOOKS) 51 981401581', '2026-01-06 11:33:25'),
(9, 2005, 2, NULL, '', '2026-01-06 17:11:24'),
(10, 2004, 5, NULL, '', '2026-01-06 19:40:51'),
(11, 2003, 2, NULL, 'OFERECER 4GB MEMÓRIA DDR4 PARA NOTEBOOK', '2026-01-06 20:08:02'),
(12, 2000, 5, NULL, '', '2026-01-06 20:22:32'),
(13, 2001, 5, NULL, '', '2026-01-06 20:23:10'),
(14, 2005, 11, NULL, 'https://www.mercadolivre.com.br/memoria-ram-4gb-ddr4-2400/p/MLB2050950941?pdp_filters=item_id%3AMLB4013734919&#38;from=gshop&#38;matt_tool=61921241&#38;matt_internal_campaign_id=&#38;matt_word=&#38;matt_source=google&#38;matt_campaign_id=22090354535&#38;matt_ad_group_id=173090631276&#38;matt_match_type=&#38;matt_network=g&#38;matt_device=c&#38;matt_creative=727882734663&#38;matt_keyword=&#38;matt_ad_position=&#38;matt_ad_type=pla&#38;matt_merchant_id=735128761&#38;matt_product_id=MLB2050950941-product&#38;matt_product_partition_id=2389906787956&#38;matt_target_id=pla-2389906787956&#38;cq_src=google_ads&#38;cq_cmp=22090354535&#38;cq_net=g&#38;cq_plt=gp&#38;cq_med=pla&#38;gad_source=1&#38;gad_campaignid=22090354535&#38;gbraid=0AAAAAD93qcD3t2W1z-HKIFF8yV_HZ_X6Y&#38;gclid=EAIaIQobChMIr8zvsaf5kQMVaVNIAB055S8nEAQYBSABEgJU8PD_BwE', '2026-01-07 11:24:28'),
(15, 2006, 3, NULL, '', '2026-01-07 17:00:23'),
(16, 2006, 10, NULL, 'Cliente ficou de vir pagar', '2026-01-07 20:19:57'),
(18, 2006, 5, NULL, 'Ficou de vir na loja realizar o pagento no cartão (2X sem juros)', '2026-01-08 18:42:09'),
(19, 2005, 12, NULL, 'Aguardando entrega da bateria', '2026-01-09 11:24:46'),
(20, 2009, 11, NULL, 'Precisa ser encomendada a fonte', '2026-01-09 11:29:31'),
(21, 2010, 4, NULL, 'Fonte a pronta entrega, iremos realizar o serviço', '2026-01-09 11:31:38'),
(22, 2005, 5, NULL, 'Chegou a bateria, notebook foi testado e está pronto para retirar', '2026-01-09 18:16:09'),
(23, 2009, 12, NULL, 'Comprada pelo Mercado livre', '2026-01-10 11:39:56'),
(24, 2007, 5, NULL, 'Foi realizada limpeza e computador está pronto para retirar.', '2026-01-10 11:40:10'),
(25, 2015, 4, NULL, '01082024 senha', '2026-01-10 13:43:05'),
(26, 2015, 5, NULL, '', '2026-01-10 13:46:53'),
(27, 2012, 5, NULL, '', '2026-01-10 13:47:13'),
(28, 2013, 5, NULL, '', '2026-01-10 13:47:19'),
(29, 2010, 5, NULL, '', '2026-01-10 13:49:12'),
(30, 2005, 5, NULL, 'Luciano entregou e recebeu domingo 11/01', '2026-01-12 11:15:04'),
(31, 2003, 5, NULL, 'Luciano entregou e recebeu domingo 11/01', '2026-01-12 11:15:21'),
(32, 2006, 5, NULL, 'Pago no cartão', '2026-01-12 11:18:26'),
(33, 2014, 4, NULL, '', '2026-01-12 11:38:02'),
(34, 2008, 4, NULL, '', '2026-01-12 11:38:15'),
(35, 2016, 5, NULL, '', '2026-01-12 14:00:33'),
(36, 2016, 5, NULL, 'Notebook estava funcionando normalmente, falei com a cliente e disse que seria cobrado limpeza e configuração. 300 reais, foi aprovado, serviço já esta pronto, apenas esperando para testar novamente e avisar ela', '2026-01-12 17:50:35'),
(37, 2014, 4, NULL, 'Já esta tudo pronto apenas falta Solid Works, estou tentando realizar a instalação', '2026-01-12 17:51:20'),
(38, 2008, 10, NULL, '', '2026-01-12 17:56:11'),
(39, 2014, 5, NULL, 'Enviada Mensagem ao cliente', '2026-01-13 13:23:50'),
(40, 2008, 5, NULL, '', '2026-01-14 16:45:01'),
(41, 2021, 1, NULL, 'PIX', '2026-01-14 18:58:15'),
(42, 2021, 5, NULL, '', '2026-01-14 20:46:17'),
(43, 2022, 4, NULL, '', '2026-01-15 16:35:52'),
(44, 2021, 5, NULL, 'Cliente comprou um estabilizador tambem, ficou de vim pagar', '2026-01-16 11:27:30'),
(45, 2023, 2, NULL, 'Foi verificado que existe uma rachadura no lado direito da tela do notebook, cliente já foi informado', '2026-01-16 11:32:37'),
(46, 2022, 5, NULL, '', '2026-01-16 11:54:53'),
(47, 2024, 1, NULL, 'Celular Suelen 51 996907515', '2026-01-16 14:25:03'),
(48, 2023, 10, NULL, 'Serviço sairia muito caro não valeria investimento.', '2026-01-17 13:33:29'),
(49, 2023, 5, NULL, '', '2026-01-19 13:05:52'),
(50, 2024, 5, NULL, '', '2026-01-19 13:20:24'),
(51, 2023, 5, NULL, 'Pago no cartão ', '2026-01-19 13:22:22'),
(52, 2027, 5, NULL, 'Foi atendimento externo', '2026-01-19 17:16:50'),
(53, 2018, 5, NULL, 'Pagto. Parcial no valor de R$ 610,00', '2026-01-20 12:00:07'),
(54, 2024, 5, NULL, 'pago em 2x no cartão', '2026-01-20 12:19:53'),
(55, 2027, 5, NULL, 'Pagamento por PIX', '2026-01-20 17:56:05'),
(56, 2033, 4, NULL, 'Cliente aprovou', '2026-01-20 19:01:01'),
(57, 2025, 2, NULL, '', '2026-01-20 19:09:31'),
(58, 2026, 2, NULL, '', '2026-01-20 19:12:05'),
(59, 2028, 5, NULL, '', '2026-01-20 19:12:30'),
(60, 2029, 2, NULL, 'Testando a horas e funcionando normalmente', '2026-01-20 19:16:09'),
(61, 2033, 5, NULL, '', '2026-01-20 20:02:41'),
(62, 2034, 5, NULL, '', '2026-01-20 20:03:16'),
(63, 2032, 2, NULL, '', '2026-01-20 20:03:51'),
(64, 2035, 2, NULL, '', '2026-01-20 20:04:12'),
(65, 2031, 2, NULL, 'Monitores não apresentaram defeito', '2026-01-21 11:09:00'),
(66, 2030, 2, NULL, 'Monitores não apresentaram defeito', '2026-01-21 11:09:06'),
(67, 2032, 5, NULL, 'Cliente irá passar  a tarde para pegar notebook e novo carregador', '2026-01-21 13:36:02'),
(68, 2035, 9, NULL, '', '2026-01-21 17:58:55'),
(69, 2031, 5, NULL, '', '2026-01-21 17:59:15'),
(70, 2030, 5, NULL, '', '2026-01-21 17:59:19'),
(71, 2025, 9, NULL, '', '2026-01-21 18:01:16'),
(72, 2026, 9, NULL, '', '2026-01-21 18:01:27'),
(73, 2035, 9, 2, '456456', '2026-01-28 01:08:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_transacoes`
--

CREATE TABLE `pagamentos_transacoes` (
  `id` int NOT NULL,
  `tipo_origem` enum('os','atendimento') COLLATE utf8mb4_unicode_ci NOT NULL,
  `origem_id` int NOT NULL,
  `maquina` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forma` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bandeira` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parcelas` int DEFAULT '1',
  `taxa_percentual` decimal(10,2) DEFAULT '0.00',
  `valor_bruto` decimal(10,2) NOT NULL,
  `valor_taxa` decimal(10,2) DEFAULT '0.00',
  `valor_liquido` decimal(10,2) NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT '0',
  `verificado_at` timestamp NULL DEFAULT NULL,
  `verificado_usuario_id` int DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pagamentos_transacoes`
--

INSERT INTO `pagamentos_transacoes` (`id`, `tipo_origem`, `origem_id`, `maquina`, `forma`, `bandeira`, `parcelas`, `taxa_percentual`, `valor_bruto`, `valor_taxa`, `valor_liquido`, `usuario_id`, `verificado`, `verificado_at`, `verificado_usuario_id`, `ativo`, `created_at`) VALUES
(1, 'os', 2032, 'Ton', 'debito', 'mastercard', 1, 0.00, 219.90, 0.00, 219.90, 9, 0, NULL, NULL, 1, '2026-01-21 18:28:39'),
(2, 'os', 2035, 'TOM', 'credito', 'Visa', 3, 8.04, 150.00, 12.06, 137.94, 2, 0, NULL, NULL, 0, '2026-01-22 12:32:34'),
(3, 'os', 2035, 'TOM', 'debito', 'Visa', 9, 1.40, 15000.00, 210.00, 14790.00, 2, 0, NULL, NULL, 0, '2026-01-22 13:05:51'),
(4, 'os', 2035, 'TOM', 'credito', 'Visa', 9, 13.07, 15000.00, 1960.50, 13039.50, 2, 0, NULL, NULL, 0, '2026-01-22 13:06:51'),
(5, 'os', 2035, 'TOM', 'credito', 'Visa', 12, 13.09, 1890.00, 247.40, 1642.60, 2, 0, NULL, NULL, 0, '2026-01-22 13:08:20'),
(6, 'os', 2035, 'TOM', 'credito', 'Visa', 12, 13.09, 1200.00, 157.08, 1042.92, 2, 0, NULL, NULL, 0, '2026-01-22 13:09:18'),
(7, 'os', 2035, 'TOM', 'credito', 'Visa', 12, 13.09, 1200.00, 157.08, 1042.92, 2, 0, NULL, NULL, 0, '2026-01-22 13:10:27'),
(8, 'os', 2035, 'TOM', 'credito', 'Mastercard', 6, 11.87, 2090.00, 248.08, 1841.92, 2, 0, NULL, NULL, 0, '2026-01-22 13:11:48'),
(9, 'os', 2035, 'TOM', 'dinheiro', '', 1, 0.00, 100.00, 0.00, 100.00, 2, 0, NULL, NULL, 1, '2026-01-22 16:06:25'),
(10, 'os', 2033, 'TOM', 'dinheiro', '', 1, 0.00, 110.00, 0.00, 110.00, 2, 1, '2026-01-26 16:17:53', 2, 1, '2026-01-22 16:23:39'),
(11, 'os', 2035, 'TOM', 'credito', 'Visa', 1, 3.30, 150.00, 4.95, 145.05, 2, 1, '2026-01-26 16:17:48', 2, 1, '2026-01-26 12:23:44'),
(12, 'atendimento', 8, 'TOM', 'debito', 'Mastercard', 1, 1.40, 320.00, 4.48, 315.52, 2, 0, NULL, NULL, 0, '2026-01-27 23:23:01'),
(13, 'atendimento', 8, 'TOM', 'debito', '', 1, 0.00, 360.00, 0.00, 360.00, 2, 0, NULL, NULL, 1, '2026-01-27 23:51:21');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos_servicos`
--

CREATE TABLE `produtos_servicos` (
  `id` int NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('produto','servico') COLLATE utf8mb4_unicode_ci NOT NULL,
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
(10, 'LIMPEZA INTERNA COMPUTADOR', 'servico', 0.00, 0.00, 200.00, 1, '2026-01-03 02:21:36', '2026-01-06 20:18:54'),
(11, 'Limpeza interna impressora', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(12, 'FORMATACAO SISTEMA OPERACIONAL', 'servico', 0.00, 0.00, 190.00, 1, '2026-01-03 02:21:36', '2026-01-06 20:18:23'),
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
(23, 'SSD SATA 120 GB', 'produto', 124.00, 219.90, 0.00, 1, '2026-01-03 02:21:36', '2026-01-08 12:40:19'),
(24, 'SSD SATA 240 GB', 'produto', 188.00, 299.90, 0.00, 1, '2026-01-03 02:21:36', '2026-01-08 12:35:48'),
(25, 'SSD SATA 480 GB', 'produto', 366.00, 599.90, 0.00, 1, '2026-01-03 02:21:36', '2026-01-08 12:37:16'),
(26, 'SSD SATA 1 TB', 'produto', 640.00, 1024.00, 0.00, 1, '2026-01-03 02:21:36', '2026-01-06 20:28:28'),
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
(37, 'FONTE ATX 200 W', 'produto', 0.00, 0.00, 100.00, 1, '2026-01-03 02:21:36', '2026-01-13 10:59:51'),
(38, 'Fonte ATX 400 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(39, 'Fonte ATX 500 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(40, 'Fonte ATX 600 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(41, 'Fonte ATX 750 W', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(42, 'HD SATA 500 GB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(43, 'HD SATA 1 TB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(44, 'HD SATA 2 TB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(45, 'Pasta termica', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(46, 'Cabo SATA', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(47, 'FONTE NOTEBOOK LENOVO', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', '2026-01-21 13:16:56'),
(48, 'Teclado USB', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(49, 'MOUSE USB', 'produto', 10.00, 30.00, 0.00, 1, '2026-01-03 02:21:36', '2026-01-13 20:08:55'),
(50, 'ADAPTADOR USB WI-FI', 'produto', 49.90, 149.90, 0.00, 1, '2026-01-03 02:21:36', '2026-01-19 17:12:41'),
(51, 'Cooler para processador', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-03 02:21:36', NULL),
(52, 'Bateria Notebook', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-06 20:10:19', NULL),
(53, 'Instalação de Software', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-06 20:11:31', NULL),
(54, 'Atualização do Sistema', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-06 20:11:54', NULL),
(55, 'Limpeza do Sistema', '', 0.00, 0.00, 0.00, 1, '2026-01-06 20:12:13', NULL),
(56, 'Clonagem Sistema', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-06 20:12:32', NULL),
(57, 'Laudo técnico', 'servico', 0.00, 0.00, 100.00, 1, '2026-01-06 20:17:18', NULL),
(58, 'LIMPEZA DESKTOP GAMER', 'servico', 0.00, 0.00, 350.00, 1, '2026-01-06 20:19:25', NULL),
(59, 'DESLOCAMENTO', 'servico', 0.00, 0.00, 0.00, 1, '2026-01-07 17:01:05', NULL),
(60, 'DESLOCAMENTO', 'servico', 0.00, 0.00, 30.00, 1, '2026-01-07 17:05:29', NULL),
(61, 'FONTE IMPRESSORA', 'produto', 70.00, 290.00, 100.00, 1, '2026-01-08 17:32:29', NULL),
(62, 'PC COMPLETO', 'produto', 1080.00, 2190.00, 0.00, 1, '2026-01-13 20:20:19', NULL),
(63, 'PEN DRIVE 32GB', 'produto', 28.00, 59.90, 0.00, 1, '2026-01-14 12:03:44', NULL),
(64, 'PARTICIONAR DISCO', 'servico', 0.00, 120.00, 0.00, 1, '2026-01-16 19:53:17', NULL),
(65, 'ADAPTADOR WIFI', 'produto', 59.90, 149.90, 0.00, 1, '2026-01-19 17:11:40', NULL),
(66, 'ATENDIMENTO TECNICO', 'servico', 0.00, 150.00, 0.00, 1, '2026-01-19 17:12:17', NULL),
(67, 'FLAT HD /SSD NOTEBOOK', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-20 17:20:46', NULL),
(68, 'REPARO ENTRADA DA FONTE', 'servico', 0.00, 0.00, 150.00, 1, '2026-01-21 13:02:56', NULL),
(69, 'FONTE NOTEBOOK DELL', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 13:17:08', NULL),
(70, 'FONTE NOTEBOOK POSITIVO', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 13:17:29', NULL),
(71, 'FONTE NOTEBOOK HP', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 13:17:48', NULL),
(72, 'FONTE NOTEBOOK ACER', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 13:17:55', NULL),
(73, 'FONTE NOTEBOOK SAMSUNG', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 13:18:11', NULL),
(74, 'MEMORIA DDR3', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 13:59:00', NULL),
(75, 'REMONTAGEM', 'servico', 0.00, 250.00, 350.00, 1, '2026-01-21 16:58:44', '2026-01-21 16:59:01'),
(76, 'PLACA MãE INTEL 1155', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 18:11:35', NULL),
(77, 'PROCESSADOR INTEL I3', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 18:11:53', NULL),
(78, 'PROCESSADOR INTEL I5', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 18:12:05', NULL),
(79, 'PROCESSADOR INTEL I7', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 18:12:16', NULL),
(80, 'MONITOR 18\'', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 19:43:24', NULL),
(81, 'MONITOR 19\'', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 19:43:38', NULL),
(82, 'MONITOR 20\'', 'produto', 0.00, 0.00, 0.00, 1, '2026-01-21 19:43:54', NULL);

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
(13, 'Em POA', '#1abc9c', 13),
(14, 'Autorizado', '#00b894', 14);

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
(1, 'Administrador', 'admin@admin.com', '$2y$10$WUvv8ExQIwPrIsTQrAiGN.CKaijvrKE3.8v9oVYpvMdYXmKkbjGZy', 'usuario', 0, 1, '2025-12-26 02:18:36'),
(2, 'hedigar', 'hedigar9@gmail.com', '$2y$10$5KvEhQ./Mu3InJQExe8o4eBRWI1vTN9/5E.si5g9.5szof8cugKBG', 'superadmin', 0, 1, '2025-12-28 22:01:51'),
(3, 'hedigar', 'hedigar1@gmail.com', '$2y$10$1A7sjgpcJKeBJXi3qjW6NuTKo7SD1j1ZvgG0EGMWAevdATE6XrhY2', 'usuario', 0, 0, '2025-12-28 22:49:22'),
(4, 'hedigar122', 'hedigar2@gmail.com', '$2y$10$kY9aHTN8F5UH1RWLhnltLeALutTouYpGlSmv56KOUl7NrUmwFZsLe', 'tecnico', 0, 0, '2025-12-28 22:49:32'),
(6, 'Sonia', 'sonia@gmail.com', '$2y$10$C0OD7oAkEAMLPW/XgH/7B.kGEkSSYKVw2uYD6gIHkJQwJTvJFzlWK', 'tecnico', 0, 1, '2026-01-04 20:50:21'),
(7, 'Pedro Luciano', 'luciano@s10.com', '$2y$10$DY39/J5zN/JDPZ54NKQow.VuC7RU2p1ojJTLbQhUMCeoxnmgwBSQG', 'admin', 0, 1, '2026-01-04 20:50:48'),
(8, 'Pedro Lelio', 'pedro@lelio.com', '$2y$10$HkadzDP2qNBHGvAUKuVgA.cvPVlw2g2Y1UGnFBcKcaJwinSgazUkC', 'usuario', 0, 1, '2026-01-04 20:51:18'),
(9, 'Allan', 'allan@zoka.com', '$2y$10$twG1ZpSkXFYVXkI3pIOh8umJo8mvb.jgtJvaIx5VI/rSHhtJijjMW', 'tecnico', 0, 1, '2026-01-04 20:51:37');

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
-- Índices de tabela `despesas`
--
ALTER TABLE `despesas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria_id` (`categoria_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_data_despesa` (`data_despesa`),
  ADD KEY `idx_status_pagamento` (`status_pagamento`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `despesas_categorias`
--
ALTER TABLE `despesas_categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nome_categoria` (`nome`);

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
  ADD KEY `ordem_servico_id` (`ordem_servico_id`),
  ADD KEY `fk_item_atendimento_ext` (`atendimento_externo_id`);

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
-- Índices de tabela `pagamentos_transacoes`
--
ALTER TABLE `pagamentos_transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pagamentos_origem` (`tipo_origem`,`origem_id`),
  ADD KEY `idx_pagamentos_created_at` (`created_at`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=945;

--
-- AUTO_INCREMENT de tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `despesas`
--
ALTER TABLE `despesas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `despesas_categorias`
--
ALTER TABLE `despesas_categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `itens_ordem_servico`
--
ALTER TABLE `itens_ordem_servico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2036;

--
-- AUTO_INCREMENT de tabela `ordens_servico_status_historico`
--
ALTER TABLE `ordens_servico_status_historico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de tabela `pagamentos_transacoes`
--
ALTER TABLE `pagamentos_transacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `produtos_servicos`
--
ALTER TABLE `produtos_servicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de tabela `status_os`
--
ALTER TABLE `status_os`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  ADD CONSTRAINT `fk_item_atendimento_ext` FOREIGN KEY (`atendimento_externo_id`) REFERENCES `atendimentos_externos` (`id`) ON DELETE CASCADE,
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
