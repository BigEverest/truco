-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 05-Nov-2017 às 21:41
-- Versão do servidor: 10.1.22-MariaDB
-- PHP Version: 7.0.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `truco`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cartas`
--

CREATE TABLE `cartas` (
  `id` int(11) NOT NULL,
  `idJog` int(11) NOT NULL,
  `carta1` int(11) NOT NULL,
  `carta2` int(11) NOT NULL,
  `carta3` int(11) NOT NULL,
  `sala` int(11) NOT NULL,
  `cadeira` int(11) NOT NULL,
  `cartaJogada` int(11) NOT NULL,
  `podeJogar` tinyint(1) NOT NULL,
  `podeTruco` int(1) NOT NULL,
  `maoDe11` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `cartas`
--

INSERT INTO `cartas` (`id`, `idJog`, `carta1`, `carta2`, `carta3`, `sala`, `cadeira`, `cartaJogada`, `podeJogar`, `podeTruco`, `maoDe11`) VALUES
(1, 1, 26, 0, 0, 1, 1, 0, 0, 0, 0),
(2, 2, 0, 24, 21, 1, 2, 0, 1, 1, 0),
(3, 3, 22, 10, 3, 1, 3, 0, 0, 0, 0),
(4, 4, 11, 1, 25, 1, 4, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogadas`
--

CREATE TABLE `jogadas` (
  `id` int(11) NOT NULL,
  `sala` int(11) NOT NULL,
  `carta1` int(11) NOT NULL,
  `carta2` int(11) NOT NULL,
  `carta3` int(11) NOT NULL,
  `carta4` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `jogadas`
--

INSERT INTO `jogadas` (`id`, `sala`, `carta1`, `carta2`, `carta3`, `carta4`) VALUES
(1, 1, 1, 6, 3, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `jogadores`
--

CREATE TABLE `jogadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `salas`
--

CREATE TABLE `salas` (
  `id` int(11) NOT NULL,
  `quedasTime1` int(11) NOT NULL,
  `quedasTime2` int(11) NOT NULL,
  `vitoriasTime1` int(11) NOT NULL,
  `vitoriasTime2` int(11) NOT NULL,
  `cadeiraMao` int(11) NOT NULL,
  `valendo` int(11) NOT NULL,
  `jogada1` int(11) NOT NULL,
  `jogada2` int(11) NOT NULL,
  `jogada3` int(11) NOT NULL,
  `jogadaAtual` int(11) NOT NULL,
  `qtdJogadores` int(11) NOT NULL,
  `reload` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `salas`
--

INSERT INTO `salas` (`id`, `quedasTime1`, `quedasTime2`, `vitoriasTime1`, `vitoriasTime2`, `cadeiraMao`, `valendo`, `jogada1`, `jogada2`, `jogada3`, `jogadaAtual`, `qtdJogadores`, `reload`) VALUES
(1, 0, 0, 2, 1, 1, 1, 1, 2, 0, 3, 2, 227);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cartas`
--
ALTER TABLE `cartas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jogadas`
--
ALTER TABLE `jogadas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jogadores`
--
ALTER TABLE `jogadores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cartas`
--
ALTER TABLE `cartas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `jogadas`
--
ALTER TABLE `jogadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `jogadores`
--
ALTER TABLE `jogadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
