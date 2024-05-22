1. Tabla aviso_tipo
INSERT INTO `aviso_tipo` (`id_aviso_tipo`, `descripcion`) VALUES (1, 'Mantenimiento'), (2, 'Política'), (3, 'Reunión'), (4, 'Actualización'); 

2. Tabla documento_tipos
INSERT INTO `documento_tipos` (`id_documento_tipo`, `descripcion`) VALUES (1, 'DNI'), (2, 'Pasaporte'), (3, 'Licencia de Conducir'); 

3. Tabla usuario_estados
INSERT INTO `usuario_estados` (`id_usuario_estado`, `descripcion`) VALUES (1, 'Activo'), (2, 'Inactivo'); 

4. Tabla usuarios
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `password`, `email`, `id_documento_tipo`, `id_usuario_estado`, `numero_documento`) VALUES (1, 'Juan', 'Pérez', 'password123', 'juan.perez@example.com', 1, 1, 12345678), (2, 'María', 'García', 'password123', 'maria.garcia@example.com', 2, 1, 87654321), (3, 'Carlos', 'López', 'password123', 'carlos.lopez@example.com', 3, 2, 11223344), (4, 'Ana', 'Martínez', 'password123', 'ana.martinez@example.com', 1, 2, 44332211); 

5. Tabla avisos
INSERT INTO `avisos` (`id_aviso`, `id_aviso_tipo`, `id_usuario`, `titulo`, `descripcion`, `fecha_publicacion`, `fecha_vencimiento`, `adjunto`, `fijado`) VALUES (1, 1, 1, 'Aviso de Mantenimiento', 'El sistema estará en mantenimiento el próximo fin de semana.', '2024-05-15', '2024-05-22', 'mantenimiento.pdf', 'No'), (2, 2, 2, 'Nueva Política de Seguridad', 'Se han actualizado las políticas de seguridad.', '2024-05-01', '2024-06-01', 'seguridad.pdf', 'Sí'), (3, 3, 3, 'Reunión de Equipo', 'Reunión programada para discutir el proyecto.', '2024-05-10', '2024-05-12', 'reunion.pdf', 'No'), (4, 4, 4, 'Actualización de Software', 'Nueva versión del software disponible.', '2024-05-20', '2024-05-27', 'actualizacion.pdf', 'Sí'); 

6. Tabla carreras
INSERT INTO `carreras` (`id_carrera_id`, `descripcion`) VALUES (1, 'Ingeniería Informática'), (2, 'Medicina'), (3, 'Derecho'), (4, 'Administración de Empresas'); 

7. Tabla usuario_tipos
INSERT INTO `usuario_tipos` (`id_usuario_tipo`, `permiso_nombre`) VALUES (1, 'Administrador'), (2, 'Usuario Regular'); 

8. Tabla usuario_roles
INSERT INTO `usuario_roles` (`id_usuario`, `id_usuario_tipo`) VALUES (1, 1), (2, 2), (3, 2), (4, 2); 

9. Tabla usuario_carreras
INSERT INTO `usuario_carreras` (`id_usuario`, `id_carrera`, `anio`, `comision`) VALUES (1, 1, 2022, 'A'), (2, 2, 2023, 'B'), (3, 3, 2021, 'C'), (4, 4, 2020, 'D'); 

10. Tabla tramite_estados
INSERT INTO `tramite_estados` (`id_estado_tramite`, `descripcion`) VALUES (1, 'Pendiente'), (2, 'En Proceso'), (3, 'Completado'); 

11. Tabla tramites_tipo
INSERT INTO `tramites_tipo` (`id_tramite_tipo`, `descripcion`) VALUES (1, 'Solicitud'), (2, 'Reclamación'), (3, 'Consulta'); 

12. Tabla tramites
INSERT INTO `tramites` (`id_tramite`, `id_usuario_creacion`, `id_usuario_responsable`, `id_tramite_tipo`, `id_estado_tramite`, `descripcion`, `fecha_creacion`) VALUES (1, 1, 2, 1, 1, 'Solicitud de nueva clave', '2024-05-01'), (2, 2, 3, 2, 2, 'Reclamación de notas', '2024-05-10'), (3, 3, 4, 3, 3, 'Consulta sobre inscripción', '2024-05-15'); 

13. Tabla tramite_adjuntos
INSERT INTO `tramite_adjuntos` (`id_tramite_adjunto`, `id_tramite`, `ubicacion_archivo`) VALUES (1, 1, 'solicitud_clave.pdf'), (2, 2, 'reclamacion_notas.pdf'), (3, 3, 'consulta_inscripcion.pdf'); 

14. Tabla tramite_movimientos
INSERT INTO `tramite_movimientos` (`id_tramite`, `fecha_movimiento`, `id_usuario`, `observacion`, `id_estado_tramite`) VALUES (1, '2024-05-02', 2, 'Clave generada y enviada', 3), (2, '2024-05-11', 3, 'Notas revisadas y actualizadas', 3), (3, '2024-05-16', 4, 'Consulta respondida', 3); 

15. Tabla tipo_notificaciones
INSERT INTO `tipo_notificaciones` (`id_notificacion_tipo`, `descripcion`) VALUES (1, 'Email'), (2, 'SMS'), (3, 'Push'); 

16. Tabla notificacion_estado
INSERT INTO `notificacion_estado` (`id_notificacion_estado`, `descripcion`) VALUES (1, 'Enviada'), (2, 'Recibida'), (3, 'Leída'); 

17. Tabla notificaciones
INSERT INTO `notificaciones` (`id_notificacion`, `id_aviso`, `id_tramite`, `id_notificacion_tipo`, `fecha_envio_notificacion`) VALUES (1, 1, 1, 1, '2024-05-02'), (2, 2, 2, 2, '2024-05-11'), (3, 3, 3, 3, '2024-05-16'); 

18. Tabla usuario_notificaciones
INSERT INTO `usuario_notificaciones` (`id_usuario`, `id_notificacion`, `id_notificacion_estado`) VALUES (1, 1, 1), (2, 2, 2), (3, 3, 3); 

19. Tabla aviso_usuario_tipo
INSERT INTO `aviso_usuario_tipo` (`id_aviso`, `id_usuario_tipo`) VALUES (1, 1), (2, 2), (3, 2), (4, 1);
