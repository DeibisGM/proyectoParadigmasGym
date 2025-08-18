-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-08-2025 a las 21:19:46
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
-- Estructura de tabla para la tabla `tbcliente`
--

CREATE TABLE `tbcliente` (
  `tbclienteid` int NOT NULL,
  `tbclientecarnet` varchar(10) NOT NULL,
  `tbclientenombre` varchar(45) NOT NULL,
  `tbclientefechanacimiento` varchar(45) NOT NULL,
  `tbclientetelefono` int NOT NULL,
  `tbclientecorreo` varchar(100) DEFAULT NULL,
  `tbclientedireccion` varchar(100) NOT NULL,
  `tbclientegenero` char(1) NOT NULL,
  `tbclienteinscripcion` date NOT NULL,
  `tbclienteestado` tinyint(1) NOT NULL DEFAULT '1',
  `tbclientecontrasena` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbcliente`
--

INSERT INTO `tbcliente` (`tbclienteid`, `tbclientecarnet`, `tbclientenombre`, `tbclientefechanacimiento`, `tbclientetelefono`, `tbclientecorreo`, `tbclientedireccion`, `tbclientegenero`, `tbclienteinscripcion`, `tbclienteestado`, `tbclientecontrasena`) VALUES
(2, '402220655', 'Maria corrales alpizar', '1999-08-08', 67803080, 'cliente@gmail.com', 'heredia, centro', 'F', '2025-07-30', 0, '12345678'),
(7, '102220456', 'Jefferson Carrillo', '1999-08-10', 84859630, 'jeffecar@gmail.com', 'San José', 'M', '2025-08-01', 1, ''),
(8, '409880755', 'Cristian Carrillo', '1997-08-17', 87654532, 'cristian@gmail.com', 'Heredia, sarapiqui', 'M', '2025-08-05', 1, '12345678');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbcuerpozona`
--

CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int NOT NULL,
  `tbcuerpozonanombre` varchar(100) NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbcuerpozona`
--

INSERT INTO `tbcuerpozona` (`tbcuerpozonaid`, `tbcuerpozonanombre`, `tbcuerpozonadescripcion`, `tbcuerpozonaactivo`) VALUES
(2, 'gdfgd', 'gdfg', 0),
(3, 'gdfg', 'gfgdf', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbdatoclinico`
--

CREATE TABLE `tbdatoclinico` (
  `tbdatoclinicoid` int NOT NULL,
  `tbclienteid` int DEFAULT NULL,
  `tbdatoclinicoenfermedad` varchar(100) DEFAULT NULL,
  `tbdatoclinicoenfermedaddescripcion` varchar(260) DEFAULT NULL,
  `tbdatoclinicomedicamento` tinyint DEFAULT NULL,
  `tbdatoclinicomedicamentodescripcion` varchar(260) DEFAULT NULL,
  `tbdatoclinicolesion` tinyint DEFAULT NULL,
  `tbdatoclinicolesiondescripcion` text,
  `tbdatoclinicodiscapacidad` tinyint DEFAULT NULL,
  `tbdatoclinicodiscapacidaddescripcion` text,
  `tbdatoclinicorestriccionmedica` tinyint DEFAULT NULL,
  `tbdatoclinicorestriccionmedicadescripcion` varchar(260) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbdatoclinico`
--

INSERT INTO `tbdatoclinico` (`tbdatoclinicoid`, `tbclienteid`, `tbdatoclinicoenfermedad`, `tbdatoclinicoenfermedaddescripcion`, `tbdatoclinicomedicamento`, `tbdatoclinicomedicamentodescripcion`, `tbdatoclinicolesion`, `tbdatoclinicolesiondescripcion`, `tbdatoclinicodiscapacidad`, `tbdatoclinicodiscapacidaddescripcion`, `tbdatoclinicorestriccionmedica`, `tbdatoclinicorestriccionmedicadescripcion`) VALUES
(1, 7, '1', 'Artritis 2', 0, '', 0, '', 0, '', 1, 'No realizar movimientos bruscos'),
(3, 8, '0', '', 0, '', 1, 'Tobillo', 0, '', 1, 'No realizar mucha fuerza'),
(4, 2, '0', '', 0, '', 0, '', 0, '', 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbinstructor`
--

CREATE TABLE `tbinstructor` (
  `tbinstructorid` int NOT NULL,
  `tbinstructornombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tbinstructortelefono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tbinstructordireccion` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tbinstructorcorreo` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tbinstructorcuenta` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tbinstructorcontraseña` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tbinstructoractivo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tbinstructor`
--

INSERT INTO `tbinstructor` (`tbinstructorid`, `tbinstructornombre`, `tbinstructortelefono`, `tbinstructordireccion`, `tbinstructorcorreo`, `tbinstructorcuenta`, `tbinstructorcontraseña`, `tbinstructoractivo`) VALUES
(1, 'Deibis Gutierrez M', '60648399', 'Por ahi', 'fdsfd@gmail.com', 'CR08492845843945', NULL, 1),
(2, 'Andrea', '77777777', 'Cariari, caribe', 'instructor@gmail.com', NULL, '12345678', 1);

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `tbinstructor`
--
ALTER TABLE `tbinstructor`
  ADD PRIMARY KEY (`tbinstructorid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbcliente`
--
ALTER TABLE `tbcliente`
  MODIFY `tbclienteid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tbdatoclinico`
--
ALTER TABLE `tbdatoclinico`
  MODIFY `tbdatoclinicoid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbinstructor`
--
ALTER TABLE `tbinstructor`
  MODIFY `tbinstructorid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
