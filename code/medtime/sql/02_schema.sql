USE medtime;

DROP TABLE IF EXISTS notificacion;
DROP TABLE IF EXISTS cita;
DROP TABLE IF EXISTS profesional;
DROP TABLE IF EXISTS paciente;
DROP TABLE IF EXISTS usuario;

CREATE TABLE usuario (
    id_usuario BIGINT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    contrasena_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

CREATE TABLE paciente (
    id_paciente BIGINT NOT NULL AUTO_INCREMENT,
    id_usuario BIGINT NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    fecha_nacimiento DATE NOT NULL,
    num_historial VARCHAR(30) NOT NULL UNIQUE,
    PRIMARY KEY (id_paciente),
    UNIQUE (id_usuario),
    CONSTRAINT fk_paciente_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE profesional (
    id_profesional BIGINT NOT NULL AUTO_INCREMENT,
    id_usuario BIGINT NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    num_colegiado VARCHAR(30) NOT NULL UNIQUE,
    duracion_media_consulta_min INT NOT NULL,
    disponible BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_profesional),
    UNIQUE (id_usuario),
    CONSTRAINT fk_profesional_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuario(id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cita (
    id_cita BIGINT NOT NULL AUTO_INCREMENT,
    id_paciente BIGINT NOT NULL,
    id_profesional BIGINT NOT NULL,
    fecha_hora_programada DATETIME NOT NULL,
    fecha_hora_estimada DATETIME NULL,
    fecha_hora_real_inicio DATETIME NULL,
    fecha_hora_real_fin DATETIME NULL,
    estado VARCHAR(30) NOT NULL,
    observaciones TEXT,
    creada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_cita),
    CONSTRAINT fk_cita_paciente
        FOREIGN KEY (id_paciente)
        REFERENCES paciente(id_paciente)
        ON DELETE CASCADE,
    CONSTRAINT fk_cita_profesional
        FOREIGN KEY (id_profesional)
        REFERENCES profesional(id_profesional)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notificacion (
    id_notificacion BIGINT NOT NULL AUTO_INCREMENT,
    id_cita BIGINT NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(30) NOT NULL,
    PRIMARY KEY (id_notificacion),
    CONSTRAINT fk_notificacion_cita
        FOREIGN KEY (id_cita)
        REFERENCES cita(id_cita)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_paciente_id_usuario ON paciente(id_usuario);
CREATE INDEX idx_profesional_id_usuario ON profesional(id_usuario);
CREATE INDEX idx_cita_id_paciente ON cita(id_paciente);
CREATE INDEX idx_cita_id_profesional ON cita(id_profesional);
CREATE INDEX idx_cita_fecha_hora_programada ON cita(fecha_hora_programada);
CREATE INDEX idx_notificacion_id_cita ON notificacion(id_cita);
