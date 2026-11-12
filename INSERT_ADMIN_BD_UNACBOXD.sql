-- USUARIO ADMINISTRADOR (ejecutar después de INSERTS_BD_UNACBOXD.sql, el rol admin tiene id_rol = 1)
-- Credenciales iniciales -> usuario: admin | contraseña: Admin123* | pregunta secreta: unacboxd
-- IMPORTANTE: cambia la contraseña desde la opción "Cambiar Contraseña" después del primer ingreso.
INSERT INTO usuario (nombre_usuario, email, contrasena, pregunta_secreta, foto_perfil, id_rol) VALUES
('admin', 'admin@unacboxd.com', '$2y$10$n10ZuwaeCgNRAi6HpAFrM.xbsdQT9XDefYkl/nLUudXtGsGBRkSVu', '$2y$10$A7anI.tlHDvheDObXxJj3e4D/g7CLmczBfbWtkbqEalIGaFPjetia', NULL, 1);
