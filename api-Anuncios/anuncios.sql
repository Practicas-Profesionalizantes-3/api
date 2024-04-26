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
