-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2024 a las 23:29:53
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
-- Estructura de tabla para la tabla `caracteristicas`
--

CREATE TABLE `caracteristicas` (
  `id` int(11) NOT NULL,
  `zona` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tamaño` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion`
--

CREATE TABLE `clasificacion` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `sugerencia` varchar(50) DEFAULT NULL,
  `sector_nro` int(4) NOT NULL,
  `pabellon_nro` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datosantropometri`
--

CREATE TABLE `datosantropometri` (
  `id` int(11) NOT NULL,
  `peso_actual` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `imc` decimal(4,2) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `tipificacion_dieta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `educacion`
--

CREATE TABLE `educacion` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `id_familiar` int(11) DEFAULT NULL,
  `establecimiento` varchar(50) NOT NULL,
  `grado` varchar(10) DEFAULT NULL,
  `año` int(4) DEFAULT NULL,
  `motivo_abandono` text DEFAULT NULL,
  `oferta_educ` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enfermedades`
--

CREATE TABLE `enfermedades` (
  `id` int(11) NOT NULL,
  `enfermedad` varchar(255) NOT NULL,
  `padecio` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrevista`
--

CREATE TABLE `entrevista` (
  `id` int(11) NOT NULL,
  `idppl` int(11) NOT NULL,
  `hora` datetime NOT NULL,
  `fechai` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familia`
--

CREATE TABLE `familia` (
  `id` int(11) NOT NULL,
  `ppl` int(11) NOT NULL,
  `relacion` text NOT NULL,
  `datos` int(11) NOT NULL,
  `ffaa` tinyint(1) DEFAULT NULL,
  `fam_detenida` tinyint(1) DEFAULT NULL,
  `fecha_fall` date DEFAULT NULL,
  `causa_fall` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fechappl`
--

CREATE TABLE `fechappl` (
  `id` int(11) NOT NULL,
  `fechadet` date NOT NULL,
  `fechacond` date NOT NULL,
  `fechavenc` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_psicologico`
--

CREATE TABLE `informe_psicologico` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `tipo_ingreso` varchar(15) NOT NULL,
  `orient_temporo_esp` text DEFAULT NULL,
  `juicio_realidad` text DEFAULT NULL,
  `ideacion` text DEFAULT NULL,
  `estado_afectivo` text DEFAULT NULL,
  `antecedente_autolesion` text DEFAULT NULL,
  `otros_datos` text DEFAULT NULL,
  `dato_interes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_psiquiatrico`
--

CREATE TABLE `informe_psiquiatrico` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `tuvo_diagnostico` tinyint(1) NOT NULL,
  `especificacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_sanitario`
--

CREATE TABLE `informe_sanitario` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `estado_gral` varchar(200) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_medicamento` int(11) NOT NULL,
  `id_enfermedades` int(11) NOT NULL,
  `id_datos_antrop` int(11) NOT NULL,
  `marcas_partic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juzgado`
--

CREATE TABLE `juzgado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `nombre_juez` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

CREATE TABLE `medicamentos` (
  `id` int(11) NOT NULL,
  `toma_med` tinyint(1) NOT NULL,
  `nombre_medicamento` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `otros`
--

CREATE TABLE `otros` (
  `id` int(11) NOT NULL,
  `datos` int(11) DEFAULT NULL,
  `id_ppl` int(11) NOT NULL,
  `vinculo_fam` text DEFAULT NULL,
  `frec_visita` varchar(20) DEFAULT NULL,
  `tiene_dni` tinyint(1) DEFAULT NULL,
  `prejuicio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id` int(11) NOT NULL,
  `dni` varchar(9) DEFAULT NULL,
  `apellidos` varchar(50) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `fechanac` date NOT NULL,
  `edad` int(3) NOT NULL,
  `direccion` int(11) NOT NULL,
  `genero` varchar(50) NOT NULL,
  `estadocivil` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ppl`
--

CREATE TABLE `ppl` (
  `id` int(11) NOT NULL,
  `idpersona` int(11) NOT NULL,
  `apodo` varchar(20) DEFAULT NULL,
  `trabaja` tinyint(1) NOT NULL,
  `profesion` varchar(50) DEFAULT NULL,
  `foto` varchar(30) DEFAULT NULL,
  `huella` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacionlegal`
--

CREATE TABLE `situacionlegal` (
  `id` int(11) NOT NULL,
  `ppl` int(11) NOT NULL,
  `motivo_t` varchar(100) DEFAULT NULL,
  `situacionlegal` varchar(15) NOT NULL,
  `prontuario` int(6) DEFAULT NULL,
  `reincidencia` tinyint(1) NOT NULL,
  `salida_transitoria` tinyint(1) NOT NULL,
  `libertad_asistida` tinyint(1) NOT NULL,
  `libertad_condicional` tinyint(1) NOT NULL,
  `delito` int(11) NOT NULL,
  `fecha` int(11) NOT NULL,
  `juzgado` int(11) NOT NULL,
  `señas_partic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipodelito`
--

CREATE TABLE `tipodelito` (
  `id` int(11) NOT NULL,
  `titulo` varchar(30) NOT NULL,
  `subcategoria` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id` int(11) NOT NULL,
  `pais` varchar(255) NOT NULL,
  `provincia` varchar(255) NOT NULL,
  `departamento` varchar(50) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas`
--

CREATE TABLE `visitas` (
  `id` int(11) NOT NULL,
  `id_ppl` int(11) NOT NULL,
  `id_familia` int(11) DEFAULT NULL,
  `id_otros` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caracteristicas`
--
ALTER TABLE `caracteristicas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clasificacion`
--
ALTER TABLE `clasificacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `datosantropometri`
--
ALTER TABLE `datosantropometri`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `educacion`
--
ALTER TABLE `educacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ppl` (`id_ppl`),
  ADD KEY `id_familiar` (`id_familiar`);

--
-- Indices de la tabla `enfermedades`
--
ALTER TABLE `enfermedades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `entrevista`
--
ALTER TABLE `entrevista`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idppl` (`idppl`);

--
-- Indices de la tabla `familia`
--
ALTER TABLE `familia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ppl` (`ppl`),
  ADD KEY `datos` (`datos`);

--
-- Indices de la tabla `fechappl`
--
ALTER TABLE `fechappl`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `informe_psicologico`
--
ALTER TABLE `informe_psicologico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `informe_psiquiatrico`
--
ALTER TABLE `informe_psiquiatrico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `informe_sanitario`
--
ALTER TABLE `informe_sanitario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ppl` (`id_ppl`),
  ADD KEY `id_medicamento` (`id_medicamento`),
  ADD KEY `id_enfermedades` (`id_enfermedades`),
  ADD KEY `id_datos_antrop` (`id_datos_antrop`),
  ADD KEY `marcas_partic` (`marcas_partic`);

--
-- Indices de la tabla `juzgado`
--
ALTER TABLE `juzgado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `otros`
--
ALTER TABLE `otros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datos` (`datos`),
  ADD KEY `id_ppl` (`id_ppl`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id`),
  ADD KEY `direccion` (`direccion`),
  ADD KEY `educacion` (`educacion`);

--
-- Indices de la tabla `ppl`
--
ALTER TABLE `ppl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idpersona` (`idpersona`);

--
-- Indices de la tabla `situacionlegal`
--
ALTER TABLE `situacionlegal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ppl` (`ppl`),
  ADD KEY `delito` (`delito`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `juzgado` (`juzgado`),
  ADD KEY `caracteristicas` (`señas_partic`);

--
-- Indices de la tabla `tipodelito`
--
ALTER TABLE `tipodelito`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ppl` (`id_ppl`),
  ADD KEY `id_familia` (`id_familia`),
  ADD KEY `id_otros` (`id_otros`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caracteristicas`
--
ALTER TABLE `caracteristicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clasificacion`
--
ALTER TABLE `clasificacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datosantropometri`
--
ALTER TABLE `datosantropometri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `educacion`
--
ALTER TABLE `educacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `enfermedades`
--
ALTER TABLE `enfermedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrevista`
--
ALTER TABLE `entrevista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familia`
--
ALTER TABLE `familia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fechappl`
--
ALTER TABLE `fechappl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informe_psicologico`
--
ALTER TABLE `informe_psicologico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informe_psiquiatrico`
--
ALTER TABLE `informe_psiquiatrico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informe_sanitario`
--
ALTER TABLE `informe_sanitario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `juzgado`
--
ALTER TABLE `juzgado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `otros`
--
ALTER TABLE `otros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ppl`
--
ALTER TABLE `ppl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `situacionlegal`
--
ALTER TABLE `situacionlegal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipodelito`
--
ALTER TABLE `tipodelito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `visitas`
--
ALTER TABLE `visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clasificacion`
--
ALTER TABLE `clasificacion`
  ADD CONSTRAINT `clasificacion_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`);

--
-- Filtros para la tabla `educacion`
--
ALTER TABLE `educacion`
  ADD CONSTRAINT `educacion_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `educacion_ibfk_2` FOREIGN KEY (`id_familiar`) REFERENCES `familia` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `entrevista`
--
ALTER TABLE `entrevista`
  ADD CONSTRAINT `entrevista_ibfk_1` FOREIGN KEY (`idppl`) REFERENCES `ppl` (`id`);

--
-- Filtros para la tabla `familia`
--
ALTER TABLE `familia`
  ADD CONSTRAINT `familia_ibfk_1` FOREIGN KEY (`ppl`) REFERENCES `ppl` (`id`),
  ADD CONSTRAINT `familia_ibfk_2` FOREIGN KEY (`datos`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `informe_psicologico`
--
ALTER TABLE `informe_psicologico`
  ADD CONSTRAINT `informe_psicologico_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`);

--
-- Filtros para la tabla `informe_psiquiatrico`
--
ALTER TABLE `informe_psiquiatrico`
  ADD CONSTRAINT `informe_psiquiatrico_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`);

--
-- Filtros para la tabla `informe_sanitario`
--
ALTER TABLE `informe_sanitario`
  ADD CONSTRAINT `informe_sanitario_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`),
  ADD CONSTRAINT `informe_sanitario_ibfk_2` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id`),
  ADD CONSTRAINT `informe_sanitario_ibfk_3` FOREIGN KEY (`id_enfermedades`) REFERENCES `enfermedades` (`id`),
  ADD CONSTRAINT `informe_sanitario_ibfk_4` FOREIGN KEY (`id_datos_antrop`) REFERENCES `datosantropometri` (`id`),
  ADD CONSTRAINT `informe_sanitario_ibfk_5` FOREIGN KEY (`marcas_partic`) REFERENCES `caracteristicas` (`id`);

--
-- Filtros para la tabla `otros`
--
ALTER TABLE `otros`
  ADD CONSTRAINT `otros_ibfk_1` FOREIGN KEY (`datos`) REFERENCES `persona` (`id`),
  ADD CONSTRAINT `otros_ibfk_2` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`);

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`direccion`) REFERENCES `ubicacion` (`id`),
  ADD CONSTRAINT `persona_ibfk_2` FOREIGN KEY (`educacion`) REFERENCES `educacion` (`id`);

--
-- Filtros para la tabla `ppl`
--
ALTER TABLE `ppl`
  ADD CONSTRAINT `ppl_ibfk_1` FOREIGN KEY (`idpersona`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `situacionlegal`
--
ALTER TABLE `situacionlegal`
  ADD CONSTRAINT `situacionlegal_ibfk_1` FOREIGN KEY (`ppl`) REFERENCES `ppl` (`id`),
  ADD CONSTRAINT `situacionlegal_ibfk_2` FOREIGN KEY (`delito`) REFERENCES `tipodelito` (`id`),
  ADD CONSTRAINT `situacionlegal_ibfk_3` FOREIGN KEY (`fecha`) REFERENCES `fechappl` (`id`),
  ADD CONSTRAINT `situacionlegal_ibfk_4` FOREIGN KEY (`juzgado`) REFERENCES `juzgado` (`id`),
  ADD CONSTRAINT `situacionlegal_ibfk_5` FOREIGN KEY (`señas_partic`) REFERENCES `caracteristicas` (`id`);

--
-- Filtros para la tabla `visitas`
--
ALTER TABLE `visitas`
  ADD CONSTRAINT `visitas_ibfk_1` FOREIGN KEY (`id_ppl`) REFERENCES `ppl` (`id`),
  ADD CONSTRAINT `visitas_ibfk_2` FOREIGN KEY (`id_familia`) REFERENCES `familia` (`id`),
  ADD CONSTRAINT `visitas_ibfk_3` FOREIGN KEY (`id_otros`) REFERENCES `otros` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
