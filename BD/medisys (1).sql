-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-03-2026 a las 23:28:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `medisys`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_auditoria` ()   BEGIN

DECLARE done INT DEFAULT FALSE;
DECLARE v_table VARCHAR(255);

DECLARE cur CURSOR FOR
SELECT table_name
FROM information_schema.tables
WHERE table_schema = DATABASE()
AND table_type = 'BASE TABLE'
AND table_name <> 'auditoria';

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

OPEN cur;

read_loop: LOOP

FETCH cur INTO v_table;

IF done THEN
LEAVE read_loop;
END IF;

SET @trigger_insert = CONCAT('
CREATE TRIGGER ', v_table, '_ai AFTER INSERT ON ', v_table, '
FOR EACH ROW
INSERT INTO auditoria
(usuario_mysql, tabla, operacion, datos_new)
VALUES
(
CURRENT_USER(),
"', v_table, '",
"INSERT",
CONCAT_WS("|", ', generar_concat_campos(v_table, 'NEW'), ')
);
');

SET @trigger_update = CONCAT('
CREATE TRIGGER ', v_table, '_au AFTER UPDATE ON ', v_table, '
FOR EACH ROW
INSERT INTO auditoria
(usuario_mysql, tabla, operacion, datos_old, datos_new)
VALUES
(
CURRENT_USER(),
"', v_table, '",
"UPDATE",
CONCAT_WS("|", ', generar_concat_campos(v_table, 'OLD'), '),
CONCAT_WS("|", ', generar_concat_campos(v_table, 'NEW'), ')
);
');

SET @trigger_delete = CONCAT('
CREATE TRIGGER ', v_table, '_ad AFTER DELETE ON ', v_table, '
FOR EACH ROW
INSERT INTO auditoria
(usuario_mysql, tabla, operacion, datos_old)
VALUES
(
CURRENT_USER(),
"', v_table, '",
"DELETE",
CONCAT_WS("|", ', generar_concat_campos(v_table, 'OLD'), ')
);
');

SET @s = @trigger_insert;
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = @trigger_update;
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = @trigger_delete;
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

END LOOP;

CLOSE cur;

END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `generar_concat_campos` (`p_tabla` VARCHAR(255), `p_prefijo` VARCHAR(10)) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci READS SQL DATA BEGIN

DECLARE resultado TEXT;

SELECT GROUP_CONCAT(
CONCAT(
'"', column_name, '=",' ,
p_prefijo, '.', column_name
)
SEPARATOR ','
)
INTO resultado
FROM information_schema.columns
WHERE table_schema = DATABASE()
AND table_name = p_tabla;

RETURN resultado;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_mysql` varchar(100) DEFAULT NULL,
  `tabla` varchar(100) DEFAULT NULL,
  `operacion` enum('INSERT','UPDATE','DELETE') DEFAULT NULL,
  `datos_old` longtext DEFAULT NULL,
  `datos_new` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(10) UNSIGNED NOT NULL,
  `denominacion` varchar(191) NOT NULL,
  `patente` varchar(15) NOT NULL,
  `tarifa_id` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `direccion` varchar(191) DEFAULT NULL,
  `telefonos` varchar(191) DEFAULT NULL,
  `localidad` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `denominacion`, `patente`, `tarifa_id`, `fecha_inicio`, `fecha_fin`, `activo`, `direccion`, `telefonos`, `localidad`, `created_at`, `updated_at`) VALUES
(733, 'Usuario Prueba', 'JHO9911111', 9, '2026-02-28', '2026-02-28', 1, 'Bruno 740', '02236065018', 'MAR DEL PLATA', NULL, NULL),
(734, 'Usuario Prueba', 'AAABBCC4522', 7, '2026-02-28', '2026-03-29', 1, 'Bruno 740', '02236065018', 'Sierra de los Padres', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modules`
--

INSERT INTO `modules` (`id`, `modulo`, `nombre`, `orden`, `activo`) VALUES
(1, 'vehiculos', 'vehiculos', 1, 1),
(2, 'clients', 'clients', 2, 1),
(3, 'user', 'user', 3, 1),
(4, 'simulador', 'simulador', 4, 1),
(5, 'cobranzas', 'cobranzas', 5, 1),
(6, 'tarifas', 'tarifas', 6, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profile_modules`
--

CREATE TABLE `profile_modules` (
  `id` int(11) NOT NULL,
  `perfil` varchar(50) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `puede_ver` tinyint(1) DEFAULT 0,
  `puede_acceder` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profile_modules`
--

INSERT INTO `profile_modules` (`id`, `perfil`, `modulo`, `puede_ver`, `puede_acceder`) VALUES
(1, 'Operador', 'vehiculos', 1, 1),
(2, 'Operador', 'clients', 1, 1),
(3, 'Operador', 'user', 0, 0),
(4, 'Operador', 'cobranzas', 0, 0),
(5, 'Operador', 'simulador', 1, 1),
(6, 'Super Admin', 'vehiculos', 1, 1),
(7, 'Super Admin', 'clients', 1, 1),
(8, 'Super Admin', 'user', 1, 1),
(9, 'Super Admin', 'simulador', 1, 1),
(10, 'Super Admin', 'cobranzas', 0, 0),
(13, 'Super Admin', 'tarifas', 1, 1),
(14, 'Operador', 'tarifas', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas`
--

CREATE TABLE `tarifas` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `unidad` enum('minutos','horas','dias','fijo') NOT NULL,
  `valor` int(11) NOT NULL DEFAULT 1,
  `monto` decimal(10,2) NOT NULL,
  `es_tarifa_fraccionable` tinyint(1) NOT NULL DEFAULT 0,
  `es_default` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `es_tope_diario` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id`, `descripcion`, `unidad`, `valor`, `monto`, `es_tarifa_fraccionable`, `es_default`, `activo`, `created_at`, `es_tope_diario`) VALUES
(1, 'Fracción 15 minutos', 'minutos', 15, 600.00, 1, 0, 1, '2026-02-24 19:04:39', 0),
(2, 'Fracción 30 minutos', 'minutos', 30, 1100.00, 1, 0, 1, '2026-02-24 19:04:39', 0),
(3, 'Por hora', 'horas', 1, 2000.00, 1, 0, 1, '2026-02-24 19:04:39', 0),
(4, 'Tope diario máximo', 'fijo', 1, 10000.00, 0, 0, 1, '2026-02-24 19:04:39', 1),
(5, 'Abono 3 días', 'dias', 3, 25000.00, 0, 0, 1, '2026-02-24 19:04:39', 0),
(6, 'Abono semanal', 'dias', 7, 55000.00, 0, 0, 1, '2026-02-24 19:04:39', 0),
(7, 'Abono mensual', 'dias', 30, 90000.00, 0, 0, 1, '2026-02-24 19:04:39', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_user` int(3) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name_user` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telefono` varchar(13) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `permisos_acceso` enum('Super Admin','Gerente','Operador') NOT NULL,
  `status` enum('activo','bloqueado') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_user`, `username`, `name_user`, `password`, `email`, `telefono`, `foto`, `permisos_acceso`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', '21232f297a57a5a743894a0e4a801fc3', 'info@sist.com', '7025', 'logo-blue.png', 'Super Admin', 'activo', '2017-04-01 08:15:15', '2026-02-25 06:14:51'),
(2, 'juan', 'juan', 'a94652aa97c7211ba8954dd15a3cf838', 'juab@juan.com', '12000', 'logo-blue.png', 'Operador', 'activo', '2017-07-25 22:34:03', '2026-02-27 04:30:02'),
(4, 'Operadores2026', 'operadores2026', '1ebef7bd551e7aeadd9d9a41b49e2500', 'operadores@gmail.com', '2236000111', 'favicon.png', 'Operador', 'activo', '2026-02-25 06:18:43', '2026-02-27 04:30:12'),
(5, 'Marina2026', 'Marina2026', '155ada61f003f381081e2ee1d6425e3d', NULL, NULL, NULL, 'Super Admin', 'activo', '2026-02-27 02:20:17', '2026-02-27 02:20:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL,
  `patente` varchar(15) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `hora_ingreso` time NOT NULL,
  `fecha_egreso` date DEFAULT NULL,
  `hora_egreso` time DEFAULT NULL,
  `tarifa_id` int(11) NOT NULL,
  `tarifa_descripcion` varchar(100) DEFAULT NULL,
  `tarifa_monto` decimal(10,2) DEFAULT NULL,
  `en_playa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto_total` decimal(10,2) DEFAULT NULL,
  `medio_cobro` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `patente`, `fecha_ingreso`, `hora_ingreso`, `fecha_egreso`, `hora_egreso`, `tarifa_id`, `tarifa_descripcion`, `tarifa_monto`, `en_playa`, `created_at`, `monto_total`, `medio_cobro`) VALUES
(290, 'AABBCC', '2026-02-28', '01:16:22', '2026-02-28', '05:17:20', 3, NULL, NULL, 0, '2026-02-28 04:16:22', 600.00, 'transferencia'),
(291, 'JHO9911111', '2026-02-28', '01:17:38', '2026-02-28', '05:17:56', 1, NULL, NULL, 0, '2026-02-28 04:17:38', 600.00, 'efectivo'),
(292, 'LVQ900', '2026-02-28', '01:20:02', '2026-03-01', '05:00:00', 4, NULL, NULL, 0, '2026-02-28 04:20:02', NULL, NULL),
(293, 'ADSF854815', '2026-02-28', '01:23:58', NULL, NULL, 4, NULL, NULL, 1, '2026-02-28 04:23:58', NULL, NULL),
(294, 'JHO9911111', '2026-02-28', '01:31:04', NULL, NULL, 9, NULL, NULL, 1, '2026-02-28 04:31:04', NULL, NULL),
(295, 'AAABBCC4522', '2026-02-28', '01:32:24', NULL, NULL, 7, NULL, NULL, 1, '2026-02-28 04:32:24', NULL, NULL),
(296, 'LVQ972', '2026-03-11', '19:22:42', NULL, NULL, 3, NULL, NULL, 1, '2026-03-11 22:22:42', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patente` (`patente`);

--
-- Indices de la tabla `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `profile_modules`
--
ALTER TABLE `profile_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `level` (`permisos_acceso`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patente` (`patente`),
  ADD KEY `en_playa` (`en_playa`),
  ADD KEY `tarifa_id` (`tarifa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=735;

--
-- AUTO_INCREMENT de la tabla `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `profile_modules`
--
ALTER TABLE `profile_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_user` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
