-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: shortline.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbcertificado`
--

DROP TABLE IF EXISTS `tbcertificado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbcertificado` (
  `tbcertificadoid` int NOT NULL AUTO_INCREMENT,
  `tbinstructorid` int DEFAULT NULL,
  `tbcertificadoimagenid` varchar(250) DEFAULT NULL,
  `tbcertificadonombre` varchar(100) DEFAULT NULL,
  `tbcertificadodescripcion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbcertificadoentidad` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`tbcertificadoid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbcertificado`
--

LOCK TABLES `tbcertificado` WRITE;
/*!40000 ALTER TABLE `tbcertificado` DISABLE KEYS */;
INSERT INTO `tbcertificado` VALUES (1,2,'26','Entrenador Personal Certificado','Certificación en entrenamiento personal y funcional.','Federación Costarricense de Fitness'),(3,1,'','Especialista en Nutrición Deportiva','Certificado en planes de nutrición para atletas.','Colegio de Nutricionistas de Costa Rica'),(5,1,'','Entrenador Personal Certificado','Entrenamiento funcional','Federación Costarricense de Fitness'),(15,3,'','n','n','n');
/*!40000 ALTER TABLE `tbcertificado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbcliente`
--

DROP TABLE IF EXISTS `tbcliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbcliente` (
  `tbclienteid` int NOT NULL AUTO_INCREMENT,
  `tbclienteimagenid` varchar(45) DEFAULT NULL,
  `tbclientecarnet` varchar(10) NOT NULL,
  `tbclientenombre` varchar(45) NOT NULL,
  `tbclientefechanacimiento` varchar(45) NOT NULL,
  `tbclientetelefono` int NOT NULL,
  `tbclientecorreo` varchar(100) DEFAULT NULL,
  `tbclientedireccion` varchar(100) NOT NULL,
  `tbclientegenero` char(1) NOT NULL,
  `tbclienteinscripcion` date NOT NULL,
  `tbclienteestado` tinyint(1) NOT NULL DEFAULT '1',
  `tbclientecontrasena` varchar(8) NOT NULL,
  PRIMARY KEY (`tbclienteid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbcliente`
--

LOCK TABLES `tbcliente` WRITE;
/*!40000 ALTER TABLE `tbcliente` DISABLE KEYS */;
INSERT INTO `tbcliente` VALUES (2,'5','402220655','Maria corrales alpizar','1999-08-08',67803080,'cliente@gmail.com','Heredia, centro','M','2025-09-01',1,'12345678'),(8,NULL,'409880755','Cristian Carrillo','1997-08-17',87654532,'cristian@gmail.com','Heredia, sarapiqui','M','2025-08-05',1,'12345678'),(9,NULL,'406650369','Jefferson Carrillo','1994-08-10',84859630,'jeffcar@gmail.com','San José','M','2025-08-14',1,'12345678'),(15,NULL,'501020201','Casandra Lascurein','2000-10-02',84582632,'cassandra@gmail.com','heredia, centro','F','2025-09-01',1,'12345678');
/*!40000 ALTER TABLE `tbcliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbclientepadecimiento`
--

DROP TABLE IF EXISTS `tbclientepadecimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbclientepadecimiento` (
  `tbclientepadecimientoid` int NOT NULL AUTO_INCREMENT,
  `tbclienteid` int DEFAULT NULL,
  `tbpadecimientoid` text,
  `tbpadecimientodictamenid` int DEFAULT NULL,
  PRIMARY KEY (`tbclientepadecimientoid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbclientepadecimiento`
--

LOCK TABLES `tbclientepadecimiento` WRITE;
/*!40000 ALTER TABLE `tbclientepadecimiento` DISABLE KEYS */;
INSERT INTO `tbclientepadecimiento` VALUES (1,2,'9$10$14',NULL),(2,9,'13$14',NULL),(6,8,NULL,NULL),(7,15,'14',57);
/*!40000 ALTER TABLE `tbclientepadecimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbcuerpozona`
--

DROP TABLE IF EXISTS `tbcuerpozona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbcuerpozona` (
  `tbcuerpozonaid` int NOT NULL,
  `tbcuerpozonapartezonaid` text,
  `tbcuerpozonaimagenesids` text,
  `tbcuerpozonanombre` varchar(100) NOT NULL,
  `tbcuerpozonadescripcion` varchar(500) NOT NULL,
  `tbcuerpozonaactivo` tinyint(1) NOT NULL,
  PRIMARY KEY (`tbcuerpozonaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbcuerpozona`
--

LOCK TABLES `tbcuerpozona` WRITE;
/*!40000 ALTER TABLE `tbcuerpozona` DISABLE KEYS */;
INSERT INTO `tbcuerpozona` VALUES (2,'1',NULL,'Pecho','Ejercicios para el desarrollo de los músculos pectorales.',0),(3,NULL,'1$4','Espalda','Ejercicios para fortalecer la espalda, incluyendo dorsales y lumbares.',1),(4,NULL,'3','Piernas','Rutinas para cuádriceps, isquiotibiales y pantorrillas.',1);
/*!40000 ALTER TABLE `tbcuerpozona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbevento`
--

DROP TABLE IF EXISTS `tbevento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbevento` (
  `tbeventoid` int NOT NULL AUTO_INCREMENT,
  `tbinstructorid` int NOT NULL,
  `tbeventonombre` varchar(255) NOT NULL,
  `tbeventodescripcion` text,
  `tbeventofecha` date NOT NULL,
  `tbeventohorainicio` time NOT NULL,
  `tbeventohorafin` time NOT NULL,
  `tbeventoaforo` int NOT NULL COMMENT 'Capacidad máxima de personas para el evento',
  `tbeventoactivo` int NOT NULL DEFAULT '1' COMMENT '1=Activo, 0=Inactivo',
  PRIMARY KEY (`tbeventoid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbevento`
--

LOCK TABLES `tbevento` WRITE;
/*!40000 ALTER TABLE `tbevento` DISABLE KEYS */;
INSERT INTO `tbevento` VALUES (1,2,'Zumba Grupal','','2025-09-25','11:00:00','13:00:00',15,1);
/*!40000 ALTER TABLE `tbevento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbhorario`
--

DROP TABLE IF EXISTS `tbhorario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbhorario` (
  `tbhorarioid` int NOT NULL,
  `tbhorariodia` varchar(25) NOT NULL,
  `tbhorarioactivo` tinyint(1) NOT NULL DEFAULT '0',
  `tbhorarioapertura` time DEFAULT NULL,
  `tbhorariocierre` time DEFAULT NULL,
  `tbhorariobloqueo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tbhorarioid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbhorario`
--

LOCK TABLES `tbhorario` WRITE;
/*!40000 ALTER TABLE `tbhorario` DISABLE KEYS */;
INSERT INTO `tbhorario` VALUES (1,'Lunes',1,'05:00:00','21:00:00','12:00:00&14:00:00'),(2,'Martes',1,'05:00:00','21:00:00','12:00:00&14:00:00'),(3,'Miércoles',1,'05:00:00','21:00:00','12:00:00&14:00:00'),(4,'Jueves',1,'05:00:00','21:00:00','12:00:00&14:00:00'),(5,'Viernes',1,'05:00:00','21:00:00','12:00:00&14:00:00'),(6,'Sábado',1,'07:00:00','13:00:00',''),(7,'Domingo',0,NULL,NULL,'');
/*!40000 ALTER TABLE `tbhorario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbhorariolibre`
--

DROP TABLE IF EXISTS `tbhorariolibre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbhorariolibre` (
  `tbhorariolibreid` int NOT NULL AUTO_INCREMENT,
  `tbhorariolibrefecha` date NOT NULL,
  `tbhorariolibrehora` time NOT NULL,
  `tbhorariolibresalaid` int NOT NULL,
  `tbhorariolibreinstructorid` varchar(100) NOT NULL,
  `tbhorariolibrecupos` int NOT NULL,
  `tbhorariolibrematriculados` int NOT NULL DEFAULT '0',
  `tbhorariolibreactivo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tbhorariolibreid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbhorariolibre`
--

LOCK TABLES `tbhorariolibre` WRITE;
/*!40000 ALTER TABLE `tbhorariolibre` DISABLE KEYS */;
INSERT INTO `tbhorariolibre` VALUES (1,'2025-09-25','07:00:00',1,'2',50,0,1),(3,'2025-09-24','10:00:00',1,'2',50,1,1),(4,'2025-09-23','07:00:00',1,'4',25,1,1),(5,'2025-09-24','08:00:00',1,'4',25,0,1);
/*!40000 ALTER TABLE `tbhorariolibre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbhorariopersonal`
--

DROP TABLE IF EXISTS `tbhorariopersonal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbhorariopersonal` (
  `tbhorariopersonalid` int NOT NULL AUTO_INCREMENT,
  `tbhorariopersonalfecha` date DEFAULT NULL,
  `tbhorariopersonalhora` time DEFAULT NULL,
  `tbinstructorid` int DEFAULT NULL,
  `tbclienteid` int DEFAULT NULL,
  `tbhorariopersonalestado` varchar(100) DEFAULT NULL,
  `tbhorariopersonalduracion` int DEFAULT NULL,
  `tbhorariopersonaltipo` varchar(100) DEFAULT NULL,
  `tbhorariopersonalfechacreacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tbhorariopersonalid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbhorariopersonal`
--

LOCK TABLES `tbhorariopersonal` WRITE;
/*!40000 ALTER TABLE `tbhorariopersonal` DISABLE KEYS */;
INSERT INTO `tbhorariopersonal` VALUES (1,'2025-09-28','08:00:00',1,2,'reservado',60,'personal',NULL);
/*!40000 ALTER TABLE `tbhorariopersonal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbimagen`
--

DROP TABLE IF EXISTS `tbimagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbimagen` (
  `tbimagenid` int NOT NULL,
  `tbimagenruta` varchar(255) NOT NULL,
  `tbimagenactivo` tinyint(1) NOT NULL,
  PRIMARY KEY (`tbimagenid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbimagen`
--

LOCK TABLES `tbimagen` WRITE;
/*!40000 ALTER TABLE `tbimagen` DISABLE KEYS */;
INSERT INTO `tbimagen` VALUES (1,'/img/cue00030001.jpg',1),(3,'/img/cue00040003.jpg',1),(4,'/img/cue00030004.jpg',1),(5,'/img/cli00020005.jpg',1),(6,'/img/ins00020006.jpg',1),(7,'/img/ins00010007.jpg',1),(10,'/img/ins00060010.jpg',1),(11,'/img/ins00050011.jpg',1),(13,'/img/cert00130013.jpg',1),(14,'/img/cert00140014.jpg',1),(15,'/img/cert00030015.jpg',1),(16,'/img/cert00030016.jpg',1),(17,'/img/cert00050017.jpg',1),(18,'/img/cert00050018.jpg',1),(19,'/img/cert00150019.jpg',1),(20,'/img/cert00050020.jpg',1),(21,'/img/pad00550021.jpg',1),(22,'/img/pad00570022.jpg',1);
/*!40000 ALTER TABLE `tbimagen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbinstructor`
--

DROP TABLE IF EXISTS `tbinstructor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbinstructor` (
  `tbinstructorid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tbinstructorimagenid` varchar(250) DEFAULT NULL,
  `tbinstructornombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbinstructortelefono` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbinstructordireccion` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbinstructorcorreo` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbinstructorcuenta` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbinstructorcontraseña` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tbinstructorcertificado` varchar(100) DEFAULT NULL,
  `tbinstructoractivo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tbinstructorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbinstructor`
