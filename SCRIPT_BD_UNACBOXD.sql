CREATE DATABASE IF NOT EXISTS unacboxd CHARACTER SET UTF8MB4;

USE unacboxd;

CREATE TABLE rol(
	id_rol INT PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL
);

CREATE TABLE usuario(
	id_usuario INT PRIMARY KEY AUTO_INCREMENT,
	nombre_usuario VARCHAR(100) NOT NULL,
	email VARCHAR(100) NOT NULL,
	contrasena VARCHAR(255) NOT NULL,
	pregunta_secreta VARCHAR(255) NOT NULL,
	id_rol INT NOT NULL
);

CREATE TABLE director(
	id_director INT PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(100) NOT NULL
);

CREATE TABLE genero(
	id_genero INT PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL
);

CREATE TABLE actor(
	id_actor INT PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(100) NOT NULL
);

CREATE TABLE pelicula(
	id_pelicula INT PRIMARY KEY AUTO_INCREMENT,
	titulo VARCHAR(150) NOT NULL,
	anio_estreno SMALLINT NOT NULL,
	duracion_minutos SMALLINT NOT NULL,
	pais VARCHAR(100) NOT NULL,
    clasificacion_edad VARCHAR(20) NOT NULL,
	sinopsis TEXT NOT NULL,
    poster VARCHAR(255),
    color VARCHAR(10),
    fondo_claro BOOLEAN,
	id_director INT NOT NULL
);

CREATE TABLE pelicula_genero(
	id_pelicula INT NOT NULL,
	id_genero INT NOT NULL,
    PRIMARY KEY (id_pelicula, id_genero)
);

CREATE TABLE pelicula_actor(
	id_pelicula INT NOT NULL,
	id_actor INT NOT NULL,
    PRIMARY KEY (id_pelicula, id_actor)
);

CREATE TABLE pelicula_usuario(
	id_pelicula INT NOT NULL,
	id_usuario INT NOT NULL,
	tipo VARCHAR(20) NOT NULL,
	activo BOOLEAN,
    PRIMARY KEY (id_pelicula, id_usuario)
);

CREATE TABLE calificacion(
	id_calificacion INT PRIMARY KEY AUTO_INCREMENT,
	id_pelicula INT NOT NULL,
	calificacion DECIMAL(3, 2) UNSIGNED NOT NULL
);

/* ========================================= */
/* LLAVES FORANEAS                           */
/* ========================================= */

ALTER TABLE usuario ADD CONSTRAINT FK_ID_ROL FOREIGN KEY (id_rol) REFERENCES rol(id_rol);

ALTER TABLE pelicula ADD CONSTRAINT FK_ID_DIRECTOR FOREIGN KEY (id_director) REFERENCES director(id_director);

ALTER TABLE pelicula_genero ADD CONSTRAINT FK_ID_PELICULA_G FOREIGN KEY (id_pelicula) REFERENCES pelicula(id_pelicula);
ALTER TABLE pelicula_genero ADD CONSTRAINT FK_ID_GENERO FOREIGN KEY (id_genero) REFERENCES genero(id_genero);

ALTER TABLE pelicula_actor ADD CONSTRAINT FK_ID_PELICULA_A FOREIGN KEY (id_pelicula) REFERENCES pelicula(id_pelicula);
ALTER TABLE pelicula_actor ADD CONSTRAINT FK_ID_ACTOR FOREIGN KEY (id_actor) REFERENCES actor(id_actor);

ALTER TABLE pelicula_usuario ADD CONSTRAINT FK_ID_PELICULA_U FOREIGN KEY (id_pelicula) REFERENCES pelicula(id_pelicula);
ALTER TABLE pelicula_usuario ADD CONSTRAINT FK_ID_USUARIO FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario);