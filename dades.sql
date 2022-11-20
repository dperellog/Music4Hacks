-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-11-2022 a las 11:35:16
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `blog`
--
--

USE blog;

-- Volcado de datos para la tabla `usuaris`
--

INSERT INTO `usuaris` (`id`, `nom`, `cognom`, `email`, `password`, `data`) VALUES
(1, 'David', 'Perello', 'dperellog@gmail.com', '$2y$04$7yHY1BKW/DEu1F8qTllWOO4z7/25ovgdXQdJyuqSLZ59BuBAIsX7q', '2022-11-20'),
(2, 'Pepet', 'Pepetet', 'ppepetet@lacetania.daw', '$2y$04$AtMP3JVoS.K.PFwE23LHd.2URzKZk65s2NyQd7g.2oGj3wlXFiJ76', '2022-11-20');
COMMIT;
--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `nombre`) VALUES
(1, 'Tutorials'),
(2, 'Covers'),
(3, 'Experiments'),
(4, 'Trucs');

--
-- Volcado de datos para la tabla `entrades`
--

INSERT INTO `entrades` (`id`, `usuari_id`, `categoria_id`, `titol`, `descripcio`, `data`) VALUES
(1, 1, 1, 'Primer Tutorial de Music4Hacks!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur aliquet, enim vitae hendrerit faucibus, mauris mi vulputate libero, eget condimentum neque lorem nec erat. Nunc nec molestie orci, sed aliquet mi. Nulla commodo nunc id mi vestibulum suscipit. Aenean dictum feugiat ligula a suscipit. Pellentesque in orci varius, ultricies tortor quis, bibendum nisi. Vestibulum non sollicitudin tellus. Etiam maximus maximus nisl. Cras euismod eros sollicitudin semper vestibulum.\r\n\r\nCurabitur mollis non eros et aliquet. Suspendisse pellentesque odio et cursus porta. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam sollicitudin dui vel risus condimentum imperdiet. Fusce tempor ex sed velit vulputate, a rhoncus mauris lobortis. Cras eget nibh mauris. Duis sit amet diam tristique, maximus risus ut, ornare nisl. Donec tristique, ligula dignissim elementum luctus, turpis lorem luctus sapien, at dictum massa sem vitae arcu. Cras ullamcorper metus metus, malesuada malesuada ante convallis sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque dictum felis eget libero ornare molestie. Duis vehicula dignissim orci, at auctor lacus consequat ut. Donec lacinia congue metus, eu egestas elit cursus at.', '2022-11-20'),
(2, 1, 1, 'Tutorial numero 2', 'In tempus ante sed scelerisque interdum. Sed eget nisi sit amet magna iaculis tempus. Etiam a sapien pretium, tristique tellus quis, pellentesque justo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In ipsum risus, convallis at massa ac, imperdiet imperdiet tortor. Etiam et ex turpis.', '2022-11-20'),
(3, 1, 2, 'Descobreix aquesta cover del senyor patata amb les seves hortalises!', 'Aliquam a tempus orci. Nullam lacinia vitae orci vitae commodo. Aenean imperdiet purus libero, eget sodales dui tincidunt at. Maecenas vel mollis diam, eget molestie orci.\r\n\r\nQuisque quis convallis arcu. Nulla venenatis nulla ut faucibus dapibus. Donec ultrices pretium elit, non tincidunt lectus ultricies ac. Fusce maximus lectus placerat sapien egestas sodales. Cras sed fermentum tellus. Donec tempor placerat nulla, in fringilla urna lobortis non. Donec pulvinar egestas risus eu elementum. Proin gravida, nibh at condimentum efficitur, purus velit eleifend quam, id venenatis erat arcu nec dui.', '2022-11-20'),
(4, 1, 3, 'Hola Mon! Benvinguts a la categoria d&#039;experiments, un lloc ple d&#039;idees ben bojes!', 'En tempus ante sed scelerisque interdum. Sed eget nisi sit amet magna iaculis tempus. Etiam a sapien pretium, tristique tellus quis, pellentesque justo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In ipsum risus, convallis at massa ac, imperdiet imperdiet tortor. Etiam et ex turpis. Curabitur dignissim bibendum ante sit amet pellentesque. Aliquam elementum elit sit amet eros facilisis bibendum.', '2022-11-20'),
(5, 2, 1, 'Tutorial d&#039;en Pepet', 'Curabitur mollis non eros et aliquet. Suspendisse pellentesque odio et cursus porta. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam sollicitudin dui vel risus condimentum imperdiet. Fusce tempor ex sed velit vulputate, a rhoncus mauris lobortis. Cras eget nibh mauris. Duis sit amet diam tristique, maximus risus ut, ornare nisl. Donec tristique, ligula dignissim elementum luctus, turpis lorem luctus sapien, at dictum massa sem vitae arcu. Cras ullamcorper metus metus, malesuada malesuada ante convallis sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque dictum felis eget libero ornare molestie. Duis vehicula dignissim orci, at auctor lacus consequat ut. Donec lacinia congue metus, eu egestas elit cursus at.', '2022-11-20'),
(6, 2, 2, 'Fantastica versio del nou grup tomaquets i alls: Reinventant el marrameu torracastanyes!', 'Nunc at pharetra ante. Curabitur eleifend pulvinar purus, eget lobortis odio interdum in. Integer placerat sem orci, sit amet pharetra leo vehicula id. Nullam semper, justo vel aliquam ornare, urna ipsum condimentum neque, id lacinia purus augue quis eros. Pellentesque tristique faucibus elit vel porttitor. Aenean sollicitudin tortor vitae dignissim eleifend. Sed varius sem ipsum, vehicula tempus sapien finibus nec. Nulla pulvinar elit in metus lacinia varius.\r\n\r\nAliquam a tempus orci. Nullam lacinia vitae orci vitae commodo. Aenean imperdiet purus libero, eget sodales dui tincidunt at. Maecenas vel mollis diam, eget molestie orci. Quisque quis convallis arcu. Nulla venenatis nulla ut faucibus dapibus. Donec ultrices pretium elit, non tincidunt lectus ultricies ac. Fusce maximus lectus placerat sapien egestas sodales. Cras sed fermentum tellus. Donec tempor placerat nulla, in fringilla urna lobortis non. Donec pulvinar egestas risus eu elementum. Proin gravida, nibh at condimentum efficitur, purus velit eleifend quam, id venenatis erat arcu nec dui. Etiam porta est non urna eleifend feugiat. Morbi aliquet sit amet lectus in fringilla. Donec quis nulla ullamcorper, condimentum augue quis, pulvinar purus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;', '2022-11-20');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
