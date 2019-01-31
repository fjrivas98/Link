create database simple
  default character set utf8
  collate utf8_general_ci;
  
create user simple@localhost
  identified by 'simple';

grant all
  on simple.*
  to simple@localhost;

flush privileges;

create table simple.usuario (
    id bigint not null auto_increment primary key,
    correo varchar(60) not null unique,
    clave varchar(255) not null
) engine = innodb
  character set utf8
  collate utf8_general_ci;
  
  
  
  create table usuario (
    id bigint not null auto_increment  primary key,
    correo varchar(60) not null unique,
    alias varchar(30) unique null,
    nombre varchar(30) not null,
    clave varchar(255) not null,
    activo bit(1) not null default 0,
    fechaalta timestamp default current_timestamp,
    admin bit(1) not null default 0
) engine = innodb
  character set utf8
  collate utf8_general_ci;