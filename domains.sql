-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 09 2021 г., 01:56
-- Версия сервера: 5.7.25-log
-- Версия PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `domains`
--

-- --------------------------------------------------------

--
-- Структура таблицы `domains`
--

CREATE TABLE `domains` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `domains`
--

INSERT INTO `domains` (`id`, `name`) VALUES
(1, 'https://www.google.com');

-- --------------------------------------------------------

--
-- Структура таблицы `elements`
--

CREATE TABLE `elements` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `elements`
--

INSERT INTO `elements` (`id`, `name`) VALUES
(1, '<a>'),
(2, '<img>'),
(3, '<p>'),
(4, '<span>');

-- --------------------------------------------------------

--
-- Структура таблицы `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `total_time` float NOT NULL,
  `send_time` timestamp NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `request`
--

INSERT INTO `request` (`id`, `domain_id`, `url_id`, `element_id`, `total_time`, `send_time`, `total`) VALUES
(1, 1, 1, 2, 0.163215, '2021-06-08 07:29:09', 520),
(2, 1, 2, 2, 0.166689, '2021-06-08 14:41:27', 2),
(43, 1, 1, 2, 0.239404, '2021-06-08 16:21:40', 2),
(44, 1, 1, 1, 0.175635, '2021-06-08 16:25:09', 29),
(45, 1, 1, 1, 0.250807, '2021-06-08 16:30:14', 29),
(46, 1, 1, 1, 0.436932, '2021-06-08 16:40:58', 29),
(47, 1, 1, 2, 0.154091, '2021-06-08 16:41:04', 2),
(48, 1, 1, 2, 0.404913, '2021-06-08 21:10:21', 2),
(49, 1, 1, 2, 0.33484, '2021-06-08 21:26:50', 2),
(50, 1, 1, 2, 0.260953, '2021-06-08 21:32:43', 2),
(51, 1, 1, 2, 0.169054, '2021-06-08 21:40:19', 2),
(52, 1, 1, 2, 0.174619, '2021-06-08 21:45:42', 2),
(53, 1, 1, 2, 0.201405, '2021-06-08 21:53:23', 2),
(54, 1, 1, 2, 0.37904, '2021-06-08 22:24:42', 2),
(55, 1, 3, 2, 0.636502, '2021-06-08 22:25:07', 21),
(56, 1, 3, 2, 0.366723, '2021-06-08 22:34:53', 21),
(57, 1, 3, 1, 0.577182, '2021-06-08 22:35:01', 60),
(58, 1, 3, 3, 0.541844, '2021-06-08 22:35:07', 0),
(59, 1, 3, 4, 0.35806, '2021-06-08 22:35:19', 85),
(60, 1, 1, 2, 0.158658, '2021-06-08 22:48:30', 2),
(61, 1, 1, 2, 0.268389, '2021-06-08 22:53:45', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `url`
--

CREATE TABLE `url` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `url`
--

INSERT INTO `url` (`id`, `name`) VALUES
(1, 'https://www.google.com/'),
(2, 'https://www.google.com'),
(3, 'https://www.google.com/search?q=cat&client=opera-gx&hs=p9S&sxsrf=ALeKk03yBhnMARn4_hpfNOeD6G1giVq39w:1623191094203&source=lnms&tbm=isch&sa=X&ved=2ahUKEwii8cmEionxAhXxlYsKHU4VDhAQ_AUoAXoECAEQAw');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `elements`
--
ALTER TABLE `elements`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `url`
--
ALTER TABLE `url`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `elements`
--
ALTER TABLE `elements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT для таблицы `url`
--
ALTER TABLE `url`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