--

LOCK TABLES `tbinstructor` WRITE;
/*!40000 ALTER TABLE `tbinstructor` DISABLE KEYS */;
INSERT INTO `tbinstructor` VALUES ('005','11','Noelia Fallas','60728094','Cariari, caribe','Noelia@gmail.com','Cr45272883','78834','',1),('1','','Deibis Gutierrez M','60648399','San Francisco, Heredia','deibis.gutierrez@gym.com','CR08492845843945','5454','',0),('2','6','Andrea','77777777','Cariari, caribe','instructor@gmail.com','','12345678',NULL,1),('3','','c','4444444444','d','dsads@gmail.com','','4444','',1),('4',NULL,'Anthony cubillo','80290948','Cariari, caribe','tony@gmail.com','CR458294627','12345',NULL,1);
/*!40000 ALTER TABLE `tbinstructor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbnumeroemergencia`
--

DROP TABLE IF EXISTS `tbnumeroemergencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbnumeroemergencia` (
  `tbnumeroemergenciaid` int NOT NULL AUTO_INCREMENT,
  `tbnumeroemergenciaclienteid` int NOT NULL,
  `tbnumeroemergencianombre` varchar(50) NOT NULL,
  `tbnumeroemergenciatelefono` varchar(8) NOT NULL,
  `tbnumeroemergenciarelacion` varchar(30) NOT NULL,
  PRIMARY KEY (`tbnumeroemergenciaid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbnumeroemergencia`
--

LOCK TABLES `tbnumeroemergencia` WRITE;
/*!40000 ALTER TABLE `tbnumeroemergencia` DISABLE KEYS */;
INSERT INTO `tbnumeroemergencia` VALUES (2,2,'Carlos Corrales A','78745845','Padre'),(3,2,'Juana Araya','85749547','Hermana'),(4,15,'Maria Lascurein','87256984','Hermana'),(5,8,'Karla Carrillo','87526941','Hermana');
/*!40000 ALTER TABLE `tbnumeroemergencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbpadecimiento`
--

DROP TABLE IF EXISTS `tbpadecimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbpadecimiento` (
  `tbpadecimientoid` int NOT NULL,
  `tbpadecimientotipo` varchar(200) DEFAULT NULL,
  `tbpadecimientonombre` varchar(200) DEFAULT NULL,
  `tbpadecimientodescripcion` text,
  `tbpadecimientoformadeactuar` text,
  PRIMARY KEY (`tbpadecimientoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbpadecimiento`
--

LOCK TABLES `tbpadecimiento` WRITE;
/*!40000 ALTER TABLE `tbpadecimiento` DISABLE KEYS */;
INSERT INTO `tbpadecimiento` VALUES (5,'Trastorno','Estrés post traumático','Problemas con la salud mental','Respiraciones para relajar'),(8,'Discapacidad','Parálisis de extremidades inferiores','No puede usar sus piernas','Realizar ejercicios de acuerdo a sus capacidades'),(9,'Enfermedad','Asma','Falta de aire en los pulmones','Utilizar la bomba de aire'),(10,'Enfermedad','Diabetes','Exceso de azucar en la sangre','Aplicar insulina'),(12,'Trastorno','Autismo','Afecta la forma de socializar y la comunicación','Tener paciencia y darle su espacio'),(13,'Lesión','Quebradura','Quebradura de una extremidad','No hacer ejercicios con la zona afectada'),(14,'Enfermedad','Artritis','Dolor en las articulaciones','Tomando medicamento');
/*!40000 ALTER TABLE `tbpadecimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbpadecimientodictamen`
--

DROP TABLE IF EXISTS `tbpadecimientodictamen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbpadecimientodictamen` (
  `tbpadecimientodictamenid` int NOT NULL AUTO_INCREMENT,
  `tbpadecimientodictamenfechaemision` date DEFAULT NULL,
  `tbpadecimientodictamenentidademision` varchar(100) DEFAULT NULL,
  `tbpadecimientodictamenimagenid` text,
  PRIMARY KEY (`tbpadecimientodictamenid`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbpadecimientodictamen`
--

LOCK TABLES `tbpadecimientodictamen` WRITE;
/*!40000 ALTER TABLE `tbpadecimientodictamen` DISABLE KEYS */;
INSERT INTO `tbpadecimientodictamen` VALUES (57,'2025-09-22','Clinica Puerto Viejo','22');
/*!40000 ALTER TABLE `tbpadecimientodictamen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbpartezona`
--

DROP TABLE IF EXISTS `tbpartezona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbpartezona` (
  `tbpartezonaid` int NOT NULL AUTO_INCREMENT,
  `tbpartezonaimagenid` varchar(100) DEFAULT NULL,
  `tbpartezonanombre` varchar(50) NOT NULL,
  `tbpartezonadescripcion` varchar(100) DEFAULT NULL,
  `tbpartezonaactivo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tbpartezonaid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbpartezona`
--

LOCK TABLES `tbpartezona` WRITE;
/*!40000 ALTER TABLE `tbpartezona` DISABLE KEYS */;
INSERT INTO `tbpartezona` VALUES (1,'','Pectoral','Parte frontal del pecho',1),(2,'','Superior izquierdo','Parte de arriba',1),(3,'','Baja','pechoss',1),(4,'','Medio','Parte del abdomen',1),(6,'','Pantorrilla','',1),(7,'','Pierna','',1);
/*!40000 ALTER TABLE `tbpartezona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbreservaevento`
--

DROP TABLE IF EXISTS `tbreservaevento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbreservaevento` (
  `tbreservaeventoid` int NOT NULL AUTO_INCREMENT,
  `tbreservaeventoclienteid` int NOT NULL,
  `tbreservaeventoeventoid` int NOT NULL,
  `tbreservaeventofecha` date NOT NULL,
  `tbreservaeventohorainicio` time NOT NULL,
  `tbreservaeventohorafin` time NOT NULL,
  `tbreservaeventoactivo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tbreservaeventoid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbreservaevento`
--

LOCK TABLES `tbreservaevento` WRITE;
/*!40000 ALTER TABLE `tbreservaevento` DISABLE KEYS */;
INSERT INTO `tbreservaevento` VALUES (1,2,1,'2025-09-25','11:00:00','13:00:00',1);
/*!40000 ALTER TABLE `tbreservaevento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbreservalibre`
--

DROP TABLE IF EXISTS `tbreservalibre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbreservalibre` (
  `tbreservalibreid` int NOT NULL AUTO_INCREMENT,
  `tbreservalibreclienteid` int NOT NULL,
  `tbreservalibrehorariolibreid` int NOT NULL,
  `tbreservalibreactivo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tbreservalibreid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbreservalibre`
--

LOCK TABLES `tbreservalibre` WRITE;
/*!40000 ALTER TABLE `tbreservalibre` DISABLE KEYS */;
INSERT INTO `tbreservalibre` VALUES (14,2,4,1),(16,2,3,1);
/*!40000 ALTER TABLE `tbreservalibre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbreservasala`
--

DROP TABLE IF EXISTS `tbreservasala`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbreservasala` (
  `tbreservasalaid` int NOT NULL AUTO_INCREMENT,
  `tbeventoid` int NOT NULL,
  `tbsalaid` varchar(255) NOT NULL,
  `tbreservafecha` date NOT NULL,
  `tbreservahorainicio` time NOT NULL,
  `tbreservahorafin` time NOT NULL,
  PRIMARY KEY (`tbreservasalaid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbreservasala`
--

LOCK TABLES `tbreservasala` WRITE;
/*!40000 ALTER TABLE `tbreservasala` DISABLE KEYS */;
INSERT INTO `tbreservasala` VALUES (1,1,'2$5','2025-09-25','11:00:00','13:00:00');
/*!40000 ALTER TABLE `tbreservasala` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbsala`
--

DROP TABLE IF EXISTS `tbsala`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbsala` (
  `tbsalaid` int NOT NULL AUTO_INCREMENT,
  `tbsalanombre` varchar(150) DEFAULT NULL,
  `tbsalacapacidad` int DEFAULT NULL,
  `tbsalaestado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tbsalaid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbsala`
--

LOCK TABLES `tbsala` WRITE;
/*!40000 ALTER TABLE `tbsala` DISABLE KEYS */;
INSERT INTO `tbsala` VALUES (1,'Sala Principal',100,1),(2,'Sala de Recepción',14,1),(4,'Sala de Yoga',25,1),(5,'Sala baile',40,0),(7,'Sala para cardio',100,1);
/*!40000 ALTER TABLE `tbsala` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-30 19:28:24
