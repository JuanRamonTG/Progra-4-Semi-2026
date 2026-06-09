-- 1. CREAR TABLA DE ROLES
CREATE TABLE IF NOT EXISTS roles (
    id_rol SERIAL PRIMARY KEY,
    nombre_rol VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);

-- Insertar el rol predeterminado de Administrador
INSERT INTO roles (id_rol, nombre_rol, descripcion) 
VALUES (1, 'Administrador', 'Rol con acceso total al sistema vial')
ON CONFLICT (id_rol) DO NOTHING;

-- Sincronizar secuencia
SELECT setval(pg_get_serial_sequence('roles', 'id_rol'), COALESCE(max(id_rol), 1)) FROM roles;


-- 2. CREAR TABLA DE USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL DEFAULT 1,
    nombre_usuario VARCHAR(255) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (id_rol) REFERENCES roles (id_rol) ON DELETE RESTRICT
);


-- 3. CREAR TABLA DE ACCIDENTES
CREATE TABLE IF NOT EXISTS accidentes (
    id_accidente SERIAL PRIMARY KEY,
    id_caso VARCHAR(100) NULL,
    tipo_accidente VARCHAR(100) NULL,
    fecha_incidente DATE NULL,
    hora_aproximada TIME NULL,
    gravedad VARCHAR(50) NULL,
    direccion VARCHAR(255) NULL,
    municipio VARCHAR(100) NULL,
    descripcion TEXT NULL,
    condicion_climatica VARCHAR(100) NULL,
    tipo_via VARCHAR(100) NULL,
    estado_pavimento VARCHAR(100) NULL,
    declaracion_involucrados TEXT NULL,
    id_usuario INT NULL,
    CONSTRAINT fk_accidentes_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE SET NULL
);


-- 4. CREAR TABLA DE EVIDENCIAS
CREATE TABLE IF NOT EXISTS evidencias (
    id_evidencia SERIAL PRIMARY KEY,
    id_accidente INT NOT NULL,
    url_archivo VARCHAR(500) NOT NULL,
    tipo_evidencia VARCHAR(100) NULL,
    CONSTRAINT fk_evidencias_accidentes FOREIGN KEY (id_accidente) REFERENCES accidentes(id_accidente) ON DELETE CASCADE
);


-- 5. CREAR TABLA DE PERSONAS INVOLUCRADAS
CREATE TABLE IF NOT EXISTS personas_involucradas (
    id_persona SERIAL PRIMARY KEY,
    id_accidente INT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    estado_persona VARCHAR(100) NULL,
    observaciones TEXT NULL,
    CONSTRAINT fk_personas_accidentes FOREIGN KEY (id_accidente) REFERENCES accidentes(id_accidente) ON DELETE CASCADE
);


-- 6. CREAR TABLA DE VEHÍCULOS INVOLUCRADOS
CREATE TABLE IF NOT EXISTS vehiculos_involucrados (
    id_vehiculo SERIAL PRIMARY KEY,
    id_accidente INT NOT NULL,
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    tipo_vehiculo VARCHAR(100) NULL,
    anio INT NULL,
    propietario VARCHAR(255) NULL,
    CONSTRAINT fk_vehiculos_accidentes FOREIGN KEY (id_accidente) REFERENCES accidentes(id_accidente) ON DELETE CASCADE
);


-- TABLAS DEL SISTEMA DE LARAVEL (OBLIGATORIAS PARA EL LOGIN)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INT NOT NULL
);

CREATE TABLE IF NOT EXISTS cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INT NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
);
