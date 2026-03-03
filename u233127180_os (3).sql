-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 03/03/2026 às 12:32
-- Versão do servidor: 11.8.3-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u233127180_os`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos_externos`
--

CREATE TABLE `atendimentos_externos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `ordem_servico_id` int(11) DEFAULT NULL,
  `descricao_problema` text NOT NULL,
  `endereco_visita` varchar(255) NOT NULL,
  `data_agendada` datetime DEFAULT NULL,
  `status` enum('pendente','agendado','em_deslocamento','concluido','cancelado') DEFAULT 'pendente',
  `pagamento` enum('não','parcial','pago') DEFAULT 'não',
  `valor_deslocamento` decimal(10,2) DEFAULT 0.00,
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `observacoes_tecnicas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `hora_entrada` time DEFAULT NULL,
  `hora_saida` time DEFAULT NULL,
  `tempo_total` varchar(50) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `equipamentos` varchar(255) DEFAULT NULL,
  `detalhes_servico` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades_pessoais`
--

CREATE TABLE `atividades_pessoais` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tempo_minutos` int(11) NOT NULL,
  `local` enum('Presencial','Home Office','Cliente') DEFAULT 'Presencial',
  `categoria` enum('operacional','supervisao','estrategico') NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `origem` enum('manual','log','atendimento','os','orcamento','sistema') DEFAULT 'manual',
  `origem_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(150) NOT NULL,
  `tipo_pessoa` varchar(20) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefone_principal` varchar(20) DEFAULT NULL,
  `telefone_secundario` varchar(20) DEFAULT NULL,
  `endereco_logradouro` varchar(150) DEFAULT NULL,
  `endereco_numero` varchar(10) DEFAULT NULL,
  `endereco_bairro` varchar(60) DEFAULT NULL,
  `endereco_cidade` varchar(60) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_gerais`
--

CREATE TABLE `configuracoes_gerais` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `valor` longtext DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `despesas`
--

CREATE TABLE `despesas` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_despesa` date NOT NULL,
  `status_pagamento` enum('pendente','pago','parcial') DEFAULT 'pendente',
  `metodo_pagamento` enum('dinheiro','cartao','pix','transferencia','boleto','outro') DEFAULT 'outro',
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `despesas_categorias`
--

CREATE TABLE `despesas_categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipamentos`
--

CREATE TABLE `equipamentos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `ordem_servico_id` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `senha` varchar(100) DEFAULT NULL,
  `acessorios` text DEFAULT NULL,
  `possui_fonte` tinyint(1) DEFAULT 0,
  `sn_fonte` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_ordem_servico`
--

CREATE TABLE `itens_ordem_servico` (
  `id` int(11) NOT NULL,
  `ordem_servico_id` int(11) DEFAULT NULL,
  `atendimento_externo_id` int(11) DEFAULT NULL,
  `tipo_item` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` decimal(10,2) DEFAULT 1.00,
  `custo` decimal(10,2) DEFAULT 0.00,
  `valor_unitario` decimal(10,2) DEFAULT 0.00,
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'pendente',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `valor_custo` decimal(10,2) DEFAULT 0.00,
  `valor_mao_de_obra` decimal(10,2) DEFAULT 0.00,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `porcentagem_margem` decimal(10,2) DEFAULT 0.00,
  `comprar_peca` tinyint(1) DEFAULT 0,
  `link_fornecedor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(150) DEFAULT NULL,
  `referencia` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `dados_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_anteriores`)),
  `dados_novos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_novos`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico`
--

CREATE TABLE `ordens_servico` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `equipamento_id` int(11) DEFAULT NULL,
  `defeito_relatado` text NOT NULL,
  `laudo_tecnico` text DEFAULT NULL,
  `valor_total_produtos` decimal(10,2) DEFAULT 0.00,
  `valor_total_servicos` decimal(10,2) DEFAULT 0.00,
  `valor_total_os` decimal(10,2) DEFAULT 0.00,
  `valor_desconto` decimal(10,2) DEFAULT 0.00,
  `status_atual_id` int(11) NOT NULL,
  `status_pagamento` enum('pendente','pago','parcial') DEFAULT 'pendente',
  `status_entrega` enum('nao_entregue','entregue') DEFAULT 'nao_entregue',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico_status_historico`
--

CREATE TABLE `ordens_servico_status_historico` (
  `id` int(11) NOT NULL,
  `ordem_servico_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos_transacoes`
--

CREATE TABLE `pagamentos_transacoes` (
  `id` int(11) NOT NULL,
  `tipo_origem` enum('os','atendimento') NOT NULL,
  `origem_id` int(11) NOT NULL,
  `maquina` varchar(100) DEFAULT NULL,
  `forma` varchar(50) NOT NULL,
  `bandeira` varchar(50) DEFAULT NULL,
  `parcelas` int(11) DEFAULT 1,
  `taxa_percentual` decimal(10,2) DEFAULT 0.00,
  `valor_bruto` decimal(10,2) NOT NULL,
  `valor_taxa` decimal(10,2) DEFAULT 0.00,
  `valor_liquido` decimal(10,2) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  `verificado_at` timestamp NULL DEFAULT NULL,
  `verificado_usuario_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos_servicos`
--

CREATE TABLE `produtos_servicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `tipo` enum('produto','servico') NOT NULL,
  `custo` decimal(10,2) DEFAULT 0.00,
  `valor_venda` decimal(10,2) DEFAULT 0.00,
  `mao_de_obra` decimal(10,2) DEFAULT 0.00,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_os`
--

CREATE TABLE `status_os` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cor` varchar(7) DEFAULT '#000000',
  `ordem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` varchar(50) NOT NULL,
  `trocar_senha` tinyint(1) DEFAULT 1,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Índices de tabela `atividades_pessoais`
--
ALTER TABLE `atividades_pessoais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_usuario_origem` (`usuario_id`,`origem`,`origem_id`),
  ADD KEY `idx_atividades_usuario_data` (`usuario_id`,`data_hora`),
  ADD KEY `idx_atividades_categoria` (`categoria`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atividades_pessoais`
--
ALTER TABLE `atividades_pessoais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `despesas`
--
ALTER TABLE `despesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `despesas_categorias`
--
ALTER TABLE `despesas_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_ordem_servico`
--
ALTER TABLE `itens_ordem_servico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ordens_servico_status_historico`
--
ALTER TABLE `ordens_servico_status_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos_transacoes`
--
ALTER TABLE `pagamentos_transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos_servicos`
--
ALTER TABLE `produtos_servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_os`
--
ALTER TABLE `status_os`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
