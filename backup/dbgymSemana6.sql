-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 26-08-2025 a las 06:16:22
-- Versión del servidor: 8.0.42-0ubuntu0.24.04.2
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbgym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcertificado`
--

CREATE TABLE `tbcertificado` (
  `tbcertificadoid` int NOT NULL,
  `tbcertificadonombre` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbcertificadodescripcion` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
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
(7, 'v', 'v', 'v', 702990544);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcliente`
--

CREATE TABLE `tbcliente` (
  `tbclienteid` int NOT NULL,
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

INSERT INTO `tbcliente` (`tbclienteid`, `tbclientecarnet`, `tbclientenombre`, `tbclientefechanacimiento`, `tbclientetelefono`, `tbclientecorreo`, `tbclientedireccion`, `tbclientegenero`, `tbclienteinscripcion`, `tbclienteestado`, `tbclientecontrasena`) VALUES
(2, '402220655', 'Maria corrales alpizar', '1999-08-08', 67803080, 'cliente@gmail.com', 'heredia, centro', 'F', '2025-07-30', 0, '12345678'),
(8, '409880755', 'Cristian Carrillo', '1997-08-17', 87654532, 'cristian@gmail.com', 'Heredia, sarapiqui', 'M', '2025-08-05', 1, '12345678'),
(9, '406650369', 'Jefferson Carrillo', '1994-08-10', 84859630, 'jeffcar@gmail.com', 'San José', 'M', '2025-08-14', 1, '12345678');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcuerpozona`
--

CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int NOT NULL,
  `tbcuerpozonanombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbcuerpozona`
--

INSERT INTO `tbcuerpozona` (`tbcuerpozonaid`, `tbcuerpozonanombre`, `tbcuerpozonadescripcion`, `tbcuerpozonaactivo`) VALUES
(2, 'Pecho', 'Ejercicios para el desarrollo de los músculos pectorales.', 0),
(3, 'Espalda', 'Ejercicios para fortalecer la espalda, incluyendo dorsales y lumbares.', 1),
(4, 'Piernas', 'Rutinas para cuádriceps, isquiotibiales y pantorrillas.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbdatoclinico`
--

CREATE TABLE `tbdatoclinico` (
  `tbdatoclinicoid` int NOT NULL,
  `tbclienteid` int DEFAULT NULL,
  `tbpadecimientoid` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbdatoclinico`
--

INSERT INTO `tbdatoclinico` (`tbdatoclinicoid`, `tbclienteid`, `tbpadecimientoid`) VALUES
(2, 2, 3),
(3, 2, 2);

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
  `tbeventoaforo` int NOT NULL,
  `tbinstructorid` int DEFAULT NULL,
  `tbeventoestado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbevento`
--

INSERT INTO `tbevento` (`tbeventoid`,  `tbinstructorid`, `tbeventonombre`, `tbeventodescripcion`, `tbeventofecha`, `tbeventohorainicio`, `tbeventohorafin`, `tbeventoaforo`, `tbeventoactivo`) VALUES
(2, 'Zumba grupal', '', '2025-08-26', '14:00:00', '15:00:00', 2, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbinstructor`
--

CREATE TABLE `tbinstructor` (
  `tbinstructorid` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tbinstructornombre` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructortelefono` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructordireccion` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorcorreo` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorcuenta` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructorcontraseña` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tbinstructoractivo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbinstructor`
--

INSERT INTO `tbinstructor` (`tbinstructorid`, `tbinstructornombre`, `tbinstructortelefono`, `tbinstructordireccion`, `tbinstructorcorreo`, `tbinstructorcuenta`, `tbinstructorcontraseña`, `tbinstructoractivo`) VALUES
('1', 'Deibis Gutierrez M', '60648399', 'San Francisco, Heredia', 'deibis.gutierrez@gym.com', 'CR08492845843945', NULL, 0),
('2', 'Andrea', '77777777', 'Cariari, caribe', 'instructor@gmail.com', NULL, '12345678', 1),
('700303132', 'Carlos Rojas', '88888888', 'San Joaquín, Heredia', 'carlos.rojas@gym.com', 'CR231111111111111111', '12345678', 1),
('702990544', 'Noelia Fallas', '60825917', 'Cariari, caribe', 'noelita@gmail.com', '', '12345', 1),
('7849027849', 'Ana Montero', '673829374', 'Santo Domingo, Heredia', 'ana.montero@gym.com', '', '12345', 1);

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
(1, 2, 'Juan Araya', '72723244', 'Novio'),
(2, 2, 'Carlos Corrales', '85649560', 'Padre');

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
(2, 'Lesión', 'Torcedura', 'Me torcí el tobillo y me da mucho dolor', 'acetaminofen'),
(3, 'Discapacidad', 'Sordera', 'El cliente no puede escuchar', 'Enseñarles rutinas de forma visual'),
(4, 'Síndrome', 'Tourette', 'Tics nerviosos', 'Tratar de no exaltarlo mucho'),
(5, 'Trastorno', 'Estrés post traumático', 'Problemas con la salud mental', 'Respiraciones para relajar'),
(6, 'Enfermedad', 'Diabetes', 'Falta de insulina', 'Darle insulina'),
(7, 'Lesión', 'Quebradura', 'Quebradura de algún miembro', 'evitar recargar o hacer peso con la parte afectada'),
(8, 'Discapacidad', 'Parálisis de extremidades inferiores', 'No puede usar sus piernas', 'Realizar ejercicios de acuerdo a sus capacidades');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbreserva`
--

CREATE TABLE `tbreserva` (
  `tbreservaid` int NOT NULL,
  `tbclienteid` int NOT NULL,
  `tbeventoid` int DEFAULT NULL,
  `tbreservafecha` date NOT NULL,
  `tbreservahorainicio` time NOT NULL,
  `tbreservahorafin` time NOT NULL,
  `tbreservaestado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbreserva`
--

INSERT INTO `tbreserva` (`tbreservaid`, `tbclienteid`, `tbeventoid`, `tbreservafecha`, `tbreservahorainicio`, `tbreservahorafin`, `tbreservaestado`) VALUES
(3, 2, NULL, '2025-08-26', '14:00:00', '16:00:00', 'activa'),
(4, 2, 2, '2025-08-26', '14:00:00', '15:00:00', 'activa');

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbcertificado`
--
ALTER TABLE `tbcertificado`
  MODIFY `tbcertificadoid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tbcliente`
--
ALTER TABLE `tbcliente`
  MODIFY `tbclienteid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `tbnumeroemergenciaid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbreserva`
--
ALTER TABLE `tbreserva`
  MODIFY `tbreservaid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
