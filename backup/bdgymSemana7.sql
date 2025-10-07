-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-09-2025 a las 05:10:29
-- Versión del servidor: 8.0.43-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdgym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcertificado`
--

CREATE TABLE `tbcertificado` (
  `tbcertificadoid` int NOT NULL,
  `tbcertificadonombre` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbcertificadodescripcion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbcertificadoentidad` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorid` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbcertificado`
--

INSERT INTO `tbcertificado` (`tbcertificadoid`, `tbcertificadonombre`, `tbcertificadodescripcion`, `tbcertificadoentidad`, `tbinstructorid`) VALUES
(1, 'Entrenador Personal Certificado', 'Certificación en entrenamiento personal y funcional.', 'Federación Costarricense de Fitness', 2),
(3, 'Especialista en Nutrición Deportiva', 'Certificado en planes de nutrición para atletas.', 'Colegio de Nutricionistas de Costa Rica', 700303132),
(5, 'Entrenador Personal Certificado', 'Entrenamiento funcional', 'Federación Costarricense de Fitness', 702990544),
(8, 'N', 'n', 'N', 3),
(9, 'a', 'a', 'a', 3),
(10, 'o', 'o', 'o', 702990544),
(11, 's', 's', 's', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcliente`
--

CREATE TABLE `tbcliente` (
  `tbclienteid` int NOT NULL,
  `tbclienteimagenid` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbclientecarnet` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tbclientenombre` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `tbclientefechanacimiento` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `tbclientetelefono` int NOT NULL,
  `tbclientecorreo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbclientedireccion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tbclientegenero` char(1) COLLATE utf8mb4_general_ci NOT NULL,
  `tbclienteinscripcion` date NOT NULL,
  `tbclienteestado` tinyint(1) NOT NULL DEFAULT '1',
  `tbclientecontrasena` varchar(8) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbcliente`
--

INSERT INTO `tbcliente` (`tbclienteid`, `tbclienteimagenid`, `tbclientecarnet`, `tbclientenombre`, `tbclientefechanacimiento`, `tbclientetelefono`, `tbclientecorreo`, `tbclientedireccion`, `tbclientegenero`, `tbclienteinscripcion`, `tbclienteestado`, `tbclientecontrasena`) VALUES
(2, '5', '402220655', 'Mario corrales alpizar', '1999-08-08', 67803080, 'cliente@gmail.com', 'Heredia, centro', 'M', '2025-07-30', 1, '12345678'),
(8, NULL, '409880755', 'Cristian Carrillo', '1997-08-17', 87654532, 'cristian@gmail.com', 'Heredia, sarapiqui', 'M', '2025-08-05', 1, '12345678'),
(9, NULL, '406650369', 'Jefferson Carrillo', '1994-08-10', 84859630, 'jeffcar@gmail.com', 'San José', 'M', '2025-08-14', 1, '12345678');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcuerpozona`
--

CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int NOT NULL,
  `tbcuerpozonaimagenesids` text COLLATE utf8mb4_general_ci,
  `tbcuerpozonanombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbcuerpozona`
--

INSERT INTO `tbcuerpozona` (`tbcuerpozonaid`, `tbcuerpozonaimagenesids`, `tbcuerpozonanombre`, `tbcuerpozonadescripcion`, `tbcuerpozonaactivo`) VALUES
(2, NULL, 'Pecho', 'Ejercicios para el desarrollo de los músculos pectorales.', 0),
(3, '1$4', 'Espalda', 'Ejercicios para fortalecer la espalda, incluyendo dorsales y lumbares.', 1),
(4, '3', 'Piernas', 'Rutinas para cuádriceps, isquiotibiales y pantorrillas.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbdatoclinico`
--

CREATE TABLE `tbdatoclinico` (
  `tbdatoclinicoid` int NOT NULL,
  `tbclienteid` int DEFAULT NULL,
  `tbpadecimientoid` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbdatoclinico`
--

INSERT INTO `tbdatoclinico` (`tbdatoclinicoid`, `tbclienteid`, `tbpadecimientoid`) VALUES
(1, 2, '8$9'),
(2, 9, '5'),
(3, 8, '8');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbevento`
--

CREATE TABLE `tbevento` (
  `tbeventoid` int NOT NULL,
  `tbeventonombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tbeventodescripcion` text COLLATE utf8mb4_general_ci,
  `tbeventofecha` date NOT NULL,
  `tbeventohorainicio` time NOT NULL,
  `tbeventohorafin` time NOT NULL,
  `tbeventoaforo` int NOT NULL COMMENT 'Capacidad máxima de personas para el evento',
  `tbinstructorid` int DEFAULT NULL,
  `tbeventoestado` int NOT NULL DEFAULT '1' COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbevento`
--

INSERT INTO `tbevento` (`tbeventoid`, `tbeventonombre`, `tbeventodescripcion`, `tbeventofecha`, `tbeventohorainicio`, `tbeventohorafin`, `tbeventoaforo`, `tbinstructorid`, `tbeventoestado`) VALUES
(2, 'Zumba grupal', '', '2025-08-26', '14:00:00', '15:00:00', 2, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbhorario`
--

CREATE TABLE `tbhorario` (
  `tbhorarioid` int NOT NULL,
  `tbhorariodia` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `tbhorarioactivo` tinyint(1) NOT NULL DEFAULT '0',
  `tbhorarioapertura` time DEFAULT NULL,
  `tbhorariocierre` time DEFAULT NULL,
  `tbhorariobloqueos` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbhorario`
--

INSERT INTO `tbhorario` (`tbhorarioid`, `tbhorariodia`, `tbhorarioactivo`, `tbhorarioapertura`, `tbhorariocierre`, `tbhorariobloqueos`) VALUES
(1, 'Lunes', 1, '06:00:00', '21:00:00', '12:00&14:00$15:00&15:15'),
(2, 'Martes', 1, '06:00:00', '21:00:00', ''),
(3, 'Miércoles', 1, '06:00:00', '21:00:00', ''),
(4, 'Jueves', 1, '06:00:00', '21:00:00', ''),
(5, 'Viernes', 1, '06:00:00', '21:00:00', ''),
(6, 'Sábado', 1, '08:00:00', '16:00:00', ''),
(7, 'Domingo', 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbimagen`
--

CREATE TABLE `tbimagen` (
  `tbimagenid` int NOT NULL,
  `tbimagenruta` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tbimagenactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbimagen`
--

INSERT INTO `tbimagen` (`tbimagenid`, `tbimagenruta`, `tbimagenactivo`) VALUES
(1, '/img/cue00030001.jpg', 1),
(3, '/img/cue00040003.jpg', 1),
(4, '/img/cue00030004.jpg', 1),
(5, '/img/cli00020005.jpg', 1),
(6, '/img/ins00020006.jpg', 1),
(7, '/img/ins00010007.jpg', 1),
(8, '/img/ins00010008.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbinstructor`
--

CREATE TABLE `tbinstructor` (
  `tbinstructorid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tbinstructorimagenid` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructornombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructortelefono` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructordireccion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorcorreo` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorcuenta` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorcontraseña` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructoractivo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbinstructor`
--

INSERT INTO `tbinstructor` (`tbinstructorid`, `tbinstructorimagenid`, `tbinstructornombre`, `tbinstructortelefono`, `tbinstructordireccion`, `tbinstructorcorreo`, `tbinstructorcuenta`, `tbinstructorcontraseña`, `tbinstructoractivo`) VALUES
('1', '8', 'Deibis Gutierrez M', '60648399', 'San Francisco, Heredia', 'deibis.gutierrez@gym.com', 'CR08492845843945', '545', 0),
('2', '6', 'Andrea', '77777777', 'Cariari, caribe', 'instructor@gmail.com', '', '12345678', 1),
('3', NULL, 'c', '4444444444', 'd', 'dsads@gmail.com', '', '4444', 1),
('4', NULL, 'Anthony cubillo', '80290948', 'Cariari, caribe', 'tony@gmail.com', 'CR458294627', '12345', 1),
('5', NULL, 'Prueba', '6555474838', 'prueba', 'prueba@gmail.com', 'CR74858593', '9999', 1),
('6', NULL, 'n', '667765675', 'n', 'juan@gamil.com', '', '1111', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbnumeroemergencia`
--

CREATE TABLE `tbnumeroemergencia` (
  `tbnumeroemergenciaid` int NOT NULL,
  `tbnumeroemergenciaclienteid` int NOT NULL,
  `tbnumeroemergencianombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tbnumeroemergenciatelefono` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `tbnumeroemergenciarelacion` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbnumeroemergencia`
--

INSERT INTO `tbnumeroemergencia` (`tbnumeroemergenciaid`, `tbnumeroemergenciaclienteid`, `tbnumeroemergencianombre`, `tbnumeroemergenciatelefono`, `tbnumeroemergenciarelacion`) VALUES
(2, 2, 'Carlos Corrales', '85649560', 'Padre'),
(3, 2, 'Juana Araya', '85749547', 'Hermana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbpadecimiento`
--

CREATE TABLE `tbpadecimiento` (
  `tbpadecimientoid` int NOT NULL,
  `tbpadecimientotipo` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbpadecimientonombre` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbpadecimientodescripcion` text COLLATE utf8mb4_general_ci,
  `tbpadecimientoformadeactuar` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbpadecimiento`
--

INSERT INTO `tbpadecimiento` (`tbpadecimientoid`, `tbpadecimientotipo`, `tbpadecimientonombre`, `tbpadecimientodescripcion`, `tbpadecimientoformadeactuar`) VALUES
(5, 'Trastorno', 'Estrés post traumático', 'Problemas con la salud mental', 'Respiraciones para relajar'),
(8, 'Discapacidad', 'Parálisis de extremidades inferiores', 'No puede usar sus piernas', 'Realizar ejercicios de acuerdo a sus capacidades'),
(9, 'Enfermedad', 'Asma', 'Falta de aire en los pulmones', 'Utilizar la bomba de aire'),
(10, 'Enfermedad', 'Diabetes', 'Exceso de azucar en la sangre', 'Aplicar insulina'),
(12, 'Trastorno', 'Autismo', 'Afecta la forma de socializar y la comunicación', 'Tener paciencia y darle su espacio'),
(13, 'Lesión', 'Quebradura', 'Quebradura de una extremidad', 'No hacer ejercicios con la zona afectada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbreserva`
--

CREATE TABLE `tbreserva` (
  `tbreservaid` int NOT NULL,
  `tbclienteid` int NOT NULL,
  `tbeventoid` int DEFAULT NULL COMMENT 'Si es NULL, es una reserva de uso libre',
  `tbreservafecha` date NOT NULL,
  `tbreservahorainicio` time NOT NULL,
  `tbreservahorafin` time NOT NULL,
  `tbreservaestado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'activa' COMMENT 'Ej: activa, cancelada, completada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbreserva`
--

INSERT INTO `tbreserva` (`tbreservaid`, `tbclienteid`, `tbeventoid`, `tbreservafecha`, `tbreservahorainicio`, `tbreservahorafin`, `tbreservaestado`) VALUES
(3, 2, NULL, '2025-08-26', '14:00:00', '16:00:00', 'activa'),
(4, 2, 2, '2025-08-26', '14:00:00', '15:00:00', 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbsala`
--

CREATE TABLE `tbsala` (
  `tbsalaid` int NOT NULL,
  `tbsalanombre` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbsalacapacidad` int DEFAULT NULL,
  `tbsalaactivo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbsala`
--

INSERT INTO `tbsala` (`tbsalaid`, `tbsalanombre`, `tbsalacapacidad`, `tbsalaactivo`) VALUES
(2, 'Sala de Recepción', 14, 1),
(4, 'Sala de Yoga', 25, 1),
(5, 'Sala baile', 40, 0),
(7, 'Sala para cardio', 100, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbcertificado`
--
ALTER TABLE `tbcertificado`
  ADD PRIMARY KEY (`tbcertificadoid`);

--
-- Indices de la tabla `tbcliente`
--
ALTER TABLE `tbcliente`
  ADD PRIMARY KEY (`tbclienteid`);

--
-- Indices de la tabla `tbcuerpozona`
--
ALTER TABLE `tbcuerpozona`
  ADD PRIMARY KEY (`tbcuerpozonaid`);

--
-- Indices de la tabla `tbdatoclinico`
--
ALTER TABLE `tbdatoclinico`
  ADD PRIMARY KEY (`tbdatoclinicoid`),
  ADD KEY `fktbclientesid_idx` (`tbclienteid`);

--
-- Indices de la tabla `tbevento`
--
ALTER TABLE `tbevento`
  ADD PRIMARY KEY (`tbeventoid`),
  ADD KEY `tbinstructorid` (`tbinstructorid`);

--
-- Indices de la tabla `tbhorario`
--
ALTER TABLE `tbhorario`
  ADD PRIMARY KEY (`tbhorarioid`);

--
-- Indices de la tabla `tbimagen`
--
ALTER TABLE `tbimagen`
  ADD PRIMARY KEY (`tbimagenid`);

--
-- Indices de la tabla `tbinstructor`
--
ALTER TABLE `tbinstructor`
  ADD PRIMARY KEY (`tbinstructorid`);

--
-- Indices de la tabla `tbnumeroemergencia`
--
ALTER TABLE `tbnumeroemergencia`
  ADD PRIMARY KEY (`tbnumeroemergenciaid`);

--
-- Indices de la tabla `tbpadecimiento`
--
ALTER TABLE `tbpadecimiento`
  ADD PRIMARY KEY (`tbpadecimientoid`);

--
-- Indices de la tabla `tbreserva`
--
ALTER TABLE `tbreserva`
  ADD PRIMARY KEY (`tbreservaid`),
  ADD KEY `tbclienteid` (`tbclienteid`),
  ADD KEY `tbeventoid` (`tbeventoid`);

--
-- Indices de la tabla `tbsala`
--
ALTER TABLE `tbsala`
  ADD PRIMARY KEY (`tbsalaid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbcertificado`
--
ALTER TABLE `tbcertificado`
  MODIFY `tbcertificadoid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tbcliente`
--
ALTER TABLE `tbcliente`
  MODIFY `tbclienteid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tbdatoclinico`
--
ALTER TABLE `tbdatoclinico`
  MODIFY `tbdatoclinicoid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tbevento`
--
ALTER TABLE `tbevento`
  MODIFY `tbeventoid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbnumeroemergencia`
--
ALTER TABLE `tbnumeroemergencia`
  MODIFY `tbnumeroemergenciaid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbreserva`
--
ALTER TABLE `tbreserva`
  MODIFY `tbreservaid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tbsala`
--
ALTER TABLE `tbsala`
  MODIFY `tbsalaid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
