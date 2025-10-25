-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 05:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bdgym`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbcertificado`
--

CREATE TABLE `tbcertificado` (
  `tbcertificadoid` int(11) NOT NULL,
  `tbinstructorid` int(11) DEFAULT NULL,
  `tbcertificadoimagenid` varchar(250) DEFAULT NULL,
  `tbcertificadonombre` varchar(100) DEFAULT NULL,
  `tbcertificadodescripcion` varchar(500) DEFAULT NULL,
  `tbcertificadoentidad` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbcertificado`
--

INSERT INTO `tbcertificado` (`tbcertificadoid`, `tbinstructorid`, `tbcertificadoimagenid`, `tbcertificadonombre`, `tbcertificadodescripcion`, `tbcertificadoentidad`) VALUES
(1, 2, '26', 'Entrenador Personal Certificado', 'Certificación en entrenamiento personal y funcional.', 'Federación Costarricense de Fitness'),
(3, 1, '', 'Especialista en Nutrición Deportiva', 'Certificado en planes de nutrición para atletas.', 'Colegio de Nutricionistas de Costa Rica'),
(5, 1, '', 'Entrenador Personal Certificado', 'Entrenamiento funcional', 'Federación Costarricense de Fitness'),
(15, 3, '', 'n', 'n', 'n');

-- --------------------------------------------------------

--
-- Table structure for table `tbcliente`
--

CREATE TABLE `tbcliente` (
  `tbclienteid` int(11) NOT NULL,
  `tbclienteimagenid` varchar(45) DEFAULT NULL,
  `tbclientecarnet` varchar(10) NOT NULL,
  `tbclientenombre` varchar(45) NOT NULL,
  `tbclientefechanacimiento` varchar(45) NOT NULL,
  `tbclientetelefono` int(11) NOT NULL,
  `tbclientecorreo` varchar(100) DEFAULT NULL,
  `tbclientedireccion` varchar(100) NOT NULL,
  `tbclientegenero` char(1) NOT NULL,
  `tbclienteinscripcion` date NOT NULL,
  `tbclientecontrasena` varchar(8) NOT NULL,
  `tbclienteactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbcliente`
--

INSERT INTO `tbcliente` (`tbclienteid`, `tbclienteimagenid`, `tbclientecarnet`, `tbclientenombre`, `tbclientefechanacimiento`, `tbclientetelefono`, `tbclientecorreo`, `tbclientedireccion`, `tbclientegenero`, `tbclienteinscripcion`, `tbclientecontrasena`, `tbclienteactivo`) VALUES
(2, '5', '402220655', 'Maria corrales alpizar', '1999-08-08', 67803080, 'cliente@gmail.com', 'Heredia, centro', 'M', '2025-09-01', '12345678', 1),
(8, NULL, '409880755', 'Cristian Carrillo', '1997-08-17', 87654532, 'cristian@gmail.com', 'Heredia, sarapiqui', 'M', '2025-08-05', '12345678', 1),
(9, NULL, '406650369', 'Jefferson Carrillo', '1994-08-10', 84859630, 'jeffcar@gmail.com', 'San José', 'M', '2025-08-14', '12345678', 1),
(15, NULL, '501020201', 'Casandra Lascurein', '2000-10-02', 84582632, 'cassandra@gmail.com', 'heredia, centro', 'F', '2025-09-01', '12345678', 1),
(20, NULL, '785256398', 'Mario Gonzáles Corrales', '2001-10-07', 69656368, 'ma.example@gmail.com', 'San jose centro', 'M', '2025-10-07', '12345678', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbclientepadecimiento`
--

CREATE TABLE `tbclientepadecimiento` (
  `tbclientepadecimientoid` int(11) NOT NULL,
  `tbclienteid` int(11) DEFAULT NULL,
  `tbpadecimientoid` text DEFAULT NULL,
  `tbpadecimientodictamenid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbclientepadecimiento`
--

INSERT INTO `tbclientepadecimiento` (`tbclientepadecimientoid`, `tbclienteid`, `tbpadecimientoid`, `tbpadecimientodictamenid`) VALUES
(1, 2, '9', NULL),
(2, 9, '14$10', NULL),
(6, 8, NULL, 58),
(7, 15, '14', 57);

-- --------------------------------------------------------

--
-- Table structure for table `tbcuerpozona`
--

CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int(11) NOT NULL,
  `tbcuerpozonapartezonaid` text DEFAULT NULL,
  `tbcuerpozonaimagenesids` text DEFAULT NULL,
  `tbcuerpozonanombre` varchar(100) NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbcuerpozona`
--

INSERT INTO `tbcuerpozona` (`tbcuerpozonaid`, `tbcuerpozonapartezonaid`, `tbcuerpozonaimagenesids`, `tbcuerpozonanombre`, `tbcuerpozonadescripcion`, `tbcuerpozonaactivo`) VALUES
(2, '1', NULL, 'Pecho', 'Ejercicios para el desarrollo de los músculos pectorales.', 0),
(3, NULL, '1$4', 'Espalda', 'Ejercicios para fortalecer la espalda, incluyendo dorsales y lumbares.', 1),
(4, NULL, '3', 'Piernas', 'Rutinas para cuádriceps, isquiotibiales y pantorrillas.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbejerciciofuerza`
--

CREATE TABLE `tbejerciciofuerza` (
  `tbejerciciofuerzaid` int(11) NOT NULL,
  `tbejerciciofuerzanombre` varchar(70) DEFAULT NULL,
  `tbejerciciofuerzadescripcion` varchar(250) DEFAULT NULL,
  `tbejerciciofuerzarepeticion` int(11) DEFAULT NULL,
  `tbejerciciofuerzaserie` int(11) DEFAULT NULL,
  `tbejerciciofuerzapeso` double DEFAULT NULL,
  `tbejerciciofuerzadescanso` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbejerciciofuerza`
--

INSERT INTO `tbejerciciofuerza` (`tbejerciciofuerzaid`, `tbejerciciofuerzanombre`, `tbejerciciofuerzadescripcion`, `tbejerciciofuerzarepeticion`, `tbejerciciofuerzaserie`, `tbejerciciofuerzapeso`, `tbejerciciofuerzadescanso`) VALUES
(2, 'Jalón pecho', 'Es como jalar cuerda al pecho', 12, 4, 75, 20),
(4, 'Plancha', 'Mantener el cuerpo recto apoyado en antebrazos y punta de los pies', 2, 1, 0, 30),
(5, 'Peso muerto', 'Levantantar pesas desde el suelo hasta la altura de la cadera con la espalda recta', 10, 3, 60, 50);

-- --------------------------------------------------------

--
-- Table structure for table `tbevento`
--

CREATE TABLE `tbevento` (
  `tbeventoid` int(11) NOT NULL,
  `tbinstructorid` int(11) NOT NULL,
  `tbeventotipo` varchar(20) NOT NULL DEFAULT 'abierto' COMMENT 'Tipos: abierto, privado',
  `tbeventonombre` varchar(255) NOT NULL,
  `tbeventodescripcion` text DEFAULT NULL,
  `tbeventofecha` date NOT NULL,
  `tbeventohorainicio` time NOT NULL,
  `tbeventohorafin` time NOT NULL,
  `tbeventoaforo` int(11) NOT NULL COMMENT 'Capacidad máxima de personas para el evento',
  `tbeventoactivo` int(11) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbevento`
--

INSERT INTO `tbevento` (`tbeventoid`, `tbinstructorid`, `tbeventotipo`, `tbeventonombre`, `tbeventodescripcion`, `tbeventofecha`, `tbeventohorainicio`, `tbeventohorafin`, `tbeventoaforo`, `tbeventoactivo`) VALUES
(1, 2, 'abierto', 'Zumba Grupal', 'fdsf', '2025-10-07', '08:00:00', '09:00:00', 15, 1),
(3, 1, 'privado', 'De prueba', '', '2025-10-07', '06:00:00', '10:00:00', 15, 1),
(4, 5, 'abierto', 'Yoga acustica', '', '2025-10-07', '10:00:00', '16:00:00', 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbhorario`
--

CREATE TABLE `tbhorario` (
  `tbhorarioid` int(11) NOT NULL,
  `tbhorariodia` varchar(25) NOT NULL,
  `tbhorarioactivo` tinyint(1) NOT NULL DEFAULT 0,
  `tbhorarioapertura` time DEFAULT NULL,
  `tbhorariocierre` time DEFAULT NULL,
  `tbhorariobloqueo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbhorario`
--

INSERT INTO `tbhorario` (`tbhorarioid`, `tbhorariodia`, `tbhorarioactivo`, `tbhorarioapertura`, `tbhorariocierre`, `tbhorariobloqueo`) VALUES
(1, 'Lunes', 1, '05:00:00', '21:00:00', '12:00:00&14:00:00'),
(2, 'Martes', 1, '05:00:00', '21:00:00', '12:00:00&14:00:00'),
(3, 'Miércoles', 1, '05:00:00', '21:00:00', '12:00:00&14:00:00'),
(4, 'Jueves', 1, '05:00:00', '21:00:00', '12:00:00&14:00:00'),
(5, 'Viernes', 1, '05:00:00', '21:00:00', '12:00:00&14:00:00'),
(6, 'Sábado', 1, '07:00:00', '13:00:00', ''),
(7, 'Domingo', 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbhorariolibre`
--

CREATE TABLE `tbhorariolibre` (
  `tbhorariolibreid` int(11) NOT NULL,
  `tbhorariolibrefecha` date NOT NULL,
  `tbhorariolibrehora` time NOT NULL,
  `tbhorariolibresalaid` int(11) NOT NULL,
  `tbhorariolibreinstructorid` varchar(100) NOT NULL,
  `tbhorariolibrecupos` int(11) NOT NULL,
  `tbhorariolibrematriculados` int(11) NOT NULL DEFAULT 0,
  `tbhorariolibreactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbhorariolibre`
--

INSERT INTO `tbhorariolibre` (`tbhorariolibreid`, `tbhorariolibrefecha`, `tbhorariolibrehora`, `tbhorariolibresalaid`, `tbhorariolibreinstructorid`, `tbhorariolibrecupos`, `tbhorariolibrematriculados`, `tbhorariolibreactivo`) VALUES
(1, '2025-09-25', '07:00:00', 1, '2', 50, 0, 1),
(3, '2025-09-24', '10:00:00', 1, '2', 50, 1, 1),
(4, '2025-09-23', '07:00:00', 1, '4', 25, 1, 1),
(5, '2025-09-24', '08:00:00', 1, '4', 25, 0, 1),
(6, '2025-10-10', '06:00:00', 1, '1', 25, 1, 1),
(7, '2025-10-08', '09:00:00', 1, '1', 25, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbhorariopersonal`
--

CREATE TABLE `tbhorariopersonal` (
  `tbhorariopersonalid` int(11) NOT NULL,
  `tbhorariopersonalfecha` date DEFAULT NULL,
  `tbhorariopersonalhora` time DEFAULT NULL,
  `tbinstructorid` int(11) DEFAULT NULL,
  `tbclienteid` int(11) DEFAULT NULL,
  `tbhorariopersonalestado` varchar(20) DEFAULT NULL,
  `tbhorariopersonalduracion` int(11) DEFAULT NULL,
  `tbhorariopersonaltipo` varchar(20) DEFAULT NULL,
  `tbhorariopersonalfechacreacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbhorariopersonal`
--

INSERT INTO `tbhorariopersonal` (`tbhorariopersonalid`, `tbhorariopersonalfecha`, `tbhorariopersonalhora`, `tbinstructorid`, `tbclienteid`, `tbhorariopersonalestado`, `tbhorariopersonalduracion`, `tbhorariopersonaltipo`, `tbhorariopersonalfechacreacion`) VALUES
(1, '2025-10-08', '09:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(2, '2025-10-08', '12:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(3, '2025-10-08', '09:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(4, '2025-10-08', '12:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(5, '2025-10-10', '08:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(6, '2025-10-10', '11:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(7, '2025-10-10', '08:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(8, '2025-10-10', '11:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(9, '2025-10-10', '08:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(10, '2025-10-10', '08:00:00', 5, NULL, 'disponible', 60, 'personal', NULL),
(11, '2025-10-10', '08:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(12, '2025-10-11', '08:00:00', 2, NULL, 'disponible', 60, 'personal', NULL),
(13, '2025-10-11', '09:00:00', 2, NULL, 'disponible', 60, 'personal', NULL),
(14, '2025-10-12', '08:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(15, '2025-10-12', '10:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(16, '2025-10-10', '09:00:00', 5, NULL, 'disponible', 60, 'personal', NULL),
(17, '2025-10-12', '09:00:00', 5, NULL, 'disponible', 60, 'personal', NULL),
(18, '2025-10-10', '10:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(19, '2025-10-11', '10:00:00', 2, NULL, 'disponible', 60, 'personal', NULL),
(20, '2025-10-12', '09:00:00', 5, NULL, 'disponible', 60, 'personal', NULL),
(21, '2025-10-12', '09:00:00', 5, NULL, 'disponible', 2, 'personal', NULL),
(22, '2025-10-15', '06:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(23, '2025-10-15', '06:30:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(24, '2025-10-14', '08:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(25, '2025-10-14', '16:00:00', 1, NULL, 'disponible', 60, 'personal', NULL),
(26, '2025-10-14', '15:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(27, '2025-10-14', '16:00:00', 4, NULL, 'disponible', 60, 'personal', NULL),
(28, '2025-10-15', '08:00:00', 5, 2, 'reservado', 60, 'personal', NULL),
(29, '2025-10-13', '18:00:00', 1, 2, 'reservado', 60, 'personal', NULL),
(30, '2025-10-13', '08:00:00', 3, 2, 'reservado', 60, 'personal', NULL),
(31, '2025-10-20', '08:00:00', 2, 2, 'reservado', 60, 'personal', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbimagen`
--

CREATE TABLE `tbimagen` (
  `tbimagenid` int(11) NOT NULL,
  `tbimagenruta` varchar(255) NOT NULL,
  `tbimagenactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbimagen`
--

INSERT INTO `tbimagen` (`tbimagenid`, `tbimagenruta`, `tbimagenactivo`) VALUES
(1, '/img/cue00030001.jpg', 1),
(3, '/img/cue00040003.jpg', 1),
(4, '/img/cue00030004.jpg', 1),
(5, '/img/cli00020005.jpg', 1),
(6, '/img/ins00020006.jpg', 1),
(7, '/img/ins00010007.jpg', 1),
(10, '/img/ins00060010.jpg', 1),
(11, '/img/ins00050011.jpg', 1),
(13, '/img/cert00130013.jpg', 1),
(14, '/img/cert00140014.jpg', 1),
(15, '/img/cert00030015.jpg', 1),
(16, '/img/cert00030016.jpg', 1),
(17, '/img/cert00050017.jpg', 1),
(18, '/img/cert00050018.jpg', 1),
(19, '/img/cert00150019.jpg', 1),
(20, '/img/cert00050020.jpg', 1),
(21, '/img/pad00550021.jpg', 1),
(22, '/img/pad00570022.jpg', 1),
(23, '/img/pad00580023.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbinstructor`
--

CREATE TABLE `tbinstructor` (
  `tbinstructorid` varchar(100) NOT NULL,
  `tbinstructorimagenid` varchar(250) DEFAULT NULL,
  `tbinstructornombre` varchar(100) DEFAULT NULL,
  `tbinstructortelefono` varchar(100) DEFAULT NULL,
  `tbinstructordireccion` varchar(500) DEFAULT NULL,
  `tbinstructorcorreo` varchar(500) DEFAULT NULL,
  `tbinstructorcuenta` varchar(500) DEFAULT NULL,
  `tbinstructorcontraseña` varchar(100) DEFAULT NULL,
  `tbinstructorcertificado` varchar(100) DEFAULT NULL,
  `tbinstructoractivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbinstructor`
--

INSERT INTO `tbinstructor` (`tbinstructorid`, `tbinstructorimagenid`, `tbinstructornombre`, `tbinstructortelefono`, `tbinstructordireccion`, `tbinstructorcorreo`, `tbinstructorcuenta`, `tbinstructorcontraseña`, `tbinstructorcertificado`, `tbinstructoractivo`) VALUES
('005', '11', 'Noelia Fallas', '60728094', 'Cariari, caribe', 'Noelia@gmail.com', 'Cr45272883', '78834', '', 1),
('1', '', 'Deibis Gutierrez M', '60648399', 'San Francisco, Heredia', 'deibis.gutierrez@gym.com', 'CR08492845843945', '5454', '', 1),
('2', '6', 'Andrea', '77777777', 'Cariari, caribe', 'instructor@gmail.com', '', '12345678', NULL, 1),
('3', '', 'c', '4444444444', 'd', 'dsads@gmail.com', '', '4444', '', 1),
('4', NULL, 'Anthony cubillo', '80290948', 'Cariari, caribe', 'tony@gmail.com', 'CR458294627', '12345', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbinstructorhorario`
--

CREATE TABLE `tbinstructorhorario` (
  `tbinstructorhorarioid` varchar(20) NOT NULL,
  `tbinstructorid` varchar(10) DEFAULT NULL,
  `tbinstructorhorariodia` varchar(10) DEFAULT NULL,
  `tbinstructorhorariohorainicio` time DEFAULT NULL,
  `tbinstructorhorariohorafin` time DEFAULT NULL,
  `tbinstructorhorarioactivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbinstructorhorario`
--

INSERT INTO `tbinstructorhorario` (`tbinstructorhorarioid`, `tbinstructorid`, `tbinstructorhorariodia`, `tbinstructorhorariohorainicio`, `tbinstructorhorariohorafin`, `tbinstructorhorarioactivo`) VALUES
('IH001', '1', 'Lunes', '07:30:00', '13:00:00', 1),
('IH002', '4', 'Lunes', '13:00:00', '21:00:00', 1),
('IH003', '2', 'Viernes', '13:00:00', '20:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbnumeroemergencia`
--

CREATE TABLE `tbnumeroemergencia` (
  `tbnumeroemergenciaid` int(11) NOT NULL,
  `tbnumeroemergenciaclienteid` int(11) NOT NULL,
  `tbnumeroemergencianombre` varchar(50) NOT NULL,
  `tbnumeroemergenciatelefono` varchar(8) NOT NULL,
  `tbnumeroemergenciarelacion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbnumeroemergencia`
--

INSERT INTO `tbnumeroemergencia` (`tbnumeroemergenciaid`, `tbnumeroemergenciaclienteid`, `tbnumeroemergencianombre`, `tbnumeroemergenciatelefono`, `tbnumeroemergenciarelacion`) VALUES
(2, 2, 'Carlos Corrales A', '78745845', 'Padre'),
(3, 2, 'Juana Araya', '85749547', 'Hermana'),
(4, 15, 'Maria Lascurein', '87256984', 'Hermana'),
(5, 8, 'Karla Carrillo', '87526941', 'Hermana');

-- --------------------------------------------------------

--
-- Table structure for table `tbpadecimiento`
--

CREATE TABLE `tbpadecimiento` (
  `tbpadecimientoid` int(11) NOT NULL,
  `tbpadecimientotipo` varchar(200) DEFAULT NULL,
  `tbpadecimientonombre` varchar(200) DEFAULT NULL,
  `tbpadecimientodescripcion` text DEFAULT NULL,
  `tbpadecimientoformadeactuar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbpadecimiento`
--

INSERT INTO `tbpadecimiento` (`tbpadecimientoid`, `tbpadecimientotipo`, `tbpadecimientonombre`, `tbpadecimientodescripcion`, `tbpadecimientoformadeactuar`) VALUES
(5, 'Trastorno', 'Estrés post traumático', 'Problemas con la salud mental', 'Respiraciones para relajar'),
(8, 'Discapacidad', 'Parálisis de extremidades inferiores', 'No puede usar sus piernas', 'Realizar ejercicios de acuerdo a sus capacidades'),
(9, 'Enfermedad', 'Asma', 'Falta de aire en los pulmones', 'Utilizar la bomba de aire'),
(10, 'Enfermedad', 'Diabetes', 'Exceso de azucar en la sangre', 'Aplicar insulina'),
(12, 'Trastorno', 'Autismo', 'Afecta la forma de socializar y la comunicación', 'Tener paciencia y darle su espacio'),
(13, 'Lesión', 'Quebradura', 'Quebradura de una extremidad', 'No hacer ejercicios con la zona afectada'),
(14, 'Enfermedad', 'Artritis', 'Dolor en las articulaciones', 'Tomando medicamento');

-- --------------------------------------------------------

--
-- Table structure for table `tbpadecimientodictamen`
--

CREATE TABLE `tbpadecimientodictamen` (
  `tbpadecimientodictamenid` int(11) NOT NULL,
  `tbpadecimientodictamenfechaemision` date DEFAULT NULL,
  `tbpadecimientodictamenentidademision` varchar(100) DEFAULT NULL,
  `tbpadecimientodictamenimagenid` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbpadecimientodictamen`
--

INSERT INTO `tbpadecimientodictamen` (`tbpadecimientodictamenid`, `tbpadecimientodictamenfechaemision`, `tbpadecimientodictamenentidademision`, `tbpadecimientodictamenimagenid`) VALUES
(57, '2025-09-22', 'Clinica Puerto Viejo', '22'),
(58, '2025-09-30', 'Clinica San Juan de Dios', '23');

-- --------------------------------------------------------

--
-- Table structure for table `tbpartezona`
--

CREATE TABLE `tbpartezona` (
  `tbpartezonaid` int(11) NOT NULL,
  `tbpartezonaimagenid` varchar(100) DEFAULT NULL,
  `tbpartezonanombre` varchar(50) NOT NULL,
  `tbpartezonadescripcion` varchar(100) DEFAULT NULL,
  `tbpartezonaactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbpartezona`
--

INSERT INTO `tbpartezona` (`tbpartezonaid`, `tbpartezonaimagenid`, `tbpartezonanombre`, `tbpartezonadescripcion`, `tbpartezonaactivo`) VALUES
(1, '', 'Pectoral', 'Parte frontal del pecho', 1),
(2, '', 'Superior izquierdo', 'Parte de arriba', 1),
(3, '', 'Baja', 'pechoss', 1),
(4, '', 'Medio', 'Parte del abdomen', 1),
(6, '', 'Pantorrilla', '', 1),
(7, '', 'Pierna', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbreservaevento`
--

CREATE TABLE `tbreservaevento` (
  `tbreservaeventoid` int(11) NOT NULL,
  `tbreservaeventoclienteid` int(11) NOT NULL,
  `tbreservaeventoeventoid` int(11) NOT NULL,
  `tbreservaeventoclienteresponsableid` int(11) DEFAULT NULL COMMENT 'ID del cliente que realiza la reserva para el grupo o para sí mismo.',
  `tbreservaeventofecha` date NOT NULL,
  `tbreservaeventohorainicio` time NOT NULL,
  `tbreservaeventohorafin` time NOT NULL,
  `tbreservaeventoactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbreservaevento`
--

INSERT INTO `tbreservaevento` (`tbreservaeventoid`, `tbreservaeventoclienteid`, `tbreservaeventoeventoid`, `tbreservaeventoclienteresponsableid`, `tbreservaeventofecha`, `tbreservaeventohorainicio`, `tbreservaeventohorafin`, `tbreservaeventoactivo`) VALUES
(1, 2, 1, NULL, '2025-09-25', '11:00:00', '13:00:00', 1),
(2, 2, 3, 2, '2025-10-07', '06:00:00', '10:00:00', 1),
(3, 8, 3, 2, '2025-10-07', '06:00:00', '10:00:00', 1),
(4, 9, 3, 2, '2025-10-07', '06:00:00', '10:00:00', 1),
(5, 2, 1, 2, '2025-10-07', '08:00:00', '09:00:00', 1),
(6, 2, 1, 2, '2025-10-07', '08:00:00', '09:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbreservalibre`
--

CREATE TABLE `tbreservalibre` (
  `tbreservalibreid` int(11) NOT NULL,
  `tbreservalibreclienteid` int(11) NOT NULL,
  `tbreservalibrehorariolibreid` int(11) NOT NULL,
  `tbreservalibreclienteresponsableid` int(11) NOT NULL COMMENT 'ID del cliente que realiza la reserva para sí mismo o para otros.',
  `tbreservalibreactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbreservalibre`
--

INSERT INTO `tbreservalibre` (`tbreservalibreid`, `tbreservalibreclienteid`, `tbreservalibrehorariolibreid`, `tbreservalibreclienteresponsableid`, `tbreservalibreactivo`) VALUES
(14, 2, 4, 2, 1),
(16, 2, 3, 2, 1),
(17, 2, 7, 2, 1),
(18, 8, 7, 2, 1),
(19, 9, 7, 2, 1),
(20, 8, 6, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbreservasala`
--

CREATE TABLE `tbreservasala` (
  `tbreservasalaid` int(11) NOT NULL,
  `tbeventoid` int(11) NOT NULL,
  `tbsalaid` varchar(255) NOT NULL,
  `tbreservafecha` date NOT NULL,
  `tbreservahorainicio` time NOT NULL,
  `tbreservahorafin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbreservasala`
--

INSERT INTO `tbreservasala` (`tbreservasalaid`, `tbeventoid`, `tbsalaid`, `tbreservafecha`, `tbreservahorainicio`, `tbreservahorafin`) VALUES
(1, 1, '2$5', '2025-10-07', '08:00:00', '09:00:00'),
(3, 3, '1', '2025-10-07', '06:00:00', '10:00:00'),
(4, 4, '1$2', '2025-10-07', '10:00:00', '16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbsala`
--

CREATE TABLE `tbsala` (
  `tbsalaid` int(11) NOT NULL,
  `tbsalanombre` varchar(150) DEFAULT NULL,
  `tbsalacapacidad` int(11) DEFAULT NULL,
  `tbsalaactivo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbsala`
--

INSERT INTO `tbsala` (`tbsalaid`, `tbsalanombre`, `tbsalacapacidad`, `tbsalaactivo`) VALUES
(1, 'Sala Principal', 100, 1),
(2, 'Sala de Recepción', 14, 1),
(4, 'Sala de Yoga', 25, 1),
(5, 'Sala baile', 40, 0),
(7, 'Sala para cardio', 90, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbcertificado`
--
ALTER TABLE `tbcertificado`
  ADD PRIMARY KEY (`tbcertificadoid`);

--
-- Indexes for table `tbcliente`
--
ALTER TABLE `tbcliente`
  ADD PRIMARY KEY (`tbclienteid`);

--
-- Indexes for table `tbclientepadecimiento`
--
ALTER TABLE `tbclientepadecimiento`
  ADD PRIMARY KEY (`tbclientepadecimientoid`);

--
-- Indexes for table `tbcuerpozona`
--
ALTER TABLE `tbcuerpozona`
  ADD PRIMARY KEY (`tbcuerpozonaid`);

--
-- Indexes for table `tbejerciciofuerza`
--
ALTER TABLE `tbejerciciofuerza`
  ADD PRIMARY KEY (`tbejerciciofuerzaid`);

--
-- Indexes for table `tbevento`
--
ALTER TABLE `tbevento`
  ADD PRIMARY KEY (`tbeventoid`);

--
-- Indexes for table `tbhorario`
--
ALTER TABLE `tbhorario`
  ADD PRIMARY KEY (`tbhorarioid`);

--
-- Indexes for table `tbhorariolibre`
--
ALTER TABLE `tbhorariolibre`
  ADD PRIMARY KEY (`tbhorariolibreid`);

--
-- Indexes for table `tbhorariopersonal`
--
ALTER TABLE `tbhorariopersonal`
  ADD PRIMARY KEY (`tbhorariopersonalid`);

--
-- Indexes for table `tbimagen`
--
ALTER TABLE `tbimagen`
  ADD PRIMARY KEY (`tbimagenid`);

--
-- Indexes for table `tbinstructor`
--
ALTER TABLE `tbinstructor`
  ADD PRIMARY KEY (`tbinstructorid`);

--
-- Indexes for table `tbinstructorhorario`
--
ALTER TABLE `tbinstructorhorario`
  ADD PRIMARY KEY (`tbinstructorhorarioid`);

--
-- Indexes for table `tbnumeroemergencia`
--
ALTER TABLE `tbnumeroemergencia`
  ADD PRIMARY KEY (`tbnumeroemergenciaid`);

--
-- Indexes for table `tbpadecimiento`
--
ALTER TABLE `tbpadecimiento`
  ADD PRIMARY KEY (`tbpadecimientoid`);

--
-- Indexes for table `tbpadecimientodictamen`
--
ALTER TABLE `tbpadecimientodictamen`
  ADD PRIMARY KEY (`tbpadecimientodictamenid`);

--
-- Indexes for table `tbpartezona`
--
ALTER TABLE `tbpartezona`
  ADD PRIMARY KEY (`tbpartezonaid`);

--
-- Indexes for table `tbreservaevento`
--
ALTER TABLE `tbreservaevento`
  ADD PRIMARY KEY (`tbreservaeventoid`);

--
-- Indexes for table `tbreservalibre`
--
ALTER TABLE `tbreservalibre`
  ADD PRIMARY KEY (`tbreservalibreid`);

--
-- Indexes for table `tbreservasala`
--
ALTER TABLE `tbreservasala`
  ADD PRIMARY KEY (`tbreservasalaid`);

--
-- Indexes for table `tbsala`
--
ALTER TABLE `tbsala`
  ADD PRIMARY KEY (`tbsalaid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbcertificado`
--
ALTER TABLE `tbcertificado`
  MODIFY `tbcertificadoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbcliente`
--
ALTER TABLE `tbcliente`
  MODIFY `tbclienteid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbclientepadecimiento`
--
ALTER TABLE `tbclientepadecimiento`
  MODIFY `tbclientepadecimientoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbejerciciofuerza`
--
ALTER TABLE `tbejerciciofuerza`
  MODIFY `tbejerciciofuerzaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbevento`
--
ALTER TABLE `tbevento`
  MODIFY `tbeventoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbhorariolibre`
--
ALTER TABLE `tbhorariolibre`
  MODIFY `tbhorariolibreid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbhorariopersonal`
--
ALTER TABLE `tbhorariopersonal`
  MODIFY `tbhorariopersonalid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbnumeroemergencia`
--
ALTER TABLE `tbnumeroemergencia`
  MODIFY `tbnumeroemergenciaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbpadecimientodictamen`
--
ALTER TABLE `tbpadecimientodictamen`
  MODIFY `tbpadecimientodictamenid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `tbpartezona`
--
ALTER TABLE `tbpartezona`
  MODIFY `tbpartezonaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbreservaevento`
--
ALTER TABLE `tbreservaevento`
  MODIFY `tbreservaeventoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbreservalibre`
--
ALTER TABLE `tbreservalibre`
  MODIFY `tbreservalibreid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbreservasala`
--
ALTER TABLE `tbreservasala`
  MODIFY `tbreservasalaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbsala`
--
ALTER TABLE `tbsala`
  MODIFY `tbsalaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
