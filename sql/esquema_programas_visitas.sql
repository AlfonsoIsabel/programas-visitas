-- esquema_programas_visitas.sql
CREATE DATABASE IF NOT EXISTS programas_visitas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE programas_visitas;

-- tabla usuarios (administradores)
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nombre VARCHAR(150),
  creado_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tabla formularios (plantillas de formularios públicos)
CREATE TABLE formularios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  descripcion TEXT,
  creado_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tabla respuestas de formularios (datos enviados por enlaces públicos)
CREATE TABLE respuestas_formularios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  form_id INT NOT NULL,
  titulo VARCHAR(255),
  nombre_remitente VARCHAR(255),
  fecha DATE,
  hora_inicio TIME,
  hora_fin TIME,
  descripcion TEXT,
  imagen VARCHAR(255),
  extra_json TEXT,
  fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (form_id) REFERENCES formularios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tabla programas (opcional: programas creados/agrupados por admin)
CREATE TABLE programas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255),
  fecha_inicio DATE,
  fecha_fin DATE,
  imagen_portada VARCHAR(255),
  logo_principal VARCHAR(255),
  logo_secundario1 VARCHAR(255),
  logo_secundario2 VARCHAR(255),
  creado_por INT,
  creado_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tabla visitas (puntos del programa)
CREATE TABLE visitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  programa_id INT NOT NULL,
  dia_semana VARCHAR(20),
  fecha DATE,
  horario VARCHAR(100),
  descripcion TEXT,
  imagen VARCHAR(255),
  orden INT DEFAULT 0,
  FOREIGN KEY (programa_id) REFERENCES programas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;