-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2024 a las 04:45:14
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
  `id_aviso_tipo` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `adjunto` varchar(255) NOT NULL,
  `fijado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `avisos`
--

INSERT INTO `avisos` (`id_aviso`, `id_aviso_tipo`, `id_usuario`, `titulo`, `descripcion`, `fecha_publicacion`, `fecha_vencimiento`, `adjunto`, `fijado`) VALUES
(1, 1, 2, 'No hay clases', 'Paro de transporte', '2024-05-10', '2024-05-10', 'pdf', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aviso_tipo`
--

CREATE TABLE `aviso_tipo` (
  `id_aviso_tipo` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `aviso_tipo`
--

INSERT INTO `aviso_tipo` (`id_aviso_tipo`, `descripcion`) VALUES
(1, 'general'),
(2, 'urgente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `id_aviso` int(11) NOT NULL,
  `id_tramite` int(11) NOT NULL,
  `id_notificacion_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_tipo`
--

CREATE TABLE `notificacion_tipo` (
  `id_notificacion_tipo` int(11) NOT NULL,
  `notificacion_tipo` varchar(50) NOT NULL,
  `prioridad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responsables`
--

CREATE TABLE `responsables` (
  `id_responsable` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `responsables`
--

INSERT INTO `responsables` (`id_responsable`, `id_usuario`) VALUES
(2, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites`
--

CREATE TABLE `tramites` (
  `id_tramite` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `id_tramite_tipo` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramites`
--

INSERT INTO `tramites` (`id_tramite`, `id_usuario`, `id_responsable`, `id_tramite_tipo`, `id_estado`, `descripcion`) VALUES
(1, 3, 1, 2, 2, 'listo'),
(2, 1, 2, 1, 1, 'detalles');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_estado`
--

CREATE TABLE `tramites_estado` (
  `id_estado` int(11) NOT NULL,
  `nombre_estado` varchar(40) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramites_estado`
--

INSERT INTO `tramites_estado` (`id_estado`, `nombre_estado`, `descripcion`) VALUES
(1, 'en pausa', 'falta de documentacion'),
(2, 'finalisado', 'listo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_tipo`
--

CREATE TABLE `tramites_tipo` (
  `id_tramite_tipo` int(11) NOT NULL,
  `tramite_nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tramites_tipo`
--

INSERT INTO `tramites_tipo` (`id_tramite_tipo`, `tramite_nombre`, `descripcion`) VALUES
(1, 'constancias de alumno regular', 'Alumno que cursa en este instituto'),
(2, 'solicitar certificados de asistencia a examen,', 'en el dia de la fecha el alumno asistió a un examen');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(35) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dni` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `carrera` varchar(255) NOT NULL,
  `anio` int(11) NOT NULL,
  `comision` varchar(255) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `firma_digital` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `password`, `dni`, `email`, `carrera`, `anio`, `comision`, `estado`, `firma_digital`) VALUES
(1, 'luciano', 'Gomez', '123', 11111111, 'luciano@itb.com.ar', 'Analista de sistemas', 3, '1', 'activo', 'luli'),
(2, 'micaela', 'martinez', '123', 22222222, 'mica@itb.com.ar', '', 0, '', 'egresado', 'mica.M'),
(3, 'david', 'yaps', '123', 333333333, 'david@itb.com.ar', '', 0, '', 'activo', 'david.Y'),
(4, 'maxi', 'lopez', 'Ma1234567', 1232654, 'algo@algo', 'sistemas', 2, '1', 'activo', 'masssi'),
(5, 'santiago', 'fraga', '$2y$10$/z5hjH9qKiNIgrKGo1lRUOlk6PIySRMu0/8JMGdZ09c6wrw/zpqBu', 33333321, 'santiago@itb.com.ar', 'Analista de Sistemas', 1, '3ra', 'en curso', 'hjkasdbfjkhasbfhy'),
(7, 'santiago2', 'fraga2', '$2y$10$M7gkPNzH.Nk6ki2f5.9/teJUoE1DAqogknVlGeTE1iOcb7qDvfXPi', 33436321, 'santiag2o@itb.com.ar', 'Analista de Sistemas', 1, '3ra', 'en curso', 'hjkasdbfjkhasbfhy'),
(8, 'asfdas', 'fasfdasdfasrag', '$2y$10$hnWsHngGpUwZEf37ipym8.4ylFyzLvizxCRvSme7fIgHPW.c8W5hi', 123121, 'santsa2131dfia@itb.com.ar', 'Analista de Sistemas', 1, '3ra', 'baja', 'hjkasdbfjkhasbfhy'),
(9, 'asdsfasdasfdas', 'fasfdasasdfasdfadfasrag', '$2y$10$k8AfaEDln32NzcNBJqFLMub/WQEs5iynBxZwv10dVmALZn6/Y9r6q', 121123, 'santsa1231asdsdfsdafas22131dfia@itb.com.ar', 'Analista de Sistemas', 1, '3ra', 'baja', 'hjkasdbfjkhasbfhy');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_notificacion`
--

CREATE TABLE `usuario_notificacion` (
  `id_usuario` int(11) NOT NULL,
  `id_notificacion` int(11) NOT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `fecha_envio` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 1),
(2, 3),
(3, 2),
(4, 2),
(9, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_tipos`
--

CREATE TABLE `usuario_tipos` (
  `id_usuario_tipo` int(11) NOT NULL,
  `permiso_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_tipos`
--

INSERT INTO `usuario_tipos` (`id_usuario_tipo`, `permiso_nombre`) VALUES
(1, 'alumno'),
(2, 'docente'),
(3, 'deptoAlumnos');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `avisos`
--
ALTER TABLE `avisos`
  ADD PRIMARY KEY (`id_aviso`),
  ADD KEY `id_aviso_tipo` (`id_aviso_tipo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `aviso_tipo`
--
ALTER TABLE `aviso_tipo`
  ADD PRIMARY KEY (`id_aviso_tipo`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_notificacion`,`id_aviso`,`id_tramite`,`id_notificacion_tipo`),
  ADD KEY `fk_id_aviso` (`id_aviso`),
  ADD KEY `fk_id_tramite` (`id_tramite`),
  ADD KEY `fk_id_notificacion_tipo` (`id_notificacion_tipo`);

--
-- Indices de la tabla `notificacion_tipo`
--
ALTER TABLE `notificacion_tipo`
  ADD PRIMARY KEY (`id_notificacion_tipo`);

--
-- Indices de la tabla `responsables`
--
ALTER TABLE `responsables`
  ADD PRIMARY KEY (`id_responsable`),
  ADD KEY `fk_usuario` (`id_usuario`);

--
-- Indices de la tabla `tramites`
--
ALTER TABLE `tramites`
  ADD PRIMARY KEY (`id_tramite`,`id_usuario`,`id_responsable`,`id_tramite_tipo`,`id_estado`),
  ADD KEY `fk_idUsuario` (`id_usuario`),
  ADD KEY `fk_id_responsable` (`id_responsable`),
  ADD KEY `fk_id_tramite_tipo` (`id_tramite_tipo`),
  ADD KEY `fk_id_estado` (`id_estado`);

--
-- Indices de la tabla `tramites_estado`
--
ALTER TABLE `tramites_estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `tramites_tipo`
--
ALTER TABLE `tramites_tipo`
  ADD PRIMARY KEY (`id_tramite_tipo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuario_notificacion`
--
ALTER TABLE `usuario_notificacion`
  ADD PRIMARY KEY (`id_usuario`,`id_notificacion`),
  ADD KEY `fk_idnotificacion` (`id_notificacion`);

--
-- Indices de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD PRIMARY KEY (`id_usuario`,`id_usuario_tipo`),
  ADD KEY `fk_id_usuario_tipo` (`id_usuario_tipo`);

--
-- Indices de la tabla `usuario_tipos`
--
ALTER TABLE `usuario_tipos`
  ADD PRIMARY KEY (`id_usuario_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `avisos`
--
ALTER TABLE `avisos`
  ADD CONSTRAINT `id_aviso_tipo` FOREIGN KEY (`id_aviso_tipo`) REFERENCES `aviso_tipo` (`id_aviso_tipo`),
  ADD CONSTRAINT `id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `fk_id_aviso` FOREIGN KEY (`id_aviso`) REFERENCES `avisos` (`id_aviso`),
  ADD CONSTRAINT `fk_id_notificacion_tipo` FOREIGN KEY (`id_notificacion_tipo`) REFERENCES `notificacion_tipo` (`id_notificacion_tipo`),
  ADD CONSTRAINT `fk_id_tramite` FOREIGN KEY (`id_tramite`) REFERENCES `tramites` (`id_tramite`);

--
-- Filtros para la tabla `responsables`
--
ALTER TABLE `responsables`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `tramites`
--
ALTER TABLE `tramites`
  ADD CONSTRAINT `fk_idUsuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_id_estado` FOREIGN KEY (`id_estado`) REFERENCES `tramites_estado` (`id_estado`),
  ADD CONSTRAINT `fk_id_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `responsables` (`id_responsable`),
  ADD CONSTRAINT `fk_id_tramite_tipo` FOREIGN KEY (`id_tramite_tipo`) REFERENCES `tramites_tipo` (`id_tramite_tipo`);

--
-- Filtros para la tabla `usuario_notificacion`
--
ALTER TABLE `usuario_notificacion`
  ADD CONSTRAINT `fk_idnotificacion` FOREIGN KEY (`id_notificacion`) REFERENCES `notificacion` (`id_notificacion`),
  ADD CONSTRAINT `fk_iduser` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD CONSTRAINT `fk_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_id_usuario_tipo` FOREIGN KEY (`id_usuario_tipo`) REFERENCES `usuario_tipos` (`id_usuario_tipo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
