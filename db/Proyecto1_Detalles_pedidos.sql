-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: Proyecto1
-- ------------------------------------------------------
-- Server version	9.0.1

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
-- Table structure for table `Detalles_pedidos`
--

DROP TABLE IF EXISTS `Detalles_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Detalles_pedidos` (
  `id_detalle_pedido` int NOT NULL AUTO_INCREMENT,
  `id_pedido` int NOT NULL,
  `id_producto` int NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `total_producto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle_pedido`),
  KEY `id_pedido` (`id_pedido`),
  CONSTRAINT `Detalles_pedidos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `Pedidos` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Detalles_pedidos`
--

LOCK TABLES `Detalles_pedidos` WRITE;
/*!40000 ALTER TABLE `Detalles_pedidos` DISABLE KEYS */;
INSERT INTO `Detalles_pedidos` VALUES (29,22,21,'Brownie de Harina de Almendra',5.00,1,5.00),(30,23,25,'Bowl de Acai y Frutas',7.50,1,7.50),(38,29,20,'Chía Pudding de Coco',3.99,1,3.99),(42,31,25,'Bowl de Acai y Frutas',7.50,2,15.00),(45,33,26,'Bowl de Pollo al Curry',9.25,5,46.25),(48,31,26,'Bowl de Pollo al Curry',9.25,3,27.75),(49,29,21,'Brownie de Harina de Almendra',5.00,1,5.00),(50,34,26,'Bowl de Pollo al Curry',9.25,4,37.00),(51,35,26,'Bowl de Pollo al Curry',9.25,10,92.50),(52,35,9,'Jugo Detox de Remolacha',4.25,4,17.00),(53,35,17,'Barra Energética de Dátiles',3.75,2,7.50),(55,36,9,'Jugo Detox de Remolacha',4.25,4,17.00),(56,36,17,'Barra Energética de Dátiles',3.75,2,7.50),(57,37,20,'Chía Pudding de Coco',3.99,1,3.99),(58,37,21,'Brownie de Harina de Almendra',5.00,1,5.00),(59,37,32,'Bowl de Poke de Atún',11.50,5,57.50),(60,38,9,'Jugo Detox de Remolacha',4.25,4,17.00),(61,38,17,'Barra Energética de Dátiles',3.75,2,7.50),(62,38,31,'Bowl de Buda con Hummus',7.99,1,7.99),(63,38,32,'Bowl de Poke de Atún',11.50,1,11.50),(64,39,26,'Bowl de Pollo al Curry',9.25,2,18.50),(65,39,7,'Agua de Coco Natural',3.50,1,3.50);
/*!40000 ALTER TABLE `Detalles_pedidos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-04 19:58:07
