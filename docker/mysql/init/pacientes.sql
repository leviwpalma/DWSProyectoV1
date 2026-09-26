use uniondental;

CREATE TABLE pacientes (
    id_paciente           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_expediente     VARCHAR(20)  NOT NULL UNIQUE,
    nombres               VARCHAR(80)  NOT NULL,
    apellidos             VARCHAR(80)  NOT NULL,
    telefono              VARCHAR(20)  NOT NULL,
    correo                VARCHAR(120) NULL,
    fecha_nacimiento      DATE         NOT NULL,
    activo                TINYINT(1)   NOT NULL DEFAULT 1,
    created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME     NULL,

    INDEX idx_nombres  (nombres, apellidos),
    INDEX idx_telefono (telefono)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;