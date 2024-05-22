-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-05-2024 a las 02:25:01
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestiondptoalumnos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avisos`
--

CREATE TABLE `avisos` (
  `id_aviso` int(11) NOT NULL,
  `id_aviso_tipo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `adjunto` varchar(255) NOT NULL,
  `fijado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aviso_tipo`
--

CREATE TABLE `aviso_tipo` (
  `id_aviso_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aviso_usuario_tipo`
--

CREATE TABLE `aviso_usuario_tipo` (
  `id_aviso` int(11) NOT NULL,
  `id_usuario_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_tipos`
--

CREATE TABLE `documento_tipos` (
  `id_documento_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_aviso` int(11) NOT NULL,
  `id_tramite` int(11) NOT NULL,
  `id_notificacion_tipo` int(11) NOT NULL,
  `fecha_envio_notificacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_estado`
--

CREATE TABLE `notificacion_estado` (
  `id_notificacion_estado` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_notificaciones`
--

CREATE TABLE `tipo_notificaciones` (
  `id_notificacion_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites`
--

CREATE TABLE `tramites` (
  `id_tramite` int(11) NOT NULL,
  `id_usuario_creacion` int(11) NOT NULL,
  `id_usuario_responsable` int(11) NOT NULL,
  `id_tramite_tipo` int(11) NOT NULL,
  `id_estado_tramite` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha_creacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_tipo`
--

CREATE TABLE `tramites_tipo` (
  `id_tramite_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite_adjuntos`
--

CREATE TABLE `tramite_adjuntos` (
  `id_tramite_adjunto` int(11) NOT NULL,
  `id_tramite` int(11) NOT NULL,
  `ubicacion_archivo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite_estados`
--

CREATE TABLE `tramite_estados` (
  `id_estado_tramite` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite_movimientos`
--

CREATE TABLE `tramite_movimientos` (
  `id_tramite` int(11) NOT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `observacion` varchar(255) NOT NULL,
  `id_estado_tramite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_documento_tipo` int(11) NOT NULL,
  `id_usuario_estado` int(11) NOT NULL,
  `numero_documento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_carreras`
--

CREATE TABLE `usuario_carreras` (
  `id_usuario` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `comision` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_estados`
--

CREATE TABLE `usuario_estados` (
  `id_usuario_estado` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_notificaciones`
--

CREATE TABLE `usuario_notificaciones` (
  `id_usuario` int(11) NOT NULL,
  `id_notificacion` int(11) NOT NULL,
  `id_notificacion_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

CREATE TABLE `usuario_roles` (
  `id_usuario` int(11) NOT NULL,
  `id_usuario_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_tipos`
--

CREATE TABLE `usuario_tipos` (
  `id_usuario_tipo` int(11) NOT NULL,
  `permiso_nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `avisos`
--
ALTER TABLE `avisos`
  ADD PRIMARY KEY (`id_aviso`),
  ADD KEY `fk_id_aviso_tipo` (`id_aviso_tipo`),
  ADD KEY `fk_id_usuario` (`id_usuario`);

--
-- Indices de la tabla `aviso_tipo`
--
ALTER TABLE `aviso_tipo`
  ADD PRIMARY KEY (`id_aviso_tipo`);

--
-- Indices de la tabla `aviso_usuario_tipo`
--
ALTER TABLE `aviso_usuario_tipo`
  ADD PRIMARY KEY (`id_aviso`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera_id`);

--
-- Indices de la tabla `documento_tipos`
--
ALTER TABLE `documento_tipos`
  ADD PRIMARY KEY (`id_documento_tipo`),
  ADD KEY `descripcion` (`descripcion`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_aviso` (`id_aviso`),
  ADD KEY `id_tramite` (`id_tramite`),
  ADD KEY `id_notificacion_tipo` (`id_notificacion_tipo`);

--
-- Indices de la tabla `notificacion_estado`
--
ALTER TABLE `notificacion_estado`
  ADD PRIMARY KEY (`id_notificacion_estado`);

--
-- Indices de la tabla `tipo_notificaciones`
--
ALTER TABLE `tipo_notificaciones`
  ADD PRIMARY KEY (`id_notificacion_tipo`);

--
-- Indices de la tabla `tramites`
--
ALTER TABLE `tramites`
  ADD PRIMARY KEY (`id_tramite`),
  ADD KEY `id_usuario_creacion` (`id_usuario_creacion`),
  ADD KEY `id_usuario_responsable` (`id_usuario_responsable`),
  ADD KEY `id_tramite_tipo` (`id_tramite_tipo`),
  ADD KEY `id_estado_tramite` (`id_estado_tramite`);

--
-- Indices de la tabla `tramites_tipo`
--
ALTER TABLE `tramites_tipo`
  ADD PRIMARY KEY (`id_tramite_tipo`);

--
-- Indices de la tabla `tramite_adjuntos`
--
ALTER TABLE `tramite_adjuntos`
  ADD PRIMARY KEY (`id_tramite_adjunto`),
  ADD KEY `fk_idtramite` (`id_tramite`);

--
-- Indices de la tabla `tramite_estados`
--
ALTER TABLE `tramite_estados`
  ADD PRIMARY KEY (`id_estado_tramite`);

--
-- Indices de la tabla `tramite_movimientos`
--
ALTER TABLE `tramite_movimientos`
  ADD PRIMARY KEY (`id_tramite`),
  ADD KEY `fecha_movimiento` (`fecha_movimiento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_documento_tipo` (`id_documento_tipo`),
  ADD KEY `id_usuario_estado` (`id_usuario_estado`);

--
-- Indices de la tabla `usuario_carreras`
--
ALTER TABLE `usuario_carreras`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_carrera` (`id_carrera`);

--
-- Indices de la tabla `usuario_estados`
--
ALTER TABLE `usuario_estados`
  ADD PRIMARY KEY (`id_usuario_estado`),
  ADD KEY `descripcion` (`descripcion`);

--
-- Indices de la tabla `usuario_notificaciones`
--
ALTER TABLE `usuario_notificaciones`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_notificacion` (`id_notificacion`),
  ADD KEY `fk_id_notificacion_estado` (`id_notificacion_estado`);

--
-- Indices de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_usuario_tipo` (`id_usuario_tipo`);

--
-- Indices de la tabla `usuario_tipos`
--
ALTER TABLE `usuario_tipos`
  ADD PRIMARY KEY (`id_usuario_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aviso_tipo`
--
ALTER TABLE `aviso_tipo`
  MODIFY `id_aviso_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `aviso_usuario_tipo`
--
ALTER TABLE `aviso_usuario_tipo`
  MODIFY `id_aviso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `notificacion_estado`
--
ALTER TABLE `notificacion_estado`
  MODIFY `id_notificacion_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_notificaciones`
--
ALTER TABLE `tipo_notificaciones`
  MODIFY `id_notificacion_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuario_notificaciones`
--
ALTER TABLE `usuario_notificaciones`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `avisos`
--
ALTER TABLE `avisos`
  ADD CONSTRAINT `fk_id_aviso_tipo` FOREIGN KEY (`id_aviso_tipo`) REFERENCES `aviso_tipo` (`id_aviso_tipo`),
  ADD CONSTRAINT `fk_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_id_aviso` FOREIGN KEY (`id_aviso`) REFERENCES `avisos` (`id_aviso`),
  ADD CONSTRAINT `fk_id_notificacion_tipo` FOREIGN KEY (`id_notificacion_tipo`) REFERENCES `tipo_notificaciones` (`id_notificacion_tipo`),
  ADD CONSTRAINT `fk_id_tramite` FOREIGN KEY (`id_tramite`) REFERENCES `tramites` (`id_tramite`);

--
-- Filtros para la tabla `tramite_adjuntos`
--
ALTER TABLE `tramite_adjuntos`
  ADD CONSTRAINT `fk_idtramite` FOREIGN KEY (`id_tramite`) REFERENCES `tramites` (`id_tramite`);

--
-- Filtros para la tabla `usuario_notificaciones`
--
ALTER TABLE `usuario_notificaciones`
  ADD CONSTRAINT `fk_id_notificacion_estado` FOREIGN KEY (`id_notificacion_estado`) REFERENCES `notificacion_estado` (`id_notificacion_estado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
