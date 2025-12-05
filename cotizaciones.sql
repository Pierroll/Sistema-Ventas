-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40101 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura de base de datos para venta
CREATE DATABASE IF NOT EXISTS `venta` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `venta`;

-- Volcando estructura para tabla venta.abonos
CREATE TABLE IF NOT EXISTS `abonos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `abono` decimal(10,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_credito` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.abonos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.apartados
CREATE TABLE IF NOT EXISTS `apartados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha_apartado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_retiro` datetime NOT NULL,
  `abono` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `color` varchar(15) NOT NULL DEFAULT '#ffa426',
  `estado` int NOT NULL DEFAULT '1',
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.apartados: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.caja
CREATE TABLE IF NOT EXISTS `caja` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caja` varchar(50) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.caja: ~0 rows (aproximadamente)
INSERT INTO `caja` (`id`, `caja`, `fecha`, `estado`) VALUES
	(1, 'GENERAL', '2022-05-28 16:18:55', 1);

-- Volcando estructura para tabla venta.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.categorias: ~3 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nombre`, `fecha`, `estado`) VALUES
	(1, 'ACCESORIOS', '2022-10-11 18:59:22', 1),
	(2, 'REPUESTOS', '2022-10-11 18:59:22', 1),
	(3, 'LUBRICANTES', '2022-10-11 18:59:22', 1);

-- Volcando estructura para tabla venta.cierre_caja
CREATE TABLE IF NOT EXISTS `cierre_caja` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `fecha_apertura` date NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `monto_final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_ventas` int NOT NULL DEFAULT '0',
  `monto_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `cierre_caja_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.cierre_caja: ~0 rows (aproximadamente)
INSERT INTO `cierre_caja` (`id`, `id_usuario`, `monto_inicial`, `fecha_apertura`, `fecha_cierre`, `monto_final`, `total_ventas`, `monto_total`, `estado`) VALUES
	(1, 1, 500.00, '2022-10-11', NULL, 0.00, 0, 0.00, 1);

-- Volcando estructura para tabla venta.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.clientes: ~0 rows (aproximadamente)
INSERT INTO `clientes` (`id`, `dni`, `nombre`, `telefono`, `direccion`, `fecha`, `estado`) VALUES
	(1, '334432423', 'NOMBRE DEL CLIENTE', '676768867', 'LIMA PERU', '2022-10-28 20:28:01', 1),
	(2, '12345678', 'Cliente Test', '987654321', 'DIRECCION TEST', '2023-01-01 00:00:00', 1);

