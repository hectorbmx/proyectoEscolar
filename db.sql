-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla blog.articles
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(180) NOT NULL,
  `body` mediumtext NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_articles_published_at` (`published_at`),
  KEY `idx_articles_status` (`status`),
  KEY `idx_articles_author` (`author_id`),
  CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla blog.articles: ~5 rows (aproximadamente)
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` (`id`, `author_id`, `title`, `body`, `published_at`, `cover_image`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Bienvenido a mi Blog', 'Este es el primer artículo de prueba.', '2025-09-23 12:59:58', 'storage/uploads/b305725139a04bd5.jpg', 'published', '2025-09-23 12:59:58', '2025-09-23 13:30:57'),
	(2, 1, 'Artículo con Imagen', 'Contenido del artículo con una portada.', '2025-09-23 12:59:58', 'storage/uploads/0206d9e33237e243.jpeg', 'published', '2025-09-23 12:59:58', '2025-09-23 13:31:43'),
	(6, 1, 'CICLISMO RUTA', 'EL CICLISMO DE RUTA ES MUY BUENO', '2025-09-23 21:35:53', 'storage/uploads/3df2aea2299e529e.png', 'published', '2025-09-23 13:35:53', '2025-09-23 13:38:33'),
	(7, 1, 'VELODROMO', 'EL VELODROMO ES UNO DE LOS DEPORTES MAS EMOCIONANTES', '2025-09-23 21:36:10', 'storage/uploads/26b39ea2551ca0ea.png', 'published', '2025-09-23 13:36:10', NULL),
	(8, 1, 'FUT BOL', 'EL FUT BALL ES EL DEPORTE MAS PRACTICADO EN EL MUNDO', '2025-09-23 21:36:37', 'storage/uploads/a8bd05fb1d226b02.png', 'published', '2025-09-23 13:36:37', NULL),
	(9, 1, 'crossfit', 'el crossfit es un deporte muy completo', '2025-09-23 21:37:08', 'storage/uploads/b8b391f59255b37d.png', 'published', '2025-09-23 13:37:08', NULL),
	(10, 1, 'Guía básica de música jazz', 'Qué es el swing, cómo funciona la improvisación y tres discos para iniciar: Kind of Blue, Time Out y A Love Supreme. Consejos para escuchar activamente.', '2025-09-23 13:40:04', 'storage/uploads/b3922f4b15ecddc4.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:41:38'),
	(11, 1, 'HIIT en 20 minutos', 'Entrenamiento por intervalos de alta intensidad para mejorar resistencia y quemar calorías. Estructura simple con calentamiento, bloques de esfuerzo y vuelta a la calma.', '2025-09-22 13:40:04', 'storage/uploads/98ccd3e6f5169efc.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:42:16'),
	(12, 1, 'Tacos regionales de México', 'Resumen de estilos: pastor, barbacoa, cochinita y birria. Salsas que combinan y tips para calentar tortillas sin que se rompan.', '2025-09-21 13:40:04', 'storage/uploads/e57edabd2c141542.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:42:57'),
	(13, 1, 'Dormir mejor: hábitos clave', 'Rutina nocturna, control de luz azul, cafeína y siestas. Cómo medir la calidad del sueño y cuándo consultar con un especialista.', '2025-09-20 13:40:04', 'storage/uploads/ea1482d83bb330c0.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:43:32'),
	(14, 1, 'Atajos de teclado para productividad', 'Combinaciones útiles en Windows y macOS para acelerar tareas. Cómo crear atajos propios y buenas prácticas de foco.', '2025-09-19 13:40:04', 'storage/uploads/f698f5c60e76363e.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:44:13'),
	(15, 1, 'Clásicos del cine para empezar', 'Cinco películas esenciales de distintas décadas y por qué siguen vigentes. Sugerencias para analizarlas en casa.', '2025-09-18 13:40:04', 'storage/uploads/3249b60ca00b93f6.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:44:48'),
	(16, 1, 'Escapada de fin de semana', 'Ideas para un viaje corto: presupuesto, mochila ligera, apps útiles y cómo buscar hospedaje bien ubicado.', '2025-09-17 13:40:04', 'storage/uploads/7063673f670c82fe.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:45:35'),
	(17, 1, 'Fotografía móvil: trucos rápidos', 'Regla de tercios, luz natural, limpieza de lente y edición básica. Cómo evitar el ruido en interiores.', '2025-09-16 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:20'),
	(18, 1, 'Guía del café de especialidad', 'Métodos de extracción, molienda y proporciones. Diferencia entre origen único y mezclas. Errores comunes.', '2025-09-15 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:21'),
	(19, 1, 'Correr tus primeros 5K', 'Plan progresivo de cuatro semanas, estiramientos, elección de tenis y cómo evitar lesiones frecuentes.', '2025-09-14 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:23'),
	(20, 1, 'Táctica 4-3-3 en fútbol', 'Funciones por línea, ventajas en presión alta y transiciones. Ejercicios simples para entrenar en cancha.', '2025-09-13 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:25'),
	(21, 1, 'Pan artesanal en casa', 'Autólisis, amasado y fermentación en frío. Cómo usar horno doméstico y lograr corteza crujiente.', '2025-09-12 13:40:04', 'storage/uploads/4d8a1bd42f857fa3.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:46:15'),
	(22, 1, 'Regla 50 30 20 para finanzas', 'Distribuye ingresos entre necesidades, gustos y ahorro. Herramientas sencillas para registrar gastos.', '2025-09-11 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:28'),
	(23, 1, 'Proteínas vegetales en la dieta', 'Fuentes principales, combinaciones de aminoácidos y ejemplo de menú equilibrado para un día.', '2025-09-10 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:29'),
	(24, 1, 'Mindfulness para principiantes', 'Prácticas de respiración, escaneo corporal y atención plena. Beneficios y recomendaciones de constancia.', '2025-09-09 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:30'),
	(25, 1, 'Contraseñas seguras y 2FA', 'Gestores de contraseñas, autenticación en dos pasos y cómo reconocer intentos de phishing.', '2025-09-08 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:31'),
	(26, 1, 'Aprender francés en 30 días', 'Plan diario con vocabulario base, pronunciación y recursos gratis. Cómo aprovechar repetición espaciada.', '2025-09-07 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:32'),
	(27, 1, 'PC para gaming económica', 'Componentes clave, relación costo rendimiento y rutas de actualización. Consejos de ventilación.', '2025-09-06 13:40:04', 'storage/uploads/5dd2720cc7927db9.png', 'published', '2025-09-23 13:40:04', '2025-09-23 13:47:13'),
	(28, 1, 'Senderismo: equipo esencial', 'Calzado, capas de ropa, hidratación y orientación básica. Reglas de dejar sin rastro.', '2025-09-05 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:36'),
	(29, 1, 'Salsas picantes caseras', 'Equilibrio entre picor y sabor. Técnicas para asar, moler y conservar. Tres recetas base para empezar.', '2025-09-04 13:40:04', NULL, 'published', '2025-09-23 13:40:04', '2025-09-23 13:40:38');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;

-- Volcando estructura para tabla blog.authors
CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla blog.authors: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` (`id`, `name`, `email`, `created_at`) VALUES
	(1, 'Admin Demo', 'admin@example.com', '2025-09-23 12:59:58');
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;

-- Volcando estructura para tabla blog.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla blog.users: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
	(1, 'Admin', 'admin@example.com', '$2y$10$39O/uK8MytRXi5JSwEVRUuot4c8rN.lJxcTaJ0eanPCxaPPBIzl4a', 'admin', '2025-09-23 13:15:45');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
