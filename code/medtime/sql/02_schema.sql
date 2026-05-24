USE medtime;

DROP TABLE IF EXISTS notificacion;
DROP TABLE IF EXISTS cita;
DROP TABLE IF EXISTS profesional;
DROP TABLE IF EXISTS paciente;
DROP TABLE IF EXISTS usuario;

CREATE TABLE usuario (
    id_usuario BIGINT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    apelidos VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    contrasinal_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    data_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

CREATE TABLE paciente (
    id_paciente BIGINT NOT NULL AUTO_INCREMENT,
    id_usuario BIGINT NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    data_nacemento DATE NOT NULL,
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
    especialidade VARCHAR(100) NOT NULL,
    num_colexiado VARCHAR(30) NOT NULL UNIQUE,
    duracion_media_consulta_min INT NOT NULL,
    dispoñible BOOLEAN NOT NULL DEFAULT TRUE,
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
    data_hora_programada DATETIME NOT NULL,
    data_hora_estimada DATETIME NULL,
    data_hora_real_inicio DATETIME NULL,
    data_hora_real_fin DATETIME NULL,
    estado VARCHAR(30) NOT NULL,
    observacions TEXT,
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
    mensaxe TEXT NOT NULL,
    data_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado_envio VARCHAR(30) NOT NULL,
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
CREATE INDEX idx_cita_data_hora_programada ON cita(data_hora_programada);
CREATE INDEX idx_notificacion_id_cita ON notificacion(id_cita);