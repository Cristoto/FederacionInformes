-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS db_competiciones CHARACTER SET utf8 COLLATE utf8_general_ci;
USE db_competiciones;

-- Creación de la tabla en la cuál se cargarán los datos.
CREATE TABLE IF NOT EXISTS competidores(
    id INT AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(25),
    apellidos VARCHAR(65),
    anio SMALLINT,
    sexo CHAR(1),
    club VARCHAR(60),
    clubComunidad VARCHAR(60),
    competicion VARCHAR(60),
    fechaCompeticion DATE,
    lugarCompeticion VARCHAR(60),
    comunidadCompeticion VARCHAR(60),
    tipoPiscina CHAR(5),
    prueba VARCHAR(100),
    agrupacion VARCHAR(130),
    categoria VARCHAR(20),
    tipoSerioe VARCHAR(50),
    ronda TINYINT,
    tiempo TIME,
    tiempoConvertido TIME,
    posicion TINYINT,
    exclusion VARCHAR(50),
    descalificado CHAR(4),
    CONSTRAINT pk_competidores PRIMARY KEY (id)
)ENGINE = InnoDb;