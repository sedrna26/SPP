-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-08-2024 a las 00:12:12
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
-- Base de datos: `spp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacionregistro`
--

CREATE TABLE `clasificacionregistro` (
  `id_clasificacion` int(11) NOT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `sector_alojamiento` varchar(100) DEFAULT NULL,
  `pabellon` int(11) DEFAULT NULL,
  `fecha_entrevista` datetime DEFAULT NULL,
  `visitas_permitidas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`visitas_permitidas`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `educacion`
--

CREATE TABLE `educacion` (
  `id_educacion` int(11) NOT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `nivel_educativo` enum('Primaria Completa','Primaria Incompleta','Secundaria Completa','Secundaria Incompleta','Terciaria Incompleta','Terciaria Completa') DEFAULT NULL,
  `establecimiento` varchar(255) DEFAULT NULL,
  `grado` varchar(100) DEFAULT NULL,
  `conocimiento_oficios` varchar(255) DEFAULT NULL,
  `interes_actividades` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `dni` bigint(20) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `edad` int(3) NOT NULL,
  `sexo` enum('Masculino','Femenino','No binario','Otro') DEFAULT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `estado_civil` enum('Soltero/a','Casado/a','Divorciado/a','Viudo/a') DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ppl`
--

CREATE TABLE `ppl` (
  `id_ppl` int(11) NOT NULL,
  `dni` bigint(20) DEFAULT NULL,
  `profesion_oficio` varchar(100) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salud`
--

CREATE TABLE `salud` (
  `id_salud` int(11) NOT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `estado_general_salud` varchar(255) DEFAULT NULL,
  `enfermedades` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`enfermedades`)),
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `imc` decimal(4,2) DEFAULT NULL,
  `diagnostico` varchar(255) DEFAULT NULL,
  `tratamientos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tratamientos`)),
  `informe_psiquiatrico` varchar(255) DEFAULT NULL,
  `informe_psicologico` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacionfamiliar`
--

CREATE TABLE `situacionfamiliar` (
  `id_familiar` int(11) NOT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `padre_nombre` varchar(100) DEFAULT NULL,
  `padre_estado_civil` enum('Soltero','Casado','Divorciado','Viudo') DEFAULT NULL,
  `padre_profesion` varchar(100) DEFAULT NULL,
  `madre_nombre` varchar(100) DEFAULT NULL,
  `madre_estado_civil` enum('Soltera','Casada','Divorciada','Viuda') DEFAULT NULL,
  `madre_profesion` varchar(100) DEFAULT NULL,
  `hermanos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hermanos`)),
  `conyuge_nombre` varchar(100) DEFAULT NULL,
  `hijos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hijos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacionlegal`
--

CREATE TABLE `situacionlegal` (
  `id_situacion_legal` int(11) NOT NULL,
  `id_ppl` int(11) DEFAULT NULL,
  `fecha_detencion` date DEFAULT NULL,
  `dependencia` varchar(100) DEFAULT NULL,
  `motivo_traslado` varchar(255) DEFAULT NULL,
  `situacion_legal` enum('Procesado','Penado') DEFAULT NULL,
  `causa` varchar(255) DEFAULT NULL,
  `juzgado` varchar(100) DEFAULT NULL,
  `nroportuario` int(11) DEFAULT NULL,
  `condena` varchar(255) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `reincidencia` enum('Primario','Reiterante') DEFAULT NULL,
  `histcondena` enum('Primario','Registra Condena') DEFAULT NULL,
  `cantcondenas` int(60) DEFAULT NULL,  
  `ultima_condena_fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clasificacionregistro`
--
ALTER TABLE `clasificacionregistro`
  ADD PRIMARY KEY (`id_clasificacion`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `educacion`
--
ALTER TABLE `educacion`
  ADD PRIMARY KEY (`id_educacion`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`dni`);

--
-- Indices de la tabla `ppl`
--
ALTER TABLE `ppl`
  ADD PRIMARY KEY (`id_ppl`),
  ADD KEY `dni` (`dni`);

--
-- Indices de la tabla `salud`
--
ALTER TABLE `salud`
  ADD PRIMARY KEY (`id_salud`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `situacionfamiliar`
--
ALTER TABLE `situacionfamiliar`
  ADD PRIMARY KEY (`id_familiar`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `situacionlegal`
--
ALTER TABLE `situacionlegal`
  ADD PRIMARY KEY (`id_situacion_legal`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clasificacionregistro`
--
ALTER TABLE `clasificacionregistro`
  MODIFY `id_clasificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `educacion`
--
ALTER TABLE `educacion`
  MODIFY `id_educacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ppl`
--
ALTER TABLE `ppl`
  MODIFY `id_ppl` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `salud`
--
ALTER TABLE `salud`
  MODIFY `id_salud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `situacionfamiliar`
--
ALTER TABLE `situacionfamiliar`
  MODIFY `id_familiar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `situacionlegal`
--
ALTER TABLE `situacionlegal`
  MODIFY `id_situacion_legal` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clasificacionregistro`
--
ALTER TABLE `clasificacionregistro`
  ADD CONSTRAINT `clasificacionregistro_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id_ppl`);

--
-- Filtros para la tabla `educacion`
--
ALTER TABLE `educacion`
  ADD CONSTRAINT `educacion_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id_ppl`);

--
-- Filtros para la tabla `ppl`
--
ALTER TABLE `ppl`
  ADD CONSTRAINT `ppl_ibfk_1` FOREIGN KEY (`dni`) REFERENCES `persona` (`dni`);

--
-- Filtros para la tabla `salud`
--
ALTER TABLE `salud`
  ADD CONSTRAINT `salud_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id_ppl`);

--
-- Filtros para la tabla `situacionfamiliar`
--
ALTER TABLE `situacionfamiliar`
  ADD CONSTRAINT `situacionfamiliar_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id_ppl`);

--
-- Filtros para la tabla `situacionlegal`
--
ALTER TABLE `situacionlegal`
  ADD CONSTRAINT `situacionlegal_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id_ppl`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
