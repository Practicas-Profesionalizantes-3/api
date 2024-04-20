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
    estado INT (11) NOT NULL
);
