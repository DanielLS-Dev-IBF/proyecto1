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
-- Table structure for table `Pedidos`
--

DROP TABLE IF EXISTS `Pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pedidos` (
  `id_pedido` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `detalles_pago` varchar(255) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT '0.00',
  `gastos_envio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_pedido` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pedido`)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pedidos`
--

LOCK TABLES `Pedidos` WRITE;
/*!40000 ALTER TABLE `Pedidos` DISABLE KEYS */;
INSERT INTO `Pedidos` VALUES (22,3,'test','test','111111111','test@gmail.com','PayPal','Cuenta de PayPal: test@gmail.com',5.00,0.00,5.00,10.00,'2024-12-22 17:02:10'),(23,3,'test','test','111111111','test@gmail.com','PayPal','Cuenta de PayPal: test@gmail.com',7.50,0.00,5.00,12.50,'2024-12-22 17:04:15'),(29,3,'','test','111111111','test@gmail.com','PayPal','',8.99,0.00,5.00,13.99,'2025-01-02 19:44:35'),(31,1,'','test','666666666','admin@gmail.com','PayPal','',42.75,0.00,5.00,47.75,'2025-01-02 23:47:46'),(33,1,'','test','666666666','admin@gmail.com','PayPal','',46.25,0.00,5.00,51.25,'2025-01-03 00:41:48'),(34,1,'admin','test2','666666666','admin@gmail.com','PayPal','Cuenta de PayPal: admin@gmail.com',37.00,0.00,5.00,42.00,'2025-01-03 13:10:05'),(35,1,'','test2','666666666','admin@gmail.com','PayPal','',117.00,0.00,0.00,117.00,'2025-01-03 13:13:58'),(36,1,'','test2','666666666','admin@gmail.com','PayPal','Cuenta de PayPal: admin@gmail.com',24.50,0.00,5.00,29.50,'2025-01-03 13:26:00'),(37,3,'test','test','111111111','test@gmail.com','PayPal','Cuenta de PayPal: admin@gmail.com',66.49,6.65,0.00,59.84,'2025-01-03 13:29:46'),(38,1,'admin','test','666666666','admin@gmail.com','PayPal','Cuenta de PayPal: admin@gmail.com',43.99,0.00,5.00,48.99,'2025-01-04 16:32:30'),(39,1,'admin','test','666666666','admin@gmail.com','PayPal','Cuenta de PayPal: admin@gmail.com',22.00,0.00,5.00,27.00,'2025-01-04 17:08:08');
/*!40000 ALTER TABLE `Pedidos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-04 18:24:42
