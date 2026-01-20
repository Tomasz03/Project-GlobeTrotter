-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 14, 2026 at 07:15 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `globetrotter_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('new','open','closed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user_id`, `admin_id`, `subject`, `status`, `created_at`, `updated_at`) VALUES
(21, 1, 1, 'Wsparcie techniczne/reklamacja', 'open', '2026-01-14 17:26:40', '2026-01-14 17:26:48'),
(22, 2, 1, 'Zapytanie o rezerwację', 'new', '2026-01-14 17:36:12', '2026-01-14 17:36:12'),
(23, 2, 1, 'Zapytanie o rezerwację', 'new', '2026-01-14 18:11:34', '2026-01-14 18:11:34');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `content`, `is_read`, `sent_at`) VALUES
(27, 21, 1, 'wiadomosc', 0, '2026-01-14 17:26:40'),
(28, 21, 1, 'czesc', 0, '2026-01-14 17:26:48'),
(29, 22, 2, 'wiadomosc od Tomasza', 1, '2026-01-14 17:36:12'),
(30, 23, 2, 'wiadomosc', 1, '2026-01-14 18:11:34');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `reserved_slots` int(11) NOT NULL,
  `reservation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `tour_id`, `reserved_slots`, `reservation_date`, `status`) VALUES
(2, 2, 2, 8, '2026-01-08 10:19:28', 'paid'),
(3, 2, 5, 10, '2026-01-09 16:25:55', 'paid'),
(4, 2, 4, 10, '2026-01-09 16:26:31', 'pending'),
(13, 1, 1, 5, '2026-01-09 21:15:22', 'cancelled'),
(14, 1, 3, 3, '2026-01-09 21:50:08', 'paid'),
(17, 1, 4, 2, '2026-01-10 18:14:29', 'paid');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `duration_days` int(11) NOT NULL,
  `departure_time` time DEFAULT NULL,
  `return_time` time DEFAULT NULL,
  `transport_type` varchar(50) NOT NULL,
  `max_slots` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `country`, `description`, `price`, `start_date`, `end_date`, `duration_days`, `departure_time`, `return_time`, `transport_type`, `max_slots`, `image_url`, `created_at`) VALUES
(1, 'Odkryj Krainę Fjordów - Norwegia', 'Norwegia', '10-dniowa wyprawa wzdłuż zachodniego wybrzeża Norwegii. Obejmuje rejsy po słynnych fjordach (Geirangerfjord, Sognefjord), trekking w górach i noclegi w urokliwych domkach. Idealne dla miłośników przyrody i pieszych wędrówek. Cena zawiera loty i wyżywienie typu HB.', 6850.00, '2026-06-15', '2026-06-24', 10, '08:30:00', '22:00:00', 'Samolot', 25, 'images/Norwegia.jpg', '2026-01-08 10:12:35'),
(2, 'Tajemnice Starożytnych Cywilizacji - Meksyk', 'Meksyk', '12 dni śladami Majów i Azteków. Zwiedzanie Chichén Itzá, Teotihuacán oraz relaks na plażach Tulum. Głębokie zanurzenie w historii, kulturze i kuchni meksykańskiej. W cenie transport wewnętrzny i przewodnicy.', 5990.00, '2026-11-05', '2026-11-16', 12, '10:00:00', '18:45:00', 'Samolot', 30, 'images/Meksyk.jpeg', '2026-01-08 10:12:35'),
(3, 'Szlakiem Piękna Toskanii i Smaków Włoch', 'Włochy', '7 dni autokarowej objazdówki przez Toskanię. Odwiedzimy Florencję, Sienę i San Gimignano, degustując lokalne wina i oliwy. Pobyt w agroturystyce, pełne wyżywienie. Opcja dla smakoszy i miłośników malowniczych krajobrazów.', 3150.00, '2026-09-08', '2026-09-14', 7, '05:00:00', '23:30:00', 'Autokar', 40, 'images/Włochy.jpg', '2026-01-08 10:12:35'),
(4, 'Miejski Weekend w Tokio', 'Japonia', '4-dniowy, intensywny wyjazd do stolicy Japonii. Odwiedźmy futurystyczne dzielnice, historyczne świątynie oraz słynny rynek Tsukiji. Wycieczka z lokalnym przewodnikiem. Noclegi w hotelu w centrum miasta.', 4100.00, '2026-04-03', '2026-04-06', 4, '11:45:00', '19:00:00', 'Samolot', 15, 'images/Tokio.jpg', '2026-01-08 10:12:35'),
(5, 'Trekking w Himalajach - Annapurna Base Camp', 'Nepal', '16-dniowa, zaawansowana wyprawa trekkingowa do bazy pod Annapurną. Wymaga dobrej kondycji fizycznej. Cena zawiera przelot, permit na trekking, ubezpieczenie górskie, lokalnych przewodników i tragarzy.', 8900.00, '2026-10-10', '2026-10-25', 16, '06:00:00', '20:00:00', 'Samolot', 12, 'images/Nepal.jpg', '2026-01-08 10:12:35'),
(6, 'Road Trip Słońca - Kalifornia i Parki Narodowe', 'USA', '14 dni samochodem przez Kalifornię, Nevadę i Arizonę. Obejmuje Park Narodowy Yosemite, Wielki Kanion i Los Angeles. Idealna dla osób ceniących niezależność. Cena zawiera wynajem samochodu i ubezpieczenie.', 7500.00, '2026-08-01', '2026-08-14', 14, '07:30:00', '21:31:00', 'Samolot', 20, 'images/Kalifornia.jpeg', '2026-01-08 10:12:35');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$J6kNNflo.QhIEejdiHzlY.MLq7Zk.RxMO07q0r/QLnPSz9pwX6P4O', 'admin@admin.pollub.pl', 'admin', '2026-01-09 17:23:22'),
(2, 'Tomasz', '$2y$10$66a4k9yedSkrPPDhLR8dQOoHUlOP7qJnDLLo0Elrs3m7QG9C6s1Ti', 's101556@pollub.edu.pl', 'user', '2026-01-08 10:18:42');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indeksy dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`tour_id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indeksy dla tabeli `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
