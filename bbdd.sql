CREATE DATABASE blog;
USE blog;

CREATE TABLE usuaris(
id          int(255) auto_increment not null,
nom      varchar(100) not null,
cognom   varchar(100) not null,
email       varchar(255) not null,
password    varchar(255) not null,
data       date not null,
CONSTRAINT pk_usuaris PRIMARY KEY(id),
CONSTRAINT uq_email UNIQUE(email)
)ENGINE=InnoDb;

CREATE TABLE categories(
id      int(255) auto_increment not null,
nombre  varchar(100),
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE entrades(
id              int(255) auto_increment not null,
usuari_id      int(255) not null,
categoria_id    int(255) not null,
titol          varchar(255) not null,
descripcio     MEDIUMTEXT,
data           date not null,
CONSTRAINT pk_entrades PRIMARY KEY(id),
CONSTRAINT fk_entrada_usuari FOREIGN KEY(usuari_id) REFERENCES usuaris(id),
CONSTRAINT fk_entrada_categoria FOREIGN KEY(categoria_id) REFERENCES categories(id) ON DELETE NO ACTION
)ENGINE=InnoDb;
