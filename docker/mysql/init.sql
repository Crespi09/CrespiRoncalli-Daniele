-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 26, 2025 alle 09:35
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
(1, 1, 'Immagine-2025-06-09-102734-png-6846b770c112b.png');

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
(1, 'Napoli', '2025-06-10 14:15:49', 5.7, 500),
(2, 'Bergamo', '2025-06-10 14:15:49', 5.6, 800);

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
(1, 1, '127.0.0.1:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NDk0NjI0NDEsImV4cCI6MTc1MDA2NzI0MSwiZGF0YSI6eyJlbWFpbCI6InRlc3RAaS5pIiwiaWQiOjEsInJvbGVzIjpbXX19.mBvQsPuT4NJ8jblTVrf7sFPVg32DswFNdkZ29w_xqGk', '[]'),
(2, 1, '127.0.0.1:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NDk1NTc0NzcsImV4cCI6MTc1MDE2MjI3NywiZGF0YSI6eyJlbWFpbCI6InRlc3RAaS5pIiwiaWQiOjEsInJvbGVzIjpbXX19.CupTQTY9fw5u7r9-i0qPOk7i-ZfcKxaRwLJuxyaeFBw', '[]'),
(4, 1, '127.0.0.1:PostmanRuntime/7.44.0', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NDk1NjE4MTIsImV4cCI6MTc1MDE2NjYxMiwiZGF0YSI6eyJlbWFpbCI6InRlc3RAaS5pIiwiaWQiOjEsInJvbGVzIjpbXX19.KbQe9qN-ILQnPjuAq7fJGpq8BkrA6VXBWtl3zOJfKPk', '[]'),
(5, 1, '127.0.0.1:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NTAzMjEyMDYsImV4cCI6MTc1MDkyNjAwNiwiZGF0YSI6eyJlbWFpbCI6InRlc3RAaS5pIiwiaWQiOjEsInJvbGVzIjpbXX19.W9AoHRxLAzktJ_jls1JonlLsdCP1MnXxtLmxjjJLfIo', '[]'),
(6, 1, '127.0.0.1:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NTAzMjEyMDcsImV4cCI6MTc1MDkyNjAwNywiZGF0YSI6eyJlbWFpbCI6InRlc3RAaS5pIiwiaWQiOjEsInJvbGVzIjpbXX19.FfbIBcd-pdnIMshRNophpZQG4y1qjQAfAnNWC-B621g', '[]'),
(7, 1, '127.0.0.1:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJhdWQiOiIiLCJpYXQiOjE3NTAzMjkyOTIsImV4cCI6MTc1MDkzNDA5MiwiZGF0YSI6eyJlbWFpbCI6InRlc3RAaS5pIiwiaWQiOjEsInJvbGVzIjpbXX19.cGVIQcAmcKkyJFj7k63U-xTXOC6jmTIFatXodTEWcy0', '[]');

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
(1, 'admin', '$2y$13$jwMTncZ3nciER024WpsKhe10xXVU5GNHQsoTTgUrXYH01WLtPlnU6', 'admin', 'admin');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_race`
--

CREATE TABLE `user_race` (
  `user_id` int(11) NOT NULL,
  `race_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `total` double NOT NULL,
  `km` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `user_race`
--

INSERT INTO `user_race` (`user_id`, `race_id`, `id`, `name`, `size`, `total`, `km`) VALUES
(1, 1, 6, 'martina cagliani', 'S', 85.5, 10),
(1, 1, 15, 'gianluca pierone', 'L', 85.5, 10),
(1, 1, 16, '4 2', 'M', 85.5, 10);

-- --------------------------------------------------------

--
-- Struttura della tabella `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indici per le tabelle `user_race`
--
ALTER TABLE `user_race`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A0EEF766A76ED395` (`user_id`),
  ADD KEY `IDX_A0EEF7666E59D40D` (`race_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `refresh_token`
--
ALTER TABLE `refresh_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `user_race`
--
ALTER TABLE `user_race`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
-- Limiti per la tabella `user_race`
--
ALTER TABLE `user_race`
  ADD CONSTRAINT `FK_A0EEF7666E59D40D` FOREIGN KEY (`race_id`) REFERENCES `race` (`id`),
  ADD CONSTRAINT `FK_A0EEF766A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

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
