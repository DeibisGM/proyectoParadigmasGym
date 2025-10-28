-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2025 at 06:53 AM
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
(5, 1, '', 'Entrenador Personal Certificado', 'Entrenamiento funcional', 'Federación Costarricense de Fitness');

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
(2, '22', '402220655', 'Perfil Cliente', '1999-08-08', 67803080, 'cliente@gmail.com', 'Heredia, centro', 'M', '2025-09-01', '12345678', 1),
(21, '23', '703032153', 'Deibys Gutierrez', '2003-10-06', 64656864, 'deibys.una@gmail.com', 'Heredia, Sarapiquí, La Victoria', 'M', '2023-09-01', '12345678', 1),
(22, '24', '703010252', 'Ciany Amador', '2002-07-23', 64658752, 'ciany.una@gmail.com', 'Heredia, Sarapiquí, Naranjal', 'F', '2025-10-23', '12345678', 1),
(23, '25', '102036845', 'Noelia Fallas', '2002-01-01', 89563247, 'noelia.una@gmail.com', 'Limón, Pococí, Cariari', 'F', '2024-10-22', '12345678', 1),
(27, '51', '402523685', 'Yeimer Requene', '2001-08-24', 64536987, 'yeimer.una@gmail.com', 'Heredia, Sarapiquí, La Victoria', 'M', '2025-02-15', '12345678', 1);

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
(2, 23, '13', 60),
(3, 22, '14', 61),
(4, 27, '13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbcuerpozona`
--

CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int(11) NOT NULL,
  `tbcuerpozonasubzonaid` text DEFAULT NULL,
  `tbcuerpozonaimagenesids` text DEFAULT NULL,
  `tbcuerpozonanombre` varchar(100) NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbcuerpozona`
--

INSERT INTO `tbcuerpozona` (`tbcuerpozonaid`, `tbcuerpozonasubzonaid`, `tbcuerpozonaimagenesids`, `tbcuerpozonanombre`, `tbcuerpozonadescripcion`, `tbcuerpozonaactivo`) VALUES
(2, '15$29$30$31', '53', 'Pecho', 'Músculos ubicados en la parte frontal del torso.', 0),
(3, '11$12$13$14', '54', 'Espalda', 'Zona posterior del tronco que va desde el cuello hasta la cintura.', 1),
(4, '23$24$25$26$27$28', '55', 'Piernas', 'Conjunto de músculos desde la cadera hasta los pies.', 1),
(5, '16$17$18$19', '56', 'Hombros', 'Zona que conecta los brazos con el tronco.', 1),
(6, '8$9$10', '57', 'Abdomen', 'Conjunto de músculos en la parte frontal y lateral del torso.', 1),
(7, '20$21$22', '58', 'Brazos', 'Músculos que van desde el hombro hasta la muñeca.', 1),
(8, '37', '59', 'Tronco', ' Conjunto que rodea el centro del cuerpo.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbejercicioequilibrio`
--

CREATE TABLE `tbejercicioequilibrio` (
  `tbejercicioequilibrioid` int(11) NOT NULL,
  `tbejercicioequilibrionombre` varchar(255) NOT NULL,
  `tbejercicioequilibriodescripcion` text NOT NULL,
  `tbejercicioequilibriodificultad` varchar(50) NOT NULL,
  `tbejercicioequilibrioduracion` int(11) NOT NULL,
  `tbejercicioequilibriomateriales` varchar(255) DEFAULT NULL,
  `tbejercicioequilibriopostura` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbejercicioequilibrio`
--

INSERT INTO `tbejercicioequilibrio` (`tbejercicioequilibrioid`, `tbejercicioequilibrionombre`, `tbejercicioequilibriodescripcion`, `tbejercicioequilibriodificultad`, `tbejercicioequilibrioduracion`, `tbejercicioequilibriomateriales`, `tbejercicioequilibriopostura`) VALUES
(2, 'Postura del arbol', 'Coloca un pie sobre el muslo interno de la pierna opuesta y junta las manos sobre la cabeza.', 'Intermedio', 30, 'esterilla', 'De pie'),
(3, 'Toques alternos con balón', 'Alterna toques rápidos con los pies sobre una pelota sin perder el equilibrio.', 'Intermedio', 45, 'pelota', 'En movimiento'),
(4, 'Tai Chi básico', 'Movimientos lentos y controlados combinando respiración y equilibrio.', 'Intermedio', 60, '', 'En movimiento'),
(5, 'Caminata en línea recta (talón con punta)', 'Camina en línea colocando el talón de un pie justo frente a los dedos del otro.', 'Principiante', 40, 'Cinta adhesiva en el suelo como guía', 'En movimiento'),
(6, 'BOSU o cojín inestable', 'Realiza equilibrio de pie sobre una superficie inestable (BOSU o cojín de aire).', 'Avanzado', 30, 'BOSU o cojín de equilibrio', 'De pie');

-- --------------------------------------------------------

--
-- Table structure for table `tbejercicioflexibilidad`
--

CREATE TABLE `tbejercicioflexibilidad` (
  `tbejercicioflexibilidadid` int(11) NOT NULL,
  `tbejercicioflexibilidadnombre` varchar(100) DEFAULT NULL,
  `tbejercicioflexibilidaddescripcion` varchar(250) DEFAULT NULL,
  `tbejercicioflexibilidadduracion` varchar(45) DEFAULT NULL,
  `tbejercicioflexibilidadseries` int(11) DEFAULT NULL,
  `tbejercicioflexibilidadequipodeayuda` varchar(100) DEFAULT NULL,
  `tbejercicioflexibilidadactivo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbejercicioflexibilidad`
--

INSERT INTO `tbejercicioflexibilidad` (`tbejercicioflexibilidadid`, `tbejercicioflexibilidadnombre`, `tbejercicioflexibilidaddescripcion`, `tbejercicioflexibilidadduracion`, `tbejercicioflexibilidadseries`, `tbejercicioflexibilidadequipodeayuda`, `tbejercicioflexibilidadactivo`) VALUES
(3, 'Estiramiento de gemelos contra la pared', 'se apoyan las manos en la pared, se adelanta una pierna y se mantien la otra extendida atrás con el talón tocando el suelo', '30', 4, 'Silla', 1),
(4, 'Estiramiento de aductores', 'Sentado con las plantas de los pies juntas se sostienen los tobillos y se presionan suavemente las rodillas hacia el suelo', '60', 3, 'colchoneta', 1),
(5, 'Yoga', 'Son una serie de posturas las cuales cambian de forma suve', '90', 2, 'colchoneta', 1);

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
  `tbejerciciofuerzapeso` tinyint(1) DEFAULT NULL,
  `tbejerciciofuerzadescanso` double DEFAULT NULL,
  `tbejerciciofuerzaactivo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbejerciciofuerza`
--

INSERT INTO `tbejerciciofuerza` (`tbejerciciofuerzaid`, `tbejerciciofuerzanombre`, `tbejerciciofuerzadescripcion`, `tbejerciciofuerzarepeticion`, `tbejerciciofuerzaserie`, `tbejerciciofuerzapeso`, `tbejerciciofuerzadescanso`, `tbejerciciofuerzaactivo`) VALUES
(6, 'Jalón pecho', 'Es jalar una cuerda al pecho', 12, 3, 1, 32, 1),
(7, 'Plancha', 'Mantener el cuerpo recto apoyando los antebrazos y punta de los', 2, 1, 0, 60, 0),
(9, 'Peso muerto', 'Levantar pesas desde el suelo hasta la altura de la cadera con la espalda recta', 12, 3, 1, 40, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbejercicioresistencia`
--

CREATE TABLE `tbejercicioresistencia` (
  `tbejercicioresistenciaid` int(11) NOT NULL,
  `tbejercicioresistencianombre` varchar(50) NOT NULL,
  `tbejercicioresistenciatiempo` varchar(50) NOT NULL,
  `tbejercicioresistenciapeso` tinyint(1) NOT NULL,
  `tbejercicioresistenciadescripcion` varchar(500) DEFAULT NULL,
  `tbejercicioresistenciaactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbejercicioresistencia`
--

INSERT INTO `tbejercicioresistencia` (`tbejercicioresistenciaid`, `tbejercicioresistencianombre`, `tbejercicioresistenciatiempo`, `tbejercicioresistenciapeso`, `tbejercicioresistenciadescripcion`, `tbejercicioresistenciaactivo`) VALUES
(7, 'Sentadilla', '30 a 60 segundos', 0, 'Pies al ancho de hombros, rodillas siguen la línea de los dedos, columna neutra, talones al suelo, baja al menos a paralelo manteniendo el torso firme.', 1),
(8, 'Zancadas alternas', '30 a 60 segundos', 0, 'Paso largo al frente, baja hasta 90° en ambas rodillas, torso erguido, empuja con la delantera para volver y alterna.', 1),
(9, 'Plancha frontal', '30 a 60 segundos', 0, 'Antebrazos al suelo, hombros sobre codos, cuerpo en línea cabeza–talones, abdomen y glúteos contraídos, no hundir la cadera.', 1),
(10, 'Flexiones', '30 a 60 segundos', 0, 'Manos bajo hombros, cuerpo en línea, baja hasta rozar el pecho con el suelo y sube extendiendo codos; rodillas al piso si necesitas.', 1),
(11, 'Remo con banda elástica', '30 a 60 segundos', 1, 'Banda anclada al frente a la altura del pecho, torso erguido, tira de la banda llevando codos atrás y juntando escápulas; vuelve controlado.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbejerciciosubzona`
--

CREATE TABLE `tbejerciciosubzona` (
  `tbejerciciosubzonaid` int(11) NOT NULL,
  `tbejerciciosubzonaejercicioid` int(11) NOT NULL,
  `tbejerciciosubzonasubid` varchar(500) NOT NULL,
  `tbejerciciosubzonanombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbejerciciosubzona`
--

INSERT INTO `tbejerciciosubzona` (`tbejerciciosubzonaid`, `tbejerciciosubzonaejercicioid`, `tbejerciciosubzonasubid`, `tbejerciciosubzonanombre`) VALUES
(4, 7, '23$25$26$27$28', 'Resistencia'),
(5, 8, '26$27$28', 'Resistencia'),
(6, 9, '9$10$26$37', 'Resistencia'),
(7, 10, '16$21$29$30$31', 'Resistencia'),
(8, 11, '12$13$22$37', 'Resistencia'),
(11, 3, '25', 'Flexibilidad'),
(12, 4, '10', 'Flexibilidad'),
(13, 5, '8', 'Flexibilidad'),
(14, 6, '8$9$10$11$20$29$30', 'Fuerza'),
(15, 7, '10$20$21$26$30', 'Fuerza'),
(17, 9, '20$21$22$28$29', 'Fuerza'),
(19, 2, '23', 'Equilibrio'),
(20, 2, '25', 'Equilibrio'),
(21, 2, '26', 'Equilibrio'),
(22, 2, '37', 'Equilibrio'),
(23, 3, '9', 'Equilibrio'),
(24, 3, '16', 'Equilibrio'),
(25, 3, '28', 'Equilibrio'),
(26, 3, '37', 'Equilibrio'),
(27, 4, '25', 'Equilibrio'),
(28, 4, '26', 'Equilibrio'),
(29, 4, '28', 'Equilibrio'),
(30, 4, '37', 'Equilibrio'),
(31, 5, '23', 'Equilibrio'),
(32, 5, '24', 'Equilibrio'),
(33, 5, '25', 'Equilibrio'),
(34, 5, '26', 'Equilibrio'),
(35, 6, '23', 'Equilibrio'),
(36, 6, '24', 'Equilibrio'),
(37, 6, '25', 'Equilibrio'),
(38, 6, '26', 'Equilibrio'),
(39, 6, '28', 'Equilibrio'),
(40, 6, '37', 'Equilibrio');

-- --------------------------------------------------------

--
-- Table structure for table `tbevento`
--

CREATE TABLE `tbevento` (
  `tbeventoid` int(11) NOT NULL,
  `tbinstructorid` int(11) NOT NULL,
  `tbeventotipo` varchar(20) NOT NULL DEFAULT 'abierto',
  `tbeventonombre` varchar(255) NOT NULL,
  `tbeventodescripcion` text DEFAULT NULL,
  `tbeventofecha` date NOT NULL,
  `tbeventohorainicio` time NOT NULL,
  `tbeventohorafin` time NOT NULL,
  `tbeventoaforo` int(11) NOT NULL,
  `tbeventoactivo` int(11) NOT NULL DEFAULT 1
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
(22, '/img/cli00020022.jpg', 1),
(23, '/img/cli00210023.jpg', 1),
(24, '/img/cli00220024.jpg', 1),
(25, '/img/cli00230025.jpg', 1),
(27, '/img/par00080027.jpg', 1),
(28, '/img/par00090028.jpg', 1),
(29, '/img/par00100029.jpg', 1),
(30, '/img/par00110030.jpg', 1),
(31, '/img/par00120031.jpg', 1),
(32, '/img/par00130032.jpg', 1),
(33, '/img/par00140033.jpg', 1),
(34, '/img/par00150034.jpg', 1),
(35, '/img/par00160035.jpg', 1),
(36, '/img/par00180036.jpg', 1),
(37, '/img/par00170037.jpg', 1),
(38, '/img/par00190038.jpg', 1),
(39, '/img/par00200039.jpg', 1),
(40, '/img/par00210040.jpg', 1),
(41, '/img/par00220041.jpg', 1),
(42, '/img/par00230042.jpg', 1),
(43, '/img/par00240043.jpg', 1),
(44, '/img/par00250044.jpg', 1),
(45, '/img/par00260045.jpg', 1),
(46, '/img/par00270046.jpg', 1),
(47, '/img/par00280047.jpg', 1),
(48, '/img/par00290048.jpg', 1),
(49, '/img/par00300049.jpg', 1),
(50, '/img/par00310050.jpg', 1),
(51, '/img/cli00270051.jpg', 1),
(52, '/img/par00370052.jpg', 1),
(53, '/img/cue00020053.jpg', 1),
(54, '/img/cue00030054.jpg', 1),
(55, '/img/cue00040055.jpg', 1),
(56, '/img/cue00050056.jpg', 1),
(57, '/img/cue00060057.jpg', 1),
(58, '/img/cue00070058.jpg', 1),
(59, '/img/cue00080059.jpg', 1);

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
('005', '11', 'Noelia Fallas', '60728094', 'Cariari, Limón', 'noelia.fallas@gmail.com', 'CR4501234567890123', '78834', '', 1),
('006', NULL, 'Gustavo Jiménez', '62278998', '200 m este de la Plaza de Semillero, Cariari', 'gustavo.jimenez@gmail.com', 'CR7356293734000000', '6666', '', 1),
('007', NULL, 'Roberto Sánchez', '78294672', 'Astúa Pirie, frente a la bomba', 'roberto.sanchez@gmail.com', 'CR8902306513780000', '7777', '', 1),
('008', NULL, 'María Fernández', '71024589', 'Guápiles centro, Limón', 'maria.fernandez@gmail.com', 'CR4801234567896543', 'mf2025', NULL, 1),
('009', NULL, 'José Rodríguez', '72058963', 'San Pablo, Heredia', 'jose.rodriguez@gmail.com', 'CR1209876543212345', 'jr2025', NULL, 1),
('010', NULL, 'Katherine Solano', '85017264', 'Alajuela centro', 'katherine.solano@gmail.com', 'CR6701239876543021', 'ks2025', NULL, 1),
('011', NULL, 'Luis Morales', '60457821', 'San Isidro de Heredia', 'luis.morales@gmail.com', 'CR2301458764321987', 'lm2025', NULL, 1),
('1', '', 'Deibis Gutiérrez M.', '60648399', 'San Francisco, Heredia', 'deibis.gutierrez@gmail.com', 'CR0849284584394500', '5454', '', 1),
('2', '6', 'Andrea Solís', '77777777', 'Cariari, Limón', 'andrea.solis@gmail.com', 'CR1234567890123456', '12345678', NULL, 1),
('3', '', 'Carlos Duarte', '44444444', 'Heredia centro', 'carlos.duarte@gmail.com', 'CR5409876543217890', '4444', '', 0),
('4', NULL, 'Anthony Cubillo', '80290948', 'Cariari, Limón', 'anthony.cubillo@gmail.com', 'CR4582946270000000', '12345', NULL, 1);

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
(1, 21, 'Mileidy Morales', '85649560', 'Madre'),
(3, 22, 'Luz Solís', '87569532', 'Madre'),
(4, 27, 'Yadir Sanchez', '87253641', 'Hermano');

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
(60, '2025-10-16', 'Clínica de Río Frío', ''),
(61, '2025-10-24', 'Hospital San Juan de Dios', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbreservaevento`
--

CREATE TABLE `tbreservaevento` (
  `tbreservaeventoid` int(11) NOT NULL,
  `tbreservaeventoclienteid` int(11) NOT NULL,
  `tbreservaeventoeventoid` int(11) NOT NULL,
  `tbreservaeventoclienteresponsableid` int(11) DEFAULT NULL,
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
  `tbreservalibreclienteresponsableid` int(11) NOT NULL,
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
-- Table structure for table `tbrutina`
--

CREATE TABLE `tbrutina` (
  `tbrutinaid` int(11) NOT NULL,
  `tbclienteid` int(11) NOT NULL,
  `tbrutinafecha` date NOT NULL,
  `tbrutinaobservacion` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbrutina`
--

INSERT INTO `tbrutina` (`tbrutinaid`, `tbclienteid`, `tbrutinafecha`, `tbrutinaobservacion`) VALUES
(1, 2, '2025-10-26', 'Bien fuerte'),
(2, 2, '2025-10-26', ''),
(3, 2, '2025-10-26', ''),
(4, 2, '2025-10-28', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbrutinaejercicio`
--

CREATE TABLE `tbrutinaejercicio` (
  `tbrutinaejercicioid` int(11) NOT NULL,
  `tbrutinaid` int(11) NOT NULL,
  `tbrutinaejerciciotipo` varchar(50) NOT NULL,
  `tbejercicioid` int(11) NOT NULL,
  `tbrutinaejercicioseries` int(11) DEFAULT NULL,
  `tbrutinaejerciciorepeticiones` int(11) DEFAULT NULL,
  `tbrutinaejerciciopeso` double DEFAULT NULL,
  `tbrutinaejerciciotiempo_seg` int(11) DEFAULT NULL,
  `tbrutinaejerciciodescanso_seg` int(11) DEFAULT NULL,
  `tbrutinaejerciciocomentario` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbrutinaejercicio`
--

INSERT INTO `tbrutinaejercicio` (`tbrutinaejercicioid`, `tbrutinaid`, `tbrutinaejerciciotipo`, `tbejercicioid`, `tbrutinaejercicioseries`, `tbrutinaejerciciorepeticiones`, `tbrutinaejerciciopeso`, `tbrutinaejerciciotiempo_seg`, `tbrutinaejerciciodescanso_seg`, `tbrutinaejerciciocomentario`) VALUES
(1, 1, 'resistencia', 7, 4, 12, 14, 0, 0, ''),
(2, 1, 'fuerza', 2, 4, 12, 72, 0, 60, ''),
(3, 2, 'resistencia', 10, 4, 12, 0, 0, 60, ''),
(4, 3, 'resistencia', 9, 3, 1, 0, 60, 60, ''),
(5, 4, 'fuerza', 6, 4, 12, 55, 0, 0, 'La ultima es al fallo'),
(6, 4, 'resistencia', 8, 4, 0, 0, 60, 60, ''),
(7, 4, 'equilibrio', 6, NULL, NULL, NULL, 60, NULL, ''),
(8, 4, 'flexibilidad', 3, 4, NULL, NULL, 60, NULL, '');

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

-- --------------------------------------------------------

--
-- Table structure for table `tbsubzona`
--

CREATE TABLE `tbsubzona` (
  `tbsubzonaid` int(11) NOT NULL,
  `tbsubzonaimagenid` varchar(100) DEFAULT NULL,
  `tbsubzonanombre` varchar(50) NOT NULL,
  `tbsubzonadescripcion` varchar(100) DEFAULT NULL,
  `tbsubzonaactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbsubzona`
--

INSERT INTO `tbsubzona` (`tbsubzonaid`, `tbsubzonaimagenid`, `tbsubzonanombre`, `tbsubzonadescripcion`, `tbsubzonaactivo`) VALUES
(8, '27', 'Recto abdominal', 'Tabla o conocido como six pack.', 1),
(9, '28', 'Oblicuos', 'Laterales del abdomen.', 1),
(10, '29', 'Transverso abdominal', 'Parte interna, estabilizadora.', 1),
(11, '30', 'Dorsales', 'Parte lateral grande de la espalda.', 1),
(12, '31', 'Trapecio', 'Parte superior de la espalda.', 1),
(13, '32', 'Romboides', 'Entre los omóplatos.', 1),
(14, '33', 'Lumbar', 'Parte baja de la espalda.', 1),
(15, '34', 'Serrato anterior', 'Costado del pecho, debajo del brazo.', 1),
(16, '35', 'Deltoide anterior', 'Parte frontal del hombro.', 1),
(17, '37', 'Deltoide lateral', 'Parte media del hombro.', 1),
(18, '36', 'Deltoide posterior', 'Parte trasera del hombro.', 1),
(19, '38', 'Trapecio superior', 'Parte superior entre cuello y hombros.', 1),
(20, '39', 'Antebrazos', 'Parte inferior del brazo, cerca de la muñeca.', 1),
(21, '40', 'Tríceps', 'Parte trasera del brazo.', 1),
(22, '41', 'Bíceps', 'Parte frontal del brazo.', 1),
(23, '42', 'Aductores', 'Parte interna de la pierna.', 1),
(24, '43', 'Abductores', 'Parte externa de la pierna.', 1),
(25, '44', 'Pantorrillas o Gemelos', 'Parte inferior trasera de la pierna.', 1),
(26, '45', 'Glúteos', 'Parte posterior de la cadera.', 1),
(27, '46', 'Isquiotibiales o femorales', 'Parte trasera del muslo.', 1),
(28, '47', 'Cuádriceps', 'Parte frontal del muslo.', 1),
(29, '48', 'Pectoral superior', 'Parte alta del pecho.', 1),
(30, '49', 'Pectoral medio', 'Zona central del pecho.', 1),
(31, '50', 'Pectoral inferior', 'Parte baja del pecho.', 1),
(37, '52', 'Core', 'Músculos que rodean la columna.', 1);

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
-- Indexes for table `tbejercicioequilibrio`
--
ALTER TABLE `tbejercicioequilibrio`
  ADD PRIMARY KEY (`tbejercicioequilibrioid`);

--
-- Indexes for table `tbejercicioflexibilidad`
--
ALTER TABLE `tbejercicioflexibilidad`
  ADD PRIMARY KEY (`tbejercicioflexibilidadid`);

--
-- Indexes for table `tbejerciciofuerza`
--
ALTER TABLE `tbejerciciofuerza`
  ADD PRIMARY KEY (`tbejerciciofuerzaid`);

--
-- Indexes for table `tbejercicioresistencia`
--
ALTER TABLE `tbejercicioresistencia`
  ADD PRIMARY KEY (`tbejercicioresistenciaid`);

--
-- Indexes for table `tbejerciciosubzona`
--
ALTER TABLE `tbejerciciosubzona`
  ADD PRIMARY KEY (`tbejerciciosubzonaid`);

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
-- Indexes for table `tbrutina`
--
ALTER TABLE `tbrutina`
  ADD PRIMARY KEY (`tbrutinaid`);

--
-- Indexes for table `tbrutinaejercicio`
--
ALTER TABLE `tbrutinaejercicio`
  ADD PRIMARY KEY (`tbrutinaejercicioid`);

--
-- Indexes for table `tbsala`
--
ALTER TABLE `tbsala`
  ADD PRIMARY KEY (`tbsalaid`);

--
-- Indexes for table `tbsubzona`
--
ALTER TABLE `tbsubzona`
  ADD PRIMARY KEY (`tbsubzonaid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbcertificado`
--
ALTER TABLE `tbcertificado`
  MODIFY `tbcertificadoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbcliente`
--
ALTER TABLE `tbcliente`
  MODIFY `tbclienteid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tbclientepadecimiento`
--
ALTER TABLE `tbclientepadecimiento`
  MODIFY `tbclientepadecimientoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbejercicioequilibrio`
--
ALTER TABLE `tbejercicioequilibrio`
  MODIFY `tbejercicioequilibrioid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbejercicioflexibilidad`
--
ALTER TABLE `tbejercicioflexibilidad`
  MODIFY `tbejercicioflexibilidadid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbejerciciofuerza`
--
ALTER TABLE `tbejerciciofuerza`
  MODIFY `tbejerciciofuerzaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbejercicioresistencia`
--
ALTER TABLE `tbejercicioresistencia`
  MODIFY `tbejercicioresistenciaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbejerciciosubzona`
--
ALTER TABLE `tbejerciciosubzona`
  MODIFY `tbejerciciosubzonaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
  MODIFY `tbpadecimientodictamenid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

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
-- AUTO_INCREMENT for table `tbrutina`
--
ALTER TABLE `tbrutina`
  MODIFY `tbrutinaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbrutinaejercicio`
--
ALTER TABLE `tbrutinaejercicio`
  MODIFY `tbrutinaejercicioid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbsala`
--
ALTER TABLE `tbsala`
  MODIFY `tbsalaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbsubzona`
--
ALTER TABLE `tbsubzona`
  MODIFY `tbsubzonaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
