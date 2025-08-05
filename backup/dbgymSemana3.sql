-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-08-2025 a las 00:30:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
-- Estructura de tabla para la tabla `tbclientes`
--

CREATE TABLE `tbclientes` (
  `tbclientesid` int(11) NOT NULL,
  `tbclientescarnet` varchar(10) NOT NULL,
  `tbclientesnombre` varchar(45) NOT NULL,
  `tbclientesfechanacimiento` varchar(45) NOT NULL,
  `tbclientestelefono` int(11) NOT NULL,
  `tbclientescorreo` varchar(100) DEFAULT NULL,
  `tbclientesdireccion` varchar(100) NOT NULL,
  `tbclientesgenero` char(1) NOT NULL,
  `tbclientesinscripcion` date NOT NULL,
  `tbclientesestado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbclientes`
--

INSERT INTO `tbclientes` (`tbclientesid`, `tbclientescarnet`, `tbclientesnombre`, `tbclientesfechanacimiento`, `tbclientestelefono`, `tbclientescorreo`, `tbclientesdireccion`, `tbclientesgenero`, `tbclientesinscripcion`, `tbclientesestado`) VALUES
(2, '402220655', 'Maria corrales alpizar', '1999-08-08', 67803080, 'maria@gmail.com', 'heredia, centro', 'F', '2025-08-01', 0),
(7, '102220456', 'Jefferson Carrillo', '1999-08-10', 84859630, 'jeffecar@gmail.com', 'San José', 'M', '2025-08-01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbdatosclinicos`
--

CREATE TABLE `tbdatosclinicos` (
  `idtbdatosclinicos` int(11) NOT NULL,
  `tbdatosclinicosenfermedad` varchar(100) DEFAULT NULL,
  `tbdatosclinicosotraenfermedad` varchar(260) DEFAULT NULL,
  `tbdatosclinicostomamedicamento` tinyint(4) DEFAULT NULL,
  `tbdatosclinicosmedicamento` varchar(260) DEFAULT NULL,
  `tbdatosclinicoslesion` tinyint(4) DEFAULT NULL,
  `tbdatosclinicosdescripcionlesion` text DEFAULT NULL,
  `tbdatosclinicosdiscapacidad` tinyint(4) DEFAULT NULL,
  `tbdatosclinicosdescripciondiscapacidad` text DEFAULT NULL,
  `tbdatosclinicosrestriccionmedica` tinyint(4) DEFAULT NULL,
  `tbclientesid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbdatosclinicos`
--

INSERT INTO `tbdatosclinicos` (`idtbdatosclinicos`, `tbdatosclinicosenfermedad`, `tbdatosclinicosotraenfermedad`, `tbdatosclinicostomamedicamento`, `tbdatosclinicosmedicamento`, `tbdatosclinicoslesion`, `tbdatosclinicosdescripcionlesion`, `tbdatosclinicosdiscapacidad`, `tbdatosclinicosdescripciondiscapacidad`, `tbdatosclinicosrestriccionmedica`, `tbclientesid`) VALUES
(1, '1', 'Asma', 1, 'Bomba de aire', 0, '', 0, '', 0, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbinstructor`
--

CREATE TABLE `tbinstructor` (
  `tbinstructorId` int(11) NOT NULL,
  `tbinstructorNombre` varchar(100) DEFAULT NULL,
  `tbinstructorTelefono` varchar(100) DEFAULT NULL,
  `tbinstructorDireccion` varchar(500) DEFAULT NULL,
  `tbinstructorCorreo` varchar(500) DEFAULT NULL,
  `tbinstructorCuenta` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbinstructor`
--

INSERT INTO `tbinstructor` (`tbinstructorId`, `tbinstructorNombre`, `tbinstructorTelefono`, `tbinstructorDireccion`, `tbinstructorCorreo`, `tbinstructorCuenta`) VALUES
(1, 'Deibis Gutierrez', '60648399', 'Por ahi', 'fdsfd@gmail.com', 'no tengo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbzonacuerpo`
--

CREATE TABLE `tbzonacuerpo` (
  `tbzonacuerpoid` int(11) NOT NULL,
  `tbzonacuerponombre` varchar(100) NOT NULL,
  `tbzonacuerpodescripcion` varchar(500) NOT NULL,
  `tbzonacuerpoactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbzonacuerpo`
--

INSERT INTO `tbzonacuerpo` (`tbzonacuerpoid`, `tbzonacuerponombre`, `tbzonacuerpodescripcion`, `tbzonacuerpoactivo`) VALUES
(3, 'fdfsd', 'pruena', 1),
(4, 'fsdfd', 'fdsfd', 1),
(5, 'Pecho', 'pectoral', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbclientes`
--
ALTER TABLE `tbclientes`
  ADD PRIMARY KEY (`tbclientesid`);

--
-- Indices de la tabla `tbdatosclinicos`
--
ALTER TABLE `tbdatosclinicos`
  ADD PRIMARY KEY (`idtbdatosclinicos`),
  ADD KEY `fktbclientesid_idx` (`tbclientesid`);

--
-- Indices de la tabla `tbinstructor`
--
ALTER TABLE `tbinstructor`
  ADD PRIMARY KEY (`tbinstructorId`);

--
-- Indices de la tabla `tbzonacuerpo`
--
ALTER TABLE `tbzonacuerpo`
  ADD PRIMARY KEY (`tbzonacuerpoid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbclientes`
--
ALTER TABLE `tbclientes`
  MODIFY `tbclientesid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tbdatosclinicos`
--
ALTER TABLE `tbdatosclinicos`
  MODIFY `idtbdatosclinicos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbinstructor`
--
ALTER TABLE `tbinstructor`
  MODIFY `tbinstructorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbdatosclinicos`
--
ALTER TABLE `tbdatosclinicos`
  ADD CONSTRAINT `fktbclientesid` FOREIGN KEY (`tbclientesid`) REFERENCES `tbclientes` (`tbclientesid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
