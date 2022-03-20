-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 20/03/2022 às 15:43
-- Versão do servidor: 5.7.37-0ubuntu0.18.04.1
-- Versão do PHP: 7.3.33-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `wx_mala_direta`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carregamentos_lista`
--

CREATE TABLE `carregamentos_lista` (
  `wx_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nome_tabela` varchar(100) NOT NULL,
  `campos` text,
  `campo_identificador` varchar(255) DEFAULT NULL,
  `campo_email` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Informação dos carregamentos de usuários';

-- --------------------------------------------------------

--
-- Estrutura para tabela `carregamentos_modelo`
--

CREATE TABLE `carregamentos_modelo` (
  `wx_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `wx_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `smtp_host` varchar(255) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `email_from` varchar(100) NOT NULL,
  `email_from_name` varchar(100) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `emails_enviados`
--

CREATE TABLE `emails_enviados` (
  `wx_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `corpo` text NOT NULL,
  `registros_enviados` text,
  `registros_nao_enviados` text,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carregamentos_lista`
--
ALTER TABLE `carregamentos_lista`
  ADD PRIMARY KEY (`wx_id`);

--
-- Índices de tabela `carregamentos_modelo`
--
ALTER TABLE `carregamentos_modelo`
  ADD PRIMARY KEY (`wx_id`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`wx_id`);

--
-- Índices de tabela `emails_enviados`
--
ALTER TABLE `emails_enviados`
  ADD PRIMARY KEY (`wx_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carregamentos_lista`
--
ALTER TABLE `carregamentos_lista`
  MODIFY `wx_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carregamentos_modelo`
--
ALTER TABLE `carregamentos_modelo`
  MODIFY `wx_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `wx_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `emails_enviados`
--
ALTER TABLE `emails_enviados`
  MODIFY `wx_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
