-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-08-2025 a las 03:24:43
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
-- Estructura de tabla para la tabla `tbclientes`
--

CREATE TABLE `tbclientes` (
  `tbclientesid` int NOT NULL,
  `tbclientescarnet` varchar(10) NOT NULL,
  `tbclientesnombre` varchar(45) NOT NULL,
  `tbclientesfechanacimiento` varchar(45) NOT NULL,
  `tbclientestelefono` int NOT NULL,
  `tbclientescorreo` varchar(100) DEFAULT NULL,
  `tbclientesdireccion` varchar(100) NOT NULL,
  `tbclientesgenero` char(1) NOT NULL,
  `tbclientesinscripcion` date NOT NULL,
  `tbclientesestado` tinyint(1) NOT NULL DEFAULT '1',
  `tbclientescontrasena` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbclientes`
--

INSERT INTO `tbclientes` (`tbclientesid`, `tbclientescarnet`, `tbclientesnombre`, `tbclientesfechanacimiento`, `tbclientestelefono`, `tbclientescorreo`, `tbclientesdireccion`, `tbclientesgenero`, `tbclientesinscripcion`, `tbclientesestado`, `tbclientescontrasena`) VALUES
(2, '402220655', 'Maria corrales alpizar', '1999-08-08', 67803080, 'cliente@gmail.com', 'heredia, centro', 'F', '2025-07-31', 0, '12345678'),
(7, '102220456', 'Jefferson Carrillo', '1999-08-10', 84859630, 'jeffecar@gmail.com', 'San José', 'M', '2025-08-01', 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbdatosclinicos`
--

CREATE TABLE `tbdatosclinicos` (
  `idtbdatosclinicos` int NOT NULL,
  `tbdatosclinicosenfermedad` varchar(100) DEFAULT NULL,
  `tbdatosclinicosotraenfermedad` varchar(260) DEFAULT NULL,
  `tbdatosclinicostomamedicamento` tinyint DEFAULT NULL,
  `tbdatosclinicosmedicamento` varchar(260) DEFAULT NULL,
  `tbdatosclinicoslesion` tinyint DEFAULT NULL,
  `tbdatosclinicosdescripcionlesion` text,
  `tbdatosclinicosdiscapacidad` tinyint DEFAULT NULL,
  `tbdatosclinicosdescripciondiscapacidad` text,
  `tbdatosclinicosrestriccionmedica` tinyint DEFAULT NULL,
  `tbdatosclinicosdescripcionrestriccionmedica` varchar(260) DEFAULT NULL,
  `tbclientesid` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbdatosclinicos`
--

INSERT INTO `tbdatosclinicos` (`idtbdatosclinicos`, `tbdatosclinicosenfermedad`, `tbdatosclinicosotraenfermedad`, `tbdatosclinicostomamedicamento`, `tbdatosclinicosmedicamento`, `tbdatosclinicoslesion`, `tbdatosclinicosdescripcionlesion`, `tbdatosclinicosdiscapacidad`, `tbdatosclinicosdescripciondiscapacidad`, `tbdatosclinicosrestriccionmedica`, `tbdatosclinicosdescripcionrestriccionmedica`, `tbclientesid`) VALUES
(1, '1', 'Artritis', 0, '', 0, '', 0, '', 1, 'No realizar movimientos bruscos', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbinstructor`
--

CREATE TABLE `tbinstructor` (
  `tbinstructorId` int NOT NULL,
  `tbinstructorNombre` varchar(100) DEFAULT NULL,
  `tbinstructorTelefono` varchar(100) DEFAULT NULL,
  `tbinstructorDireccion` varchar(500) DEFAULT NULL,
  `tbinstructorCorreo` varchar(500) DEFAULT NULL,
  `tbinstructorCuenta` varchar(500) DEFAULT NULL,
  `tbinstructorContraseña` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbinstructor`
--

INSERT INTO `tbinstructor` (`tbinstructorId`, `tbinstructorNombre`, `tbinstructorTelefono`, `tbinstructorDireccion`, `tbinstructorCorreo`, `tbinstructorCuenta`, `tbinstructorContraseña`) VALUES
(1, 'Deibis Gutierrez M', '60648399', 'Por ahi', 'fdsfd@gmail.com', 'CR08492845843945', NULL),
(2, 'Andrea', '77777777', 'Cariari, caribe', 'admin@gmail.com', NULL, '12345678');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbzonacuerpo`
--

CREATE TABLE `tbzonacuerpo` (
  `tbzonacuerpoid` int NOT NULL,
  `tbzonacuerponombre` varchar(100) NOT NULL,
  `tbzonacuerpodescripcion` varchar(500) NOT NULL,
  `tbzonacuerpoactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbzonacuerpo`
--

INSERT INTO `tbzonacuerpo` (`tbzonacuerpoid`, `tbzonacuerponombre`, `tbzonacuerpodescripcion`, `tbzonacuerpoactivo`) VALUES
(2, 'Pierna', 'No se partes de pierna', 0),
(3, 'Brazos', 'Triceps, biceps, etc', 1);

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
  MODIFY `tbclientesid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tbdatosclinicos`
--
ALTER TABLE `tbdatosclinicos`
  MODIFY `idtbdatosclinicos` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbinstructor`
--
ALTER TABLE `tbinstructor`
  MODIFY `tbinstructorId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