-- Volcando estructura para tabla venta.compras
CREATE TABLE IF NOT EXISTS `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_proveedor` int NOT NULL,
  `id_usuario` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `serie` int NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `metodo` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_proveedor` (`id_proveedor`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.compras: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `logo` varchar(10) NOT NULL,
  `moneda` int NOT NULL,
  `impuesto` int NOT NULL,
  `cant_factura` int NOT NULL DEFAULT '0',
  `site` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moneda` (`moneda`),
  CONSTRAINT `configuracion_ibfk_1` FOREIGN KEY (`moneda`) REFERENCES `moneda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.configuracion: ~1 rows (aproximadamente)
INSERT INTO `configuracion` (`id`, `ruc`, `nombre`, `telefono`, `correo`, `direccion`, `mensaje`, `logo`, `moneda`, `impuesto`, `cant_factura`, `site`) VALUES
	(1, '6456665478', 'sistema de venta', '900897537', 'admin@gmail.com.com', 'LIMA - PERÚ - Lorem ipsum dolor sit amet.', '<p>GRACIAS POR LA PREFERENCIAs</p>', 'logo.png', 1, 18, 1000, 'tuweb.com');

-- Volcando estructura para tabla venta.cotizaciones
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `productos` longtext NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `validez` varchar(50) NOT NULL DEFAULT '',
  `comentario` longtext,
  `id_cliente` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.cotizaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.creditos
CREATE TABLE IF NOT EXISTS `creditos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `monto` decimal(10,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  `id_venta` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.creditos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.detalle
CREATE TABLE IF NOT EXISTS `detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `detalle_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.detalle: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.detalle_apartados
CREATE TABLE IF NOT EXISTS `detalle_apartados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `id_producto` int NOT NULL,
  `id_apartado` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.detalle_apartados: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.detalle_compras
CREATE TABLE IF NOT EXISTS `detalle_compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_compra` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_compra` (`id_compra`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.detalle_compras: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.detalle_permisos
CREATE TABLE IF NOT EXISTS `detalle_permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_permiso` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_permiso` (`id_permiso`),
  CONSTRAINT `detalle_permisos_ibfk_1` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_permisos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.detalle_permisos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.detalle_temp
CREATE TABLE IF NOT EXISTS `detalle_temp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `detalle_temp_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.detalle_temp: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.detalle_ventas
CREATE TABLE IF NOT EXISTS `detalle_ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.detalle_ventas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.inventario
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  `entradas` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha` date NOT NULL,
  `salidas` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.inventario: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.landing
CREATE TABLE IF NOT EXISTS `landing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hora_registro` time NOT NULL,
  `fecha_registro` date NOT NULL,
  `pagina` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `telefono` varchar(14) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `correo` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `negocio` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `control` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- Volcando datos para la tabla venta.landing: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.medidas
CREATE TABLE IF NOT EXISTS `medidas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `nombre_corto` varchar(5) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.medidas: ~2 rows (aproximadamente)
INSERT INTO `medidas` (`id`, `nombre`, `nombre_corto`, `fecha`, `estado`) VALUES
	(1, 'UNIDAD', 'UND', '2022-10-11 18:59:40', 1),
	(2, 'SET', 'SET', '2022-10-11 18:59:40', 1);

-- Volcando estructura para tabla venta.moneda
CREATE TABLE IF NOT EXISTS `moneda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `simbolo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.moneda: ~0 rows (aproximadamente)
INSERT INTO `moneda` (`id`, `simbolo`, `nombre`, `fecha`, `estado`) VALUES
	(1, 'S/', 'NUEVO SOLES', '2022-05-28 16:18:27', 1);

-- Volcando estructura para tabla venta.permisos
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `permiso` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.permisos: ~45 rows (aproximadamente)
INSERT INTO `permisos` (`id`, `permiso`) VALUES
	(1, 'crear_usuario'),
	(2, 'modificar_usuario'),
	(3, 'eliminar_usuario'),
	(4, 'restaurar_usuario'),
	(5, 'configuracion'),
	(6, 'crear_moneda'),
	(7, 'modificar_moneda'),
	(8, 'eliminar_moneda'),
	(9, 'crear_caja'),
	(10, 'modificar_caja'),
	(11, 'eliminar_caja'),
	(12, 'restaurar_caja'),
	(13, 'crear_cliente'),
	(14, 'modificar_cliente'),
	(15, 'eliminar_cliente'),
	(16, 'restaurar_cliente'),
	(17, 'crear_proveedor'),
	(18, 'modificar_proveedor'),
	(19, 'eliminar_proveedor'),
	(20, 'restaurar_proveedor'),
	(21, 'inventario'),
	(22, 'crear_medida'),
	(23, 'modificar_medida'),
	(24, 'eliminar_medida'),
	(25, 'restaurar_medida'),
	(26, 'crear_categoria'),
	(27, 'modificar_categoria'),
	(28, 'eliminar_categoria'),
	(29, 'restaurar_categoria'),
	(30, 'crear_producto'),
	(31, 'modificar_producto'),
	(32, 'eliminar_producto'),
	(33, 'restaurar_producto'),
	(34, 'nueva_compra'),
	(35, 'anular_compra'),
	(36, 'reporte_compras'),
	(37, 'nueva_venta'),
	(38, 'anular_venta'),
	(39, 'reporte_ventas'),
	(40, 'reporte_pdf_inventario'),
	(41, 'reporte_pdf_compras'),
	(42, 'reporte_pdf_ventas'),
	(43, 'abrir_caja'),
	(44, 'cerrar_caja'),
	(46, 'landing'),
	(47, 'cotizaciones');

-- Volcando estructura para tabla venta.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_medida` int NOT NULL,
  `id_categoria` int NOT NULL,
  `foto` varchar(50) NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_medida` (`id_medida`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_medida`) REFERENCES `medidas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.productos: ~2 rows (aproximadamente)
INSERT INTO `productos` (`id`, `codigo`, `descripcion`, `precio_compra`, `precio_venta`, `cantidad`, `id_medida`, `id_categoria`, `foto`, `estado`, `fecha`) VALUES
	(1, '133444343', 'PRUEBA DEL PRODUCTO', 150.00, 160.00, 0.00, 1, 1, 'default.png', 1, '2022-10-28 20:18:19'),
	(2, '56563564', 'OTRO PRODCTO', 123.00, 170.00, 0.00, 1, 1, 'default.png', 1, '2022-10-28 20:23:19'),
	(3, '65454564', 'PRODEUCTO CON IGV 18', 300.00, 350.00, 0.00, 1, 1, 'default.png', 1, '2022-10-29 03:06:04');

-- Volcando estructura para tabla venta.proveedor
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.proveedor: ~0 rows (aproximadamente)
INSERT INTO `proveedor` (`id`, `ruc`, `nombre`, `telefono`, `direccion`, `estado`) VALUES
	(1, '9877989633', 'OPEN SERVICES', '965874125', 'PERU - ANCASH', 1);

-- Volcando estructura para tabla venta.temp_apartados
CREATE TABLE IF NOT EXISTS `temp_apartados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.temp_apartados: ~0 rows (aproximadamente)

-- Volcando estructura para tabla venta.temp_cotizaciones
CREATE TABLE IF NOT EXISTS `temp_cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cantidad` int NOT NULL,
  `medida` varchar(50) NOT NULL DEFAULT 'UND',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto` int NOT NULL DEFAULT '0',
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.temp_cotizaciones: ~1 rows (aproximadamente)

-- Volcando estructura para tabla venta.terminos
CREATE TABLE IF NOT EXISTS `terminos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.terminos: ~6 rows (aproximadamente)
INSERT INTO `terminos` (`id`, `titulo`, `descripcion`) VALUES
	(1, 'NUESTROS COSTOS', 'EXPRESADOS EN SOLES, PRECIOS UNITARIOS NO INCLUYEN IGV'),
	(2, 'FORMA DE PAGO', 'AL CONTADO Y/O'),
	(3, 'TIEMPO DE ENTREGA', ': DIAS HÁBILES DESPUES DE LA OC Y DEPOSITO EN CUENTA'),
	(4, ' LUGAR DE ENTREGA', 'A'),
	(5, ' DURACIÓN DEL HDPE', 'HASTA 20 AÑOS COMO MATERIAL'),
	(6, 'GARANTÍA', 'HASTA 36 MESES POR');

-- Volcando estructura para tabla venta.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `correo` varchar(80) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `perfil` varchar(50) NOT NULL DEFAULT 'avatar.svg',
  `clave` varchar(100) NOT NULL,
  `id_caja` int NOT NULL,
  `token` varchar(50) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_caja` (`id_caja`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.usuarios: ~0 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo`, `telefono`, `direccion`, `perfil`, `clave`, `id_caja`, `token`, `fecha`, `estado`) VALUES
	(1, 'SUPER ADMINISTRADOR', NULL, 'admin@agmail.com', NULL, NULL, 'avatar.svg', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 1, NULL, '2022-05-28 16:29:38', 1),
	(2, 'VENDEDOR', NULL, 'vendedor@agmail.com', NULL, NULL, 'avatar.svg', 'f3c2b74c8f3c12759451c6a5545c10847c7046d6b13c843953b4647055b05d54', 1, NULL, '2022-05-28 16:29:38', 1);

-- Volcando estructura para tabla venta.ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_cliente` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `apertura` int NOT NULL DEFAULT '1',
  `serie` int NOT NULL DEFAULT '1',
  `metodo` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla venta.ventas: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;