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
-- Table structure for table `Producto`
--

DROP TABLE IF EXISTS `Producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Producto` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `id_producto_UNIQUE` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Producto`
--

LOCK TABLES `Producto` WRITE;
/*!40000 ALTER TABLE `Producto` DISABLE KEYS */;
INSERT INTO `Producto` VALUES (6,'Smoothie Verde Vital','Batido energético con espinacas, plátano, manzana y un toque de jengibre.','img\\Productos\\Smoothie Verde Vital.webp\n',5.99,'Bebida'),(7,'Agua de Coco Natural','Refrescante agua de coco 100% natural, rica en electrolitos y minerales.','img\\Productos\\Agua de Coco Natural.webp\n',3.50,'Bebida'),(8,'Té Matcha Latte','Latte de matcha preparado con leche de almendras, ideal para energía sostenida.','img\\Productos\\Té Matcha Latte.webp\n',4.50,'Bebida'),(9,'Jugo Detox de Remolacha','Jugo revitalizante con remolacha, zanahoria, manzana y limón.','img\\Productos\\Jugo Detox de Remolacha.webp\n',4.25,'Bebida'),(10,'Limonada de Aloe Vera','Limonada casera con gel de aloe vera, perfecta para la digestión.','img\\Productos\\Limonada de Aloe Vera.webp\n',3.75,'Bebida'),(11,'Batido de Proteína de Arándanos','Batido nutritivo con arándanos, proteína de guisante y leche de avena.','img\\Productos\\Batido de Proteína de Arándanos.webp\n',6.50,'Bebida'),(12,'Infusión de Hibisco y Frutas','Infusión fría de hibisco con trozos de frutas frescas, sin azúcar añadido.','img\\Productos\\Infusión de Hibisco y Frutas.webp\n',4.00,'Bebida'),(13,'Agua de Pepino y Menta','Agua infusionada con pepino fresco y hojas de menta, ultra hidratante.','img\\Productos\\Agua de Pepino y Menta.webp\n',3.25,'Bebida'),(14,'Smoothie de Mango y Chía','Smoothie tropical con mango maduro, semillas de chía y leche de coco.','img\\Productos\\Smoothie de Mango y Chía.webp\n',5.75,'Bebida'),(15,'Parfait de Yogur y Frutas','Capas de yogur griego natural, granola casera y una variedad de frutas frescas de temporada.','img\\Productos\\Parfait de Yogur y Frutas.webp\n',4.99,'Postre'),(16,'Mousse de Aguacate y Cacao','Cremosa mousse hecha con aguacate, cacao puro y endulzada con miel natural.','img\\Productos\\Mousse de Aguacate y Cacao.webp\n',5.50,'Postre'),(17,'Barra Energética de Dátiles','Barras caseras de dátiles, nueces, avena y semillas, perfectas para un snack dulce y nutritivo.','img\\Productos\\Barra Energética de Dátiles.webp\n',3.75,'Postre'),(18,'Tarta de Manzana Integral','Tarta de manzana elaborada con harina integral, endulzada con sirope de arce y especias naturales.','img\\Productos\\Tarta de Manzana Integral.webp\n',6.25,'Postre'),(19,'Helado de Plátano y Fresas','Helado vegano hecho con plátanos congelados y fresas, sin azúcares añadidos.','img\\Productos\\Helado de Plátano y Fresas.webp\n',4.50,'Postre'),(20,'Chía Pudding de Coco','Pudín de semillas de chía remojadas en leche de coco, servido con trozos de mango fresco.','img\\Productos\\Chía Pudding de Coco.webp\n',3.99,'Postre'),(21,'Brownie de Harina de Almendra','Brownie saludable hecho con harina de almendra, cacao oscuro y endulzado con stevia.','img\\Productos\\Brownie de Harina de Almendra.webp\n',5.00,'Postre'),(22,'Fruta Asada con Canela','Selección de frutas asadas como piña, durazno y pera, espolvoreadas con canela natural.','img\\Productos\\Fruta Asada con Canela.webp\n',3.50,'Postre'),(23,'Trufas de Cacao y Almendra','Trufas hechas con cacao puro, almendras trituradas y un toque de vainilla natural.','img\\Productos\\Trufas de Cacao y Almendra.webp\n',4.25,'Postre'),(24,'Bowl de Quinoa y Verduras','Delicioso bowl con quinoa integral, brócoli al vapor, zanahorias ralladas y aderezo de tahini.','img\\Productos\\Bowl de Quinoa y Verduras.webp',8.99,'Bowl'),(25,'Bowl de Acai y Frutas','Refrescante bowl de acai servido con granola casera, fresas, plátano y semillas de chía.','img\\Productos\\Bowl de Acai y Frutas.webp\n',7.50,'Bowl'),(26,'Bowl de Pollo al Curry','Suculento pollo al curry con arroz integral, espinacas, pimientos y una salsa cremosa de coco.','img\\Productos\\Bowl de Pollo al Curry.webp\n',9.25,'Bowl'),(27,'Bowl Mediterráneo de Falafel','Bowl lleno de falafel casero, hummus, pepino, tomate, aceitunas y pan pita integral.','img\\Productos\\Bowl Mediterráneo de Falafel.webp\n',8.50,'Bowl'),(28,'Bowl de Salmón y Aguacate','Fresco salmón a la parrilla con aguacate, arroz de coliflor, edamames y salsa de soja baja en sodio.','img\\Productos\\Bowl de Salmón y Aguacate.webp\n',10.00,'Bowl'),(29,'Bowl Vegano de Tofu y Vegetales','Tofu marinado al grill con quinoa, kale, zanahorias, edamames y aderezo de sésamo.','img\\Productos\\Bowl Vegano de Tofu y Vegetales.webp\n',9.00,'Bowl'),(30,'Bowl de Burrito de Arroz Integral','Arroz integral, frijoles negros, maíz, pico de gallo, aguacate y queso vegano en un bowl estilo burrito.','img\\Productos\\Bowl de Burrito de Arroz Integral.webp\n',8.75,'Bowl'),(31,'Bowl de Buda con Hummus','Hummus cremoso sobre una cama de espinacas, quinoa, garbanzos, aguacate y vegetales asados.','img\\Productos\\Bowl de Buda con Hummus.webp\n',7.99,'Bowl'),(32,'Bowl de Poke de Atún','Atún fresco marinado con salsa de soja, servido sobre arroz de sushi, aguacate, pepino y algas.','img\\Productos\\Bowl de Poke de Atún.webp\n',11.50,'Bowl');
/*!40000 ALTER TABLE `Producto` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-04 19:58:06
