CREATE DATABASE IF NOT EXISTS api_canal_denuncias;
USE api_canal_denuncias;

CREATE TABLE users(
    id 		int(255) auto_increment not null,
    email varchar(255),
    role	varchar(20),
    name	varchar(255),
    surname	varchar(255),
    password varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    remember_token varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE noticias (
	id int(255) auto_increment not null, 
	categoria varchar(255),
	titulo varchar(255),
	cuerpo text,
	imagen varchar(255),
	principal  bit(1),
	created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
     CONSTRAINT pk_noticias PRIMARY KEY(id)
)ENGINE=InnoDb;


CREATE TABLE denuncias (
    id int(255) auto_increment not null,
    ticket varchar(255) not null,
    nombre varchar(255),
    email varchar(255),
    teléfono varchar(255),
    motivo varchar(255),
    denuncia text,
    reclamo text,
    antecedentes varchar(255),
    razon_social varchar(255),
    rut varchar(255),
    resuelto bit(1),
    departamento varchar(255),
    correo_encargado varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_denuncias PRIMARY KEY(id)
)ENGINE=InnoDb;


CREATE TABLE contactos (
    id int(255) auto_increment not null,
    nombre varchar(255),
    email varchar(255),
    teléfono varchar(255),
    motivo varchar(255),
    cargo varchar(255),
    pretension_renta varchar(255),
    rut varchar(255),
    empresa varchar(255),
    r_social varchar(255),
    producto_proveedor text,
    servicio_solicitado varchar(255),
    tipo_equipo varchar(255),
    origen  varchar(255),
    destino varchar(255),
    carga varchar(255),
    mensaje text,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_contactos PRIMARY KEY(id)
)ENGINE=InnoDb;

    
CREATE TABLE contenido (
    id int(255) auto_increment not null,
    pagina varchar(255),
	h1  varchar(255), 
	h2  varchar(255),
	h3  varchar(255),
	h4  varchar(255),
	h5  varchar(255),
	p1  text,
	p2  text,
	p3  text,
	p4  text,
	p5  text,
	p6  text,
	p7  text,
	p8  text,
	p9  text,
	p10 text,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_contenido PRIMARY KEY(id)
)ENGINE=InnoDb;


