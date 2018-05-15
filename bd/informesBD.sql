-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS db_competiciones CHARACTER SET utf8 COLLATE utf8_general_ci;
USE db_competiciones;

-- Creación de la tabla en la cuál se cargarán los datos.
CREATE TABLE IF NOT EXISTS competidores(
    id INT AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(20),
    apellidos VARCHAR(50),
    anio SMALLINT,
    sexo CHAR(1),
    club VARCHAR(60),
    clubComunidad VARCHAR(60),
    competicion VARCHAR(60),
    fechaCompeticion DATE,
    lugarCompeticion VARCHAR(60),
    comunidadCompeticion VARCHAR(60),
    tipoPiscina CHAR(3),
    prueba VARCHAR(60),
    agrupacion VARCHAR(100),
    categoria VARCHAR(20),
    tipoSerioe VARCHAR(30),
    ronda TINYINT,
    tiempo VARCHAR(8),
    tiempoConvertido VARCHAR(8),
    posicion TINYINT,
    exclusion VARCHAR(20),
    descalificado CHAR(2),
    CONSTRAINT pk_competidores PRIMARY KEY (id)
)ENGINE = InnoDb;