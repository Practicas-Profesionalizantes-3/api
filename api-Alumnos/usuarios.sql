CREATE TABLE usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni INT (11) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    carrera VARCHAR (255) NOT NULL,
    anio INT (5) NOT NULL,
    comision VARCHAR (10) NOT NULL,
    estado INT (11) NOT NULL,
    rol varchar (255) NOT NULL
);
CREATE TABLE anuncios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR (255) NOT NULL,
    carrera VARCHAR (255) NOT NULL,
    anio VARCHAR (255) NOT NULL,
    comision VARCHAR (255) NOT NULL,
    estado VARCHAR (255) NOT NULL,
    fechaDesde VARCHAR(255) UNIQUE NOT NULL,
    fechaHasta VARCHAR(255) NOT NULL
);

CREATE TABLE tramites (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tipo_tramite VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    estado VARCHAR (255) NOT NULL
    
);

CREATE TABLE notificaciones (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tipo_notificacion VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL
);