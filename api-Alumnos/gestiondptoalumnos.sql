-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-05-2024 a las 02:10:10
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
  `fijado` varchar(255) NOT NULL,
  `id_aviso_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `avisos`
--

INSERT INTO `avisos` (`id_aviso`, `id_aviso_tipo`, `id_usuario`, `titulo`, `descripcion`, `fecha_publicacion`, `fecha_vencimiento`, `adjunto`, `fijado`, `id_aviso_estado`) VALUES
(1, 1, 1, 'Aviso de Mantenimiento', 'El sistema estará en mantenimiento el próximo fin de semana.', '2024-05-15', '2024-05-22', 'mantenimiento.pdf', 'No', 2),
(2, 2, 2, 'Nueva Política de Seguridad', 'Se han actualizado las políticas de seguridad.', '2024-05-01', '2024-06-01', 'seguridad.pdf', 'Sí', 1),
(3, 3, 3, 'Reunión de Equipo', 'Reunión programada para discutir el proyecto.', '2024-05-10', '2024-05-12', 'reunion.pdf', 'No', 2),
(4, 4, 4, 'Actualización de Software', 'Nueva versión del software disponible.', '2024-05-20', '2024-05-27', 'actualizacion.pdf', 'Sí', 2),
(5, 1, 1, 'Aviso de Mantenimiento', 'El sistema estará en mantenimiento el próximo fin ', '2024-05-12', '2024-05-14', 'mantenimiento', 'si', 1),
(6, 2, 3, 'Libreta Virtual2', 'Se va a implementar la libreta virtual2', '2024-05-12', '2024-06-12', 'Libreta_Virtual.pdf', 'si', 1),
(7, 2, 3, 'Libreta Virtual3', 'Se va a implementar la libreta virtual2', '2024-05-12', '2024-06-12', 'Libreta_Virtual.pdf', 'si', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aviso_estado`
--

CREATE TABLE `aviso_estado` (
  `id_aviso_estado` int(11) NOT NULL,
  `descripcion` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `aviso_estado`
--

INSERT INTO `aviso_estado` (`id_aviso_estado`, `descripcion`) VALUES
(1, 'Activo'),
(2, 'Inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aviso_tipo`
--

CREATE TABLE `aviso_tipo` (
  `id_aviso_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `aviso_tipo`
--

INSERT INTO `aviso_tipo` (`id_aviso_tipo`, `descripcion`) VALUES
(1, 'Mantenimiento'),
(2, 'Política'),
(3, 'Reunión'),
(4, 'Actualización');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aviso_usuario_tipo`
--

CREATE TABLE `aviso_usuario_tipo` (
  `id_aviso` int(11) NOT NULL,
  `id_usuario_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `aviso_usuario_tipo`
--

INSERT INTO `aviso_usuario_tipo` (`id_aviso`, `id_usuario_tipo`) VALUES
(1, 1),
(2, 2),
(3, 2),
(4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera_id`, `descripcion`) VALUES
(1, 'Ingeniería Informática'),
(2, 'Medicina'),
(3, 'Derecho'),
(4, 'Administración de Empresas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_tipos`
--

CREATE TABLE `documento_tipos` (
  `id_documento_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `documento_tipos`
--

INSERT INTO `documento_tipos` (`id_documento_tipo`, `descripcion`) VALUES
(1, 'DNI'),
(3, 'Licencia de Conducir'),
(2, 'Pasaporte');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_aviso` int(11) DEFAULT NULL,
  `id_tramite` int(11) DEFAULT NULL,
  `id_notificacion_tipo` int(11) NOT NULL,
  `fecha_envio_notificacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `id_aviso`, `id_tramite`, `id_notificacion_tipo`, `fecha_envio_notificacion`) VALUES
(1, 1, 1, 1, '2024-05-02'),
(2, 2, 2, 2, '2024-05-11'),
(3, 3, 3, 3, '2024-05-16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_estado`
--

CREATE TABLE `notificacion_estado` (
  `id_notificacion_estado` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `notificacion_estado`
--

INSERT INTO `notificacion_estado` (`id_notificacion_estado`, `descripcion`) VALUES
(1, 'Enviada'),
(2, 'Recibida'),
(3, 'Leída');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_notificaciones`
--

CREATE TABLE `tipo_notificaciones` (
  `id_notificacion_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_notificaciones`
--

INSERT INTO `tipo_notificaciones` (`id_notificacion_tipo`, `descripcion`) VALUES
(1, 'Email'),
(2, 'SMS'),
(3, 'Push');

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

--
-- Volcado de datos para la tabla `tramites`
--

INSERT INTO `tramites` (`id_tramite`, `id_usuario_creacion`, `id_usuario_responsable`, `id_tramite_tipo`, `id_estado_tramite`, `descripcion`, `fecha_creacion`) VALUES
(1, 1, 2, 1, 1, 'Solicitud de nueva clave', '2024-05-01'),
(2, 2, 3, 2, 2, 'Reclamación de notas', '2024-05-10'),
(3, 3, 4, 3, 3, 'Consulta sobre inscripción', '2024-05-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_tipo`
--

CREATE TABLE `tramites_tipo` (
  `id_tramite_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramites_tipo`
--

INSERT INTO `tramites_tipo` (`id_tramite_tipo`, `descripcion`) VALUES
(1, 'Solicitud'),
(2, 'Reclamación'),
(3, 'Consulta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite_adjuntos`
--

CREATE TABLE `tramite_adjuntos` (
  `id_tramite_adjunto` int(11) NOT NULL,
  `id_tramite` int(11) NOT NULL,
  `ubicacion_archivo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramite_adjuntos`
--

INSERT INTO `tramite_adjuntos` (`id_tramite_adjunto`, `id_tramite`, `ubicacion_archivo`) VALUES
(1, 1, 'solicitud_clave.pdf'),
(2, 2, 'reclamacion_notas.pdf'),
(3, 3, 'consulta_inscripcion.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite_estados`
--

CREATE TABLE `tramite_estados` (
  `id_estado_tramite` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramite_estados`
--

INSERT INTO `tramite_estados` (`id_estado_tramite`, `descripcion`) VALUES
(1, 'Pendiente'),
(2, 'En Proceso'),
(3, 'Completado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite_movimientos`
--

CREATE TABLE `tramite_movimientos` (
  `id_tramite` int(11) NOT NULL,
  `fecha_movimiento` date NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `observacion` varchar(255) NOT NULL,
  `id_estado_tramite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramite_movimientos`
--

INSERT INTO `tramite_movimientos` (`id_tramite`, `fecha_movimiento`, `id_usuario`, `observacion`, `id_estado_tramite`) VALUES
(1, '2024-05-02', 2, 'Clave generada y enviada', 3),
(2, '2024-05-11', 3, 'Notas revisadas y actualizadas', 3),
(3, '2024-05-16', 4, 'Consulta respondida', 3);

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

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `password`, `email`, `id_documento_tipo`, `id_usuario_estado`, `numero_documento`) VALUES
(1, 'Maximiliano', 'Lopez', 'password123', 'javierM@itb.com.ar', 1, 1, 30524956),
(2, 'María', 'García', 'password123', 'maria.garcia@example.com', 2, 1, 87654321),
(3, 'Carlos', 'López', 'password123', 'carlos.lopez@example.com', 3, 1, 11223344),
(4, 'Ana', 'Martínez', 'password123', 'ana.martinez@example.com', 1, 1, 44332211),
(5, 'Maximiliano', 'Lopez', '$2y$10$k0TGHmqBOf0vsaeecJMQZOcCqRisP9zJpcDn47ZcErHFq22dr0pTe', 'javierM@itb.com.ar', 1, 1, 30524956),
(6, 'nadia', 'lopez', '$2y$10$M4vnwxOCkhrsotEhGn464.2u7NR6vOCoX.j.Uxl5LIhjdgIFncGOG', 'juanperez@example.com', 1, 1, 2147483647),
(13, 'Nadia123', 'lopez2', '$2y$10$wpkzAPuArFWzkuPOI76kEueLQq.L0d8p1hWpV.N3ObRs5wnDxnkKm', 'juanperez@example.com123', 1, 1, 2147483647),
(14, 'maxi', 'lopez2', '$2y$10$Gur3ytpg4nyPV4tlwMvCLOmWb4fmBjOsLAFIl3nBaridS6NuLMNpm', 'juanasdfdsaperez@example.com123', 1, 1, 2147483647),
(15, 'maxi', 'lopez2', '$2y$10$WqJ7VcrUMxj6Uo36Yw0kpOG/GIauyqfNCelPzLeT6dKRXOKN/XmCu', 'juanas1231231dfdsaperez@example.com123', 1, 1, 2147483647),
(16, 'maxi2', 'lopez212', '$2y$10$87P6FvIz4P/h.mLosFjkj.mX9nC/.lFe3rYVGwifJ4GBgAHJB9HTm', 'juanas1231231dfdsap121erez@example.com123', 1, 1, 2147483647),
(17, 'maxi2', 'lopez212', '$2y$10$sguDzH.4QrnOqsBNorPlt.5Ejb5e.58nQZvjAfCW4Wmstv4hx7uH2', 'juanas12222231231dfdsap121erez@example.com123', 1, 1, 2147483647),
(18, 'maxi2', 'lopez212', '$2y$10$bTSkkvBS/se2wgB29qkAAec9FOSuC4SesVzAZZTMNFEDKMO242MUC', 'juan1as12222231231dfdsap121erez@example.com123', 1, 1, 2147483647),
(19, 'maxi2', 'lopez212', '$2y$10$8xhfz8tYqYUWDyZpm3jor.v69TuA7ttC5NS3FNSnEYkwfplz7SAr6', 'juan1a111s12222231231dfdsap121erez@example.com123', 1, 1, 2147483647),
(20, 'maxi2', 'lopez212', '$2y$10$IE1Xpjjrv483.vL8aQFOlOPkdVAiOyaiJXdDDQ3HNdOzQUujOYYbO', 'juan111a111s12222231231dfdsap121erez@example.com123', 1, 1, 2147483647),
(21, 'maxi2', 'lopez212', '$2y$10$MCA0pu0jiOiexxnlI8OlO.dXiqOoOPoupwBQCqxEJUdPlW2cSoO8S', 'jua1n111a111s12222231231dfdsap121erez@example.com123', 1, 1, 2147483647),
(22, 'maxi2', 'lopez212', '$2y$10$i0CQLhUwB/mYEQbxEDVD8uCXpmUk1TXXQvDKlWtRHHbM9uFRUpKBS', 'maxilopez@hotmail.com', 1, 1, 2147483647),
(23, 'maxi2', 'lopez212', '$2y$10$VNlGU/6OkZDez9/6SrdlduCcpqb9lNLIwW5Q8WJT4r4X4ZVpOiCv6', 'maasaxilopez@hotmail.com', 1, 1, 2147483647);

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

--
-- Volcado de datos para la tabla `usuario_carreras`
--

INSERT INTO `usuario_carreras` (`id_usuario`, `id_carrera`, `anio`, `comision`) VALUES
(1, 1, 2025, 'B'),
(2, 2, 2023, 'B'),
(3, 3, 2021, 'C'),
(4, 4, 2020, 'D'),
(11, 2, 2024, 'a'),
(12, 2, 2024, 'a'),
(13, 2, 2024, 'a'),
(19, 2, 2024, 'a'),
(21, 2, 2024, 'a'),
(22, 2, 2024, 'a'),
(23, 2, 2024, 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_estados`
--

CREATE TABLE `usuario_estados` (
  `id_usuario_estado` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_estados`
--

INSERT INTO `usuario_estados` (`id_usuario_estado`, `descripcion`) VALUES
(1, 'Activo'),
(2, 'Inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_notificaciones`
--

CREATE TABLE `usuario_notificaciones` (
  `id_usuario` int(11) NOT NULL,
  `id_notificacion` int(11) NOT NULL,
  `id_notificacion_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_notificaciones`
--

INSERT INTO `usuario_notificaciones` (`id_usuario`, `id_notificacion`, `id_notificacion_estado`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

CREATE TABLE `usuario_roles` (
  `id_usuario` int(11) NOT NULL,
  `id_usuario_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_roles`
--

INSERT INTO `usuario_roles` (`id_usuario`, `id_usuario_tipo`) VALUES
(1, 3),
(2, 2),
(3, 1),
(3, 2),
(4, 2),
(11, 1),
(12, 1),
(13, 1),
(13, 2),
(21, 2),
(22, 1),
(22, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_tipos`
--

CREATE TABLE `usuario_tipos` (
  `id_usuario_tipo` int(11) NOT NULL,
  `permiso_nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_tipos`
--

INSERT INTO `usuario_tipos` (`id_usuario_tipo`, `permiso_nombre`) VALUES
(1, 'Administrador'),
(2, 'Usuario Regular'),
(3, 'Docente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `avisos`
--
ALTER TABLE `avisos`
  ADD PRIMARY KEY (`id_aviso`);

--
-- Indices de la tabla `aviso_estado`
--
ALTER TABLE `aviso_estado`
  ADD PRIMARY KEY (`id_aviso_estado`);

--
-- Indices de la tabla `aviso_tipo`
--
ALTER TABLE `aviso_tipo`
  ADD PRIMARY KEY (`id_aviso_tipo`);

--
-- Indices de la tabla `aviso_usuario_tipo`
--
ALTER TABLE `aviso_usuario_tipo`
  ADD PRIMARY KEY (`id_aviso`,`id_usuario_tipo`);

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
  ADD PRIMARY KEY (`id_notificacion`);

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
  ADD PRIMARY KEY (`id_tramite`,`fecha_movimiento`),
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
  ADD PRIMARY KEY (`id_usuario`,`id_carrera`),
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
  ADD PRIMARY KEY (`id_usuario`,`id_usuario_tipo`),
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
-- AUTO_INCREMENT de la tabla `avisos`
--
ALTER TABLE `avisos`
  MODIFY `id_aviso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `aviso_estado`
--
ALTER TABLE `aviso_estado`
  MODIFY `id_aviso_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuario_notificaciones`
--
ALTER TABLE `usuario_notificaciones`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

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
