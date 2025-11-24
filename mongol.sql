-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4
-- Время создания: Ноя 24 2025 г., 06:58
-- Версия сервера: 8.4.6
-- Версия PHP: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mongol`
--

-- --------------------------------------------------------

--
-- Структура таблицы `doc_type`
--

CREATE TABLE `doc_type` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `doc_type`
--

INSERT INTO `doc_type` (`id`, `title`) VALUES
(1, 'паспорт гражданина РФ'),
(2, 'заграничный\r\nпаспорт гражданина РФ'),
(3, 'паспорт гражданина Монголии'),
(4, 'другой документ');

-- --------------------------------------------------------

--
-- Структура таблицы `passport`
--

CREATE TABLE `passport` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `passport_type_id` int NOT NULL,
  `passport_expire` date NOT NULL,
  `passport_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `passport_another` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `passport`
--

INSERT INTO `passport` (`id`, `user_id`, `passport_type_id`, `passport_expire`, `passport_number`, `passport_another`) VALUES
(1, 1, 1, '2025-11-29', '111', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `stop_point`
--

CREATE TABLE `stop_point` (
  `id` int NOT NULL,
  `name` int NOT NULL,
  `ru` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `patronymic` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `auth_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `name`, `surname`, `patronymic`, `email`, `phone`, `password`, `auth_key`) VALUES
(1, 'q', 'q', 'q', 'q@q.q', '+11111111111', '$2y$13$onFgWYBRPHfE6diR5MwVbOWjE.2OfWI7Zf9eD9DP2xMRTNlRBVNFi', '1WaPAiiCymHeQOrZHpiKLaui3x7GAtIh');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `doc_type`
--
ALTER TABLE `doc_type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `passport`
--
ALTER TABLE `passport`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `stop_point`
--
ALTER TABLE `stop_point`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `doc_type`
--
ALTER TABLE `doc_type`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `passport`
--
ALTER TABLE `passport`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `stop_point`
--
ALTER TABLE `stop_point`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
