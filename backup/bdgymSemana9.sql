SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `tbcertificado` (
  `tbcertificadoid` int(11) NOT NULL,
  `tbinstructorid` int(11) DEFAULT NULL,
  `tbcertificadoimagenid` varchar(250) DEFAULT NULL,
  `tbcertificadonombre` varchar(100) DEFAULT NULL,
  `tbcertificadodescripcion` varchar(500) DEFAULT NULL,
  `tbcertificadoentidad` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbcertificado` (`tbcertificadoid`, `tbinstructorid`, `tbcertificadoimagenid`, `tbcertificadonombre`, `tbcertificadodescripcion`, `tbcertificadoentidad`) VALUES
(1, 2, '26', 'Entrenador Personal Certificado', 'Certificación en entrenamiento personal y funcional.', 'Federación Costarricense de Fitness'),
(3, 1, '', 'Especialista en Nutrición Deportiva', 'Certificado en planes de nutrición para atletas.', 'Colegio de Nutricionistas de Costa Rica'),
(5, 1, '', 'Entrenador Personal Certificado', 'Entrenamiento funcional', 'Federación Costarricense de Fitness'),
(15, 3, '', 'n', 'n', 'n');

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
  `tbclienteestado` tinyint(1) NOT NULL DEFAULT 1,
  `tbclientecontrasena` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbcliente` (`tbclienteid`, `tbclienteimagenid`, `tbclientecarnet`, `tbclientenombre`, `tbclientefechanacimiento`, `tbclientetelefono`, `tbclientecorreo`, `tbclientedireccion`, `tbclientegenero`, `tbclienteinscripcion`, `tbclienteestado`, `tbclientecontrasena`) VALUES
(2, '5', '402220655', 'Mario corrales alpizar', '1999-08-08', 67803080, 'cliente@gmail.com', 'Heredia, centro', 'M', '2025-09-01', 1, '12345678'),
(8, NULL, '409880755', 'Cristian Carrillo', '1997-08-17', 87654532, 'cristian@gmail.com', 'Heredia, sarapiqui', 'M', '2025-08-05', 1, '12345678'),
(9, NULL, '406650369', 'Jefferson Carrillo', '1994-08-10', 84859630, 'jeffcar@gmail.com', 'San José', 'M', '2025-08-14', 1, '12345678'),
(15, NULL, '501020201', 'Casandra Lascurein', '2000-10-02', 84582632, 'cassandra@gmail.com', 'heredia, centro', 'F', '2025-09-01', 1, '12345678');

CREATE TABLE `tbclientepadecimiento` (
  `tbclientepadecimientoid` int(11) NOT NULL,
  `tbclienteid` int(11) DEFAULT NULL,
  `tbpadecimientoid` text DEFAULT NULL,
  `tbpadecimientodictamenid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbclientepadecimiento` (`tbclientepadecimientoid`, `tbclienteid`, `tbpadecimientoid`, `tbpadecimientodictamenid`) VALUES
(1, 2, '9$10', NULL),
(2, 9, '5', NULL),
(4, 8, '12$13', NULL),
(5, 15, '10$9', 17);

CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int(11) NOT NULL,
  `tbcuerpozonapartezonaid` text DEFAULT NULL,
  `tbcuerpozonaimagenesids` text DEFAULT NULL,
  `tbcuerpozonanombre` varchar(100) NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbcuerpozona` (`tbcuerpozonaid`, `tbcuerpozonapartezonaid`, `tbcuerpozonaimagenesids`, `tbcuerpozonanombre`, `tbcuerpozonadescripcion`, `tbcuerpozonaactivo`) VALUES
(2, '1', NULL, 'Pecho', 'Ejercicios para el desarrollo de los músculos pectorales.', 0),
(3, NULL, '1$4', 'Espalda', 'Ejercicios para fortalecer la espalda, incluyendo dorsales y lumbares.', 1),
(4, NULL, '3', 'Piernas', 'Rutinas para cuádriceps, isquiotibiales y pantorrillas.', 1);

CREATE TABLE `tbevento` (
  `tbeventoid` int(11) NOT NULL,
  `tbeventonombre` varchar(255) NOT NULL,
  `tbeventodescripcion` text DEFAULT NULL,
  `tbeventofecha` date NOT NULL,
  `tbeventohorainicio` time NOT NULL,
  `tbeventohorafin` time NOT NULL,
  `tbeventoaforo` int(11) NOT NULL,
  `tbinstructorid` int(11) NOT NULL,
  `tbeventoestado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbevento` (`tbeventoid`, `tbeventonombre`, `tbeventodescripcion`, `tbeventofecha`, `tbeventohorainicio`, `tbeventohorafin`, `tbeventoaforo`, `tbinstructorid`, `tbeventoestado`) VALUES
(1, 'Zumba Grupal', '', '2025-09-25', '11:00:00', '13:00:00', 15, 2, 1);

CREATE TABLE `tbhorario` (
  `tbhorarioid` int(11) NOT NULL,
  `tbhorariodia` varchar(25) NOT NULL,
  `tbhorarioactivo` tinyint(1) NOT NULL DEFAULT 0,
  `tbhorarioapertura` time DEFAULT NULL,
  `tbhorariocierre` time DEFAULT NULL,
  `tbhorariobloqueos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbhorario` (`tbhorarioid`, `tbhorariodia`, `tbhorarioactivo`, `tbhorarioapertura`, `tbhorariocierre`, `tbhorariobloqueos`) VALUES
(1, 'Lunes', 1, '05:00:00', '21:00:00', '13:00&14:00$15:00&15:15'),
(2, 'Martes', 1, '06:00:00', '21:00:00', ''),
(3, 'Miércoles', 1, '06:00:00', '21:00:00', ''),
(4, 'Jueves', 1, '06:00:00', '21:00:00', ''),
(5, 'Viernes', 1, '06:00:00', '21:00:00', ''),
(6, 'Sábado', 1, '08:00:00', '16:00:00', ''),
(7, 'Domingo', 0, NULL, NULL, '');

CREATE TABLE `tbimagen` (
  `tbimagenid` int(11) NOT NULL,
  `tbimagenruta` varchar(255) NOT NULL,
  `tbimagenactivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(21, '/img/par00010021.jpg', 1),
(22, '/img/pad00010022.jpg', 1),
(23, '/img/pad00020023.jpg', 1),
(24, '/img/pad00030024.jpg', 1),
(26, '/img/cert00010026.jpg', 1),
(27, '/img/pad00070027.jpg', 1),
(28, '/img/pad00080028.jpg', 1),
(30, '/img/pad00170030.jpg', 1);

CREATE TABLE `tbinstructor` (
  `tbinstructorid` varchar(100) NOT NULL,
  `tbinstructorimagenid` varchar(250) DEFAULT NULL,
  `tbinstructornombre` varchar(100) DEFAULT NULL,
  `tbinstructortelefono` varchar(100) DEFAULT NULL,
  `tbinstructordireccion` varchar(500) DEFAULT NULL,
  `tbinstructorcorreo` varchar(500) DEFAULT NULL,
  `tbinstructorcuenta` varchar(500) DEFAULT NULL,
  `tbinstructorcontraseña` varchar(100) DEFAULT NULL,
  `tbinstructoractivo` tinyint(1) NOT NULL DEFAULT 1,
  `tbinstructorcertificado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbinstructor` (`tbinstructorid`, `tbinstructorimagenid`, `tbinstructornombre`, `tbinstructortelefono`, `tbinstructordireccion`, `tbinstructorcorreo`, `tbinstructorcuenta`, `tbinstructorcontraseña`, `tbinstructoractivo`, `tbinstructorcertificado`) VALUES
('005', '11', 'Noelia Fallas', '60728094', 'Cariari, caribe', 'Noelia@gmail.com', 'Cr45272883', '78834', 1, ''),
('1', '', 'Deibis Gutierrez M', '60648399', 'San Francisco, Heredia', 'deibis.gutierrez@gym.com', 'CR08492845843945', '5454', 0, ''),
('2', '6', 'Andrea', '77777777', 'Cariari, caribe', 'instructor@gmail.com', '', '12345678', 1, NULL),
('3', '', 'c', '4444444444', 'd', 'dsads@gmail.com', '', '4444', 1, ''),
('4', NULL, 'Anthony cubillo', '80290948', 'Cariari, caribe', 'tony@gmail.com', 'CR458294627', '12345', 1, NULL);

CREATE TABLE `tbnumeroemergencia` (
  `tbnumeroemergenciaid` int(11) NOT NULL,
  `tbnumeroemergenciaclienteid` int(11) NOT NULL,
  `tbnumeroemergencianombre` varchar(50) NOT NULL,
  `tbnumeroemergenciatelefono` varchar(8) NOT NULL,
  `tbnumeroemergenciarelacion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbnumeroemergencia` (`tbnumeroemergenciaid`, `tbnumeroemergenciaclienteid`, `tbnumeroemergencianombre`, `tbnumeroemergenciatelefono`, `tbnumeroemergenciarelacion`) VALUES
(2, 2, 'Carlos Corrales', '85649560', 'Padre'),
(3, 2, 'Juana Araya', '85749547', 'Hermana'),
(4, 15, 'Maria Lascurein', '87256984', 'Hermana');

CREATE TABLE `tbpadecimiento` (
  `tbpadecimientoid` int(11) NOT NULL,
  `tbpadecimientotipo` varchar(200) DEFAULT NULL,
  `tbpadecimientonombre` varchar(200) DEFAULT NULL,
  `tbpadecimientodescripcion` text DEFAULT NULL,
  `tbpadecimientoformadeactuar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbpadecimiento` (`tbpadecimientoid`, `tbpadecimientotipo`, `tbpadecimientonombre`, `tbpadecimientodescripcion`, `tbpadecimientoformadeactuar`) VALUES
(5, 'Trastorno', 'Estrés post traumático', 'Problemas con la salud mental', 'Respiraciones para relajar'),
(8, 'Discapacidad', 'Parálisis de extremidades inferiores', 'No puede usar sus piernas', 'Realizar ejercicios de acuerdo a sus capacidades'),
(9, 'Enfermedad', 'Asma', 'Falta de aire en los pulmones', 'Utilizar la bomba de aire'),
(10, 'Enfermedad', 'Diabetes', 'Exceso de azucar en la sangre', 'Aplicar insulina'),
(12, 'Trastorno', 'Autismo', 'Afecta la forma de socializar y la comunicación', 'Tener paciencia y darle su espacio'),
(13, 'Lesión', 'Quebradura', 'Quebradura de una extremidad', 'No hacer ejercicios con la zona afectada'),
(14, 'Enfermedad', 'Artritis', 'Dolor en las articulaciones', 'Tomando medicamento');

CREATE TABLE `tbpadecimientodictamen` (
  `tbpadecimientodictamenid` int(11) NOT NULL,
  `tbpadecimientodictamenfechaemision` date DEFAULT NULL,
  `tbpadecimientodictamenentidademision` varchar(100) DEFAULT NULL,
  `tbpadecimientodictamenimagenid` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbpadecimientodictamen` (`tbpadecimientodictamenid`, `tbpadecimientodictamenfechaemision`, `tbpadecimientodictamenentidademision`, `tbpadecimientodictamenimagenid`) VALUES
(17, '2025-09-12', 'Clinica Biblica', '30');

CREATE TABLE `tbpartezona` (
  `tbpartezonaid` int(11) NOT NULL,
  `tbpartezonaimagenid` varchar(100) DEFAULT NULL,
  `tbpartezonanombre` varchar(50) NOT NULL,
  `tbpartezonadescripcion` varchar(100) DEFAULT NULL,
  `tbpartezonaactivo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbpartezona` (`tbpartezonaid`, `tbpartezonaimagenid`, `tbpartezonanombre`, `tbpartezonadescripcion`, `tbpartezonaactivo`) VALUES
(1, '21', 'Pectoral', 'Parte frontal del pecho', 1),
(2, '', 'Superior izquierdo', 'Parte de arriba', 0),
(3, '', 'Baja', 'pechoss', 1),
(4, '', 'Medio', 'Parte del abdomen', 1);

CREATE TABLE `tbreserva` (
  `tbreservaid` int(11) NOT NULL,
  `tbclienteid` int(11) NOT NULL,
  `tbeventoid` int(11) DEFAULT NULL,
  `tbreservafecha` date NOT NULL,
  `tbreservahorainicio` time NOT NULL,
  `tbreservahorafin` time NOT NULL,
  `tbreservaestado` varchar(20) NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbreserva` (`tbreservaid`, `tbclienteid`, `tbeventoid`, `tbreservafecha`, `tbreservahorainicio`, `tbreservahorafin`, `tbreservaestado`) VALUES
(6, 2, 1, '2025-09-25', '11:00:00', '13:00:00', 'activa');

CREATE TABLE `tbreservasala` (
  `tbreservasalaid` int(11) NOT NULL,
  `tbeventoid` int(11) NOT NULL,
  `tbsalaid` varchar(255) NOT NULL,
  `tbreservafecha` date NOT NULL,
  `tbreservahorainicio` time NOT NULL,
  `tbreservahorafin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbreservasala` (`tbreservasalaid`, `tbeventoid`, `tbsalaid`, `tbreservafecha`, `tbreservahorainicio`, `tbreservahorafin`) VALUES
(1, 1, '2$5', '2025-09-25', '11:00:00', '13:00:00');

CREATE TABLE `tbsala` (
  `tbsalaid` int(11) NOT NULL,
  `tbsalanombre` varchar(150) DEFAULT NULL,
  `tbsalacapacidad` int(11) DEFAULT NULL,
  `tbsalaactivo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbsala` (`tbsalaid`, `tbsalanombre`, `tbsalacapacidad`, `tbsalaactivo`) VALUES
(2, 'Sala de Recepción', 14, 1),
(4, 'Sala de Yoga', 25, 1),
(5, 'Sala baile', 40, 0),
(7, 'Sala para cardio', 100, 1);


ALTER TABLE `tbcertificado`
  ADD PRIMARY KEY (`tbcertificadoid`);

ALTER TABLE `tbcliente`
  ADD PRIMARY KEY (`tbclienteid`);

ALTER TABLE `tbclientepadecimiento`
  ADD PRIMARY KEY (`tbclientepadecimientoid`);

ALTER TABLE `tbcuerpozona`
  ADD PRIMARY KEY (`tbcuerpozonaid`);

ALTER TABLE `tbevento`
  ADD PRIMARY KEY (`tbeventoid`);

ALTER TABLE `tbhorario`
  ADD PRIMARY KEY (`tbhorarioid`);

ALTER TABLE `tbimagen`
  ADD PRIMARY KEY (`tbimagenid`);

ALTER TABLE `tbinstructor`
  ADD PRIMARY KEY (`tbinstructorid`);

ALTER TABLE `tbnumeroemergencia`
  ADD PRIMARY KEY (`tbnumeroemergenciaid`);

ALTER TABLE `tbpadecimiento`
  ADD PRIMARY KEY (`tbpadecimientoid`);

ALTER TABLE `tbpadecimientodictamen`
  ADD PRIMARY KEY (`tbpadecimientodictamenid`);

ALTER TABLE `tbpartezona`
  ADD PRIMARY KEY (`tbpartezonaid`);

ALTER TABLE `tbreserva`
  ADD PRIMARY KEY (`tbreservaid`);

ALTER TABLE `tbreservasala`
  ADD PRIMARY KEY (`tbreservasalaid`);

ALTER TABLE `tbsala`
  ADD PRIMARY KEY (`tbsalaid`);


ALTER TABLE `tbcertificado`
  MODIFY `tbcertificadoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `tbcliente`
  MODIFY `tbclienteid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `tbclientepadecimiento`
  MODIFY `tbclientepadecimientoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `tbevento`
  MODIFY `tbeventoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `tbnumeroemergencia`
  MODIFY `tbnumeroemergenciaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `tbpadecimientodictamen`
  MODIFY `tbpadecimientodictamenid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

ALTER TABLE `tbpartezona`
  MODIFY `tbpartezonaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `tbreserva`
  MODIFY `tbreservaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `tbreservasala`
  MODIFY `tbreservasalaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `tbsala`
  MODIFY `tbsalaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
