-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 14, 2025 alle 11:57
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `esercitazione_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `autore` varchar(180) NOT NULL,
  `quantity` int(11) NOT NULL,
  `daily_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250314094524', '2025-03-14 09:45:27', 98);

-- --------------------------------------------------------

--
-- Struttura della tabella `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `photo`
--

CREATE TABLE `photo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `photo`
--

INSERT INTO `photo` (`id`, `user_id`, `path`) VALUES
(1, 1, 'user-png-67d408b31c14e.png');

-- --------------------------------------------------------

--
-- Struttura della tabella `race`
--

CREATE TABLE `race` (
  `id` int(11) NOT NULL,
  `place` varchar(180) NOT NULL,
  `date` datetime NOT NULL,
  `coeff` double NOT NULL,
  `max_partecipanti` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `race`
--

INSERT INTO `race` (`id`, `place`, `date`, `coeff`, `max_partecipanti`) VALUES
(1, 'Roma', '2025-04-10 00:00:00', 1.15, 1000),
(2, 'Milano', '2025-05-15 00:00:00', 1.2, 1500),
(3, 'Napoli', '2025-06-01 00:00:00', 1.1, 800),
(4, 'Torino', '2025-07-05 00:00:00', 1.25, 1200),
(5, 'Firenze', '2025-08-20 00:00:00', 1.18, 900),
(6, 'Bologna', '2025-09-10 00:00:00', 1.22, 1100),
(7, 'Venezia', '2025-10-05 00:00:00', 1.3, 1300);

-- --------------------------------------------------------

--
-- Struttura della tabella `refresh_token`
--

CREATE TABLE `refresh_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device` varchar(180) NOT NULL,
  `refresh_token` varchar(300) DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `refresh_token`
--

INSERT INTO `refresh_token` (`id`, `user_id`, `device`, `refresh_token`, `roles`) VALUES
(1, 1, '127.0.0.1:PostmanRuntime/7.43.2', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NDE5NDU1NjcsImV4cCI6MTc0MjU1MDM2NywiZGF0YSI6eyJlbWFpbCI6InRlc3RAZXhhbXBsZS5pdCIsImlkIjoxLCJyb2xlcyI6W119fQ.APZ7qpQje5yLpja97HA1x8uInfmCdIblKyfX4CDUD-E', '[]'),
(2, 1, '127.0.0.1:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NDE5NDY2NzUsImV4cCI6MTc0MjU1MTQ3NSwiZGF0YSI6eyJlbWFpbCI6InRlc3RAZXhhbXBsZS5pdCIsImlkIjoxLCJyb2xlcyI6WyJST0xFX0FETUlOIl19fQ.wsUhsP0iDfdjk5dHREJKvC4JQx9OPZgmXdFGXVtz0D4', '[]');

-- --------------------------------------------------------

--
-- Struttura della tabella `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `can_create` tinyint(1) NOT NULL,
  `can_read` tinyint(1) NOT NULL,
  `can_update` tinyint(1) NOT NULL,
  `can_delete` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `role`
--

INSERT INTO `role` (`id`, `name`, `can_create`, `can_read`, `can_update`, `can_delete`) VALUES
(1, 'ROLE_ADMIN', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(180) NOT NULL,
  `surname` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `surname`) VALUES
(1, 'test@example.it', '$2y$13$5.bW.SYuXsH1ncMNfL.WX.SYzRPEc1KxbEUEIRKbNX349hHL4F9TO', 'pierone', 'cipolla');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(1, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indici per le tabelle `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indici per le tabelle `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_14B78418A76ED395` (`user_id`);

--
-- Indici per le tabelle `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `refresh_token`
--
ALTER TABLE `refresh_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_id` (`id`),
  ADD KEY `IDX_C74F2195A76ED395` (`user_id`);

--
-- Indici per le tabelle `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_NAME` (`name`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Indici per le tabelle `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  ADD KEY `IDX_2DE8C6A3D60322AC` (`role_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `photo`
--
ALTER TABLE `photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `race`
--
ALTER TABLE `race`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `refresh_token`
--
ALTER TABLE `refresh_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `FK_14B78418A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Limiti per la tabella `refresh_token`
--
ALTER TABLE `refresh_token`
  ADD CONSTRAINT `FK_C74F2195A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Limiti per la tabella `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
