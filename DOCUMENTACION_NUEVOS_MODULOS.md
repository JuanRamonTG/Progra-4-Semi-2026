# Documentación de Nuevos Módulos

## Resumen General

Se han creado dos nuevos módulos completos con formularios, tablas de base de datos, controladores, rutas y componentes Vue.js:

1. **Registro de Material** - Gestión de materiales con ubicación, descripción y verificación
2. **Registro de Fallecido** - Gestión de registros de fallecimiento con datos detallados

---

## 1. BASES DE DATOS

### Tablas Creadas

#### 📦 Tabla: `registromateriales`
```sql
CREATE TABLE registromateriales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    descripcion TEXT,
    verificacion BOOLEAN DEFAULT FALSE,
    fotos LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 💔 Tabla: `registrofallecidos`
```sql
CREATE TABLE registrofallecidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    descripcion TEXT,
    verificacion BOOLEAN DEFAULT FALSE,
    fotos LONGTEXT,
    testigos TEXT,
    hora_fallecimiento DATETIME,
    estado VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Migraciones ejecutadas:**
- ✅ `2026_04_22_000001_create_registromateriales_table.php`
- ✅ `2026_04_22_000002_create_registrofallecidos_table.php`

---

## 2. MODELOS LARAVEL

### Modelo: RegistroMaterial
**Archivo:** `app/Models/RegistroMaterial.php`
- Tabla: `registromateriales`
- Fillable: fecha_hora, ubicacion, descripcion, verificacion, fotos
- Casts: fecha_hora (datetime), verificacion (boolean)

### Modelo: RegistroFallecido
**Archivo:** `app/Models/RegistroFallecido.php`
- Tabla: `registrofallecidos`
- Fillable: fecha_hora, ubicacion, descripcion, verificacion, fotos, testigos, hora_fallecimiento, estado
- Casts: fecha_hora (datetime), hora_fallecimiento (datetime), verificacion (boolean)

---

## 3. CONTROLADORES

### RegistroMaterialController
**Archivo:** `app/Http/Controllers/RegistroMaterialController.php`

#### Métodos:
- **index(Request $request)** - Maneja todas las operaciones
  - `accion=consultar` - Obtiene registros con búsqueda por:
    - `ubicacion` - Búsqueda por ubicación
    - `descripcion` - Búsqueda por descripción
    - `fecha_desde` - Filtro de fecha inicial
    - `fecha_hasta` - Filtro de fecha final
    - `verificacion` - Filtro por estado de verificación
  
  - `accion=nuevo|modificar` - Guarda o actualiza un registro
  - `accion=eliminar` - Elimina un registro

#### Validaciones:
- ✅ Validar que fecha_hora sea requerida
- ✅ Validar que ubicación sea requerida
- ✅ Manejo de errores con respuestas JSON

### RegistroFallecidoController
**Archivo:** `app/Http/Controllers/RegistroFallecidoController.php`

#### Métodos:
- **index(Request $request)** - Maneja todas las operaciones
  - `accion=consultar` - Obtiene registros con búsqueda por:
    - `ubicacion` - Búsqueda por ubicación
    - `descripcion` - Búsqueda por descripción
    - `fecha_desde` - Filtro de fecha inicial
    - `fecha_hasta` - Filtro de fecha final
    - `estado` - Filtro por estado (fallecido, hospitalizado)
    - `verificacion` - Filtro por estado de verificación
  
  - `accion=nuevo|modificar` - Guarda o actualiza un registro
  - `accion=eliminar` - Elimina un registro

#### Validaciones:
- ✅ Validar que fecha_hora sea requerida
- ✅ Validar que ubicación sea requerida
- ✅ Manejo de errores con respuestas JSON

---

## 4. RUTAS

**Archivo:** `routes/web.php`

### Rutas de Sincronización (Legacy)
```php
Route::prefix('private/modulos')->group(function () {
    Route::any('registromaterial/registromaterial.php', [RegistroMaterialController::class, 'index']);
    Route::any('registrofallecido/registrofallecido.php', [RegistroFallecidoController::class, 'index']);
});
```

### Rutas Vistas
```php
Route::get('/registromaterial', function () {
    return view('registromaterial');
});

Route::get('/registrofallecido', function () {
    return view('registrofallecido');
});
```

---

## 5. COMPONENTES VUE.JS

### Componente: registromaterial.js
**Archivo:** `public/componentes/registromaterial.js`

#### Características:
- ✅ Formulario completo con validaciones
- ✅ Búsqueda integrada
- ✅ Listado de registros con edición y eliminación
- ✅ Notificaciones con Alertify
- ✅ Diseño responsive con Bootstrap 5
- ✅ Gradientes y animaciones

#### Métodos:
- `guardarRegistroMaterial()` - Guarda o actualiza un registro
- `eliminarRegistroMaterial(id)` - Elimina un registro
- `modificarRegistroMaterial(material)` - Carga un registro para editar
- `buscarRegistroMaterial()` - Activa el componente de búsqueda
- `limpiarFormulario()` - Reinicia el formulario

### Componente: registrofallecido.js
**Archivo:** `public/componentes/registrofallecido.js`

#### Características:
- ✅ Formulario completo con campos adicionales
- ✅ Selector de estado (fallecido/hospitalizado)
- ✅ Búsqueda integrada
- ✅ Listado con código de colores por estado
- ✅ Notificaciones con Alertify
- ✅ Diseño responsive con Bootstrap 5

#### Métodos:
- `guardarRegistroFallecido()` - Guarda o actualiza un registro
- `eliminarRegistroFallecido(id)` - Elimina un registro
- `modificarRegistroFallecido(registro)` - Carga un registro para editar
- `buscarRegistroFallecido()` - Activa el componente de búsqueda
- `getEstadoColor(estado)` - Retorna color según estado
- `limpiarFormulario()` - Reinicia el formulario

### Componente: busqueda_registromaterial.js
**Archivo:** `public/componentes/busqueda_registromaterial.js`

#### Campos de Búsqueda:
- Ubicación (texto)
- Descripción (texto)
- Fecha Desde (date)
- Fecha Hasta (date)
- Verificación (select)

### Componente: busqueda_registrofallecido.js
**Archivo:** `public/componentes/busqueda_registrofallecido.js`

#### Campos de Búsqueda:
- Ubicación (texto)
- Descripción (texto)
- Fecha Desde (date)
- Fecha Hasta (date)
- Estado (select)
- Verificación (select)

---

## 6. VISTAS BLADE

### Vista: registromaterial.blade.php
**Archivo:** `resources/views/registromaterial.blade.php`

#### Características:
- ✅ Navbar con color primario (azul-púrpura)
- ✅ Gradiente de fondo llamativo
- ✅ Componentes Vue integrados
- ✅ Notificaciones con Alertify
- ✅ Bootstrap 5 responsive
- ✅ Font Awesome icons

### Vista: registrofallecido.blade.php
**Archivo:** `resources/views/registrofallecido.blade.php`

#### Características:
- ✅ Navbar con color de peligro (rojo)
- ✅ Gradiente de fondo (rosa a rojo)
- ✅ Componentes Vue integrados
- ✅ Notificaciones con Alertify
- ✅ Bootstrap 5 responsive
- ✅ Font Awesome icons

### Vista: welcome.blade.php (Actualizada)
**Archivo:** `resources/views/welcome.blade.php`

#### Características:
- ✅ Página de inicio con tres módulos
- ✅ Sistema Académico
- ✅ Registro de Material
- ✅ Registro de Fallecido
- ✅ Diseño moderno con gradientes
- ✅ Tarjetas interactivas con hover effects
- ✅ Responsive design

---

## 7. DISEÑO Y CARACTERÍSTICAS

### Colores y Estilos

#### Registro de Material
- Color primario: Azul-Púrpura (#667eea a #764ba2)
- Icono: 📦 Box-open
- Gradiente: Púrpura a púrpura oscuro

#### Registro de Fallecido
- Color primario: Rojo-Rosa (#f093fb a #f5576c)
- Icono: 💔 Heartbeat
- Gradiente: Rosa a rojo

### Características Técnicas

1. **Validaciones Frontend:**
   - Campos requeridos (fecha_hora, ubicación)
   - Validación de formularios en tiempo real
   - Mensajes de error personalizados

2. **Notificaciones:**
   - ✅ Éxito al guardar
   - ❌ Error con descripción
   - ⚠️ Advertencia de búsqueda
   - ℹ️ Información

3. **Búsqueda Avanzada:**
   - Múltiples campos de búsqueda
   - Búsqueda por rango de fechas
   - Filtros por estado/verificación
   - Resultados en tiempo real

4. **CRUD Completo:**
   - CREATE: Nuevo registro
   - READ: Listar y buscar
   - UPDATE: Editar registro
   - DELETE: Eliminar con confirmación

5. **Responsivo:**
   - Diseño mobile-first
   - Bootstrap 5 grid system
   - Menú responsive
   - Tarjetas adaptables

---

## 8. CÓMO USAR

### Acceso a los Módulos

#### Opción 1: Desde la página de inicio
```
http://localhost/registromaterial
http://localhost/registrofallecido
```

#### Opción 2: Desde el Sistema Académico
```
http://localhost/sistema
```
Luego hacer clic en los enlaces del menú.

### Operaciones Básicas

#### Crear nuevo registro:
1. Llenar el formulario con los datos
2. Hacer clic en "GUARDAR"
3. Confirmar el mensaje de éxito

#### Buscar registros:
1. Hacer clic en "BUSCAR"
2. Ingresar criterios de búsqueda
3. Hacer clic en "Buscar"

#### Editar registro:
1. Hacer clic en "Editar" en el listado
2. Modificar los datos
3. Hacer clic en "GUARDAR"

#### Eliminar registro:
1. Hacer clic en "Eliminar" en el listado
2. Confirmar la eliminación
3. Se eliminará inmediatamente

---

## 9. REQUISITOS CUMPLIDOS

✅ **1. Creación de la Base de Datos (tablas) (MySQL) y modelo en Laravel**
- Migraciones creadas y ejecutadas
- Modelos Eloquent implementados
- Relaciones y casts configurados

✅ **2. Creación del controlador y funcionalidad de los métodos index, store, update y delete**
- RegistroMaterialController creado
- RegistroFallecidoController creado
- Métodos CRUD implementados
- Manejo de errores robusto

✅ **3. Creación y funcionalidad de las rutas en laravel**
- Rutas privadas para sincronización
- Rutas públicas para vistas
- Imports de controladores correctos

✅ **4. Creación y funcionalidad del componente del formulario en Vue.js**
- Componentes completos con validaciones
- Data binding bidireccional
- Métodos de CRUD funcionales
- Emisión de eventos

✅ **5. Diseño atractivo e innovador con Bootstrap y responsive design**
- Gradientes CSS modernos
- Animaciones y transiciones
- Diseño mobile-first
- Iconos Font Awesome
- Colores coherentes y atractivos

✅ **6. Búsqueda por dos o más campos del formulario**
- Búsqueda por ubicación
- Búsqueda por descripción
- Filtro por rango de fechas
- Filtro por estado/verificación
- Componentes de búsqueda separados

✅ **7. Uso de notificaciones o mensajes de información al usuario**
- Alertify.js integrado
- Mensajes de éxito (verde)
- Mensajes de error (rojo)
- Mensajes de advertencia (amarillo)
- Confirmaciones de eliminación

---

## 10. ESTRUCTURA DE ARCHIVOS

```
academica/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── RegistroMaterialController.php
│   │       └── RegistroFallecidoController.php
│   └── Models/
│       ├── RegistroMaterial.php
│       └── RegistroFallecido.php
├── database/
│   └── migrations/
│       ├── 2026_04_22_000001_create_registromateriales_table.php
│       └── 2026_04_22_000002_create_registrofallecidos_table.php
├── public/
│   └── componentes/
│       ├── registromaterial.js
│       ├── busqueda_registromaterial.js
│       ├── registrofallecido.js
│       └── busqueda_registrofallecido.js
├── resources/
│   └── views/
│       ├── registromaterial.blade.php
│       ├── registrofallecido.blade.php
│       └── welcome.blade.php (actualizado)
└── routes/
    └── web.php (actualizado)
```

---

## 11. NOTAS IMPORTANTES

- Las migraciones ya se han ejecutado correctamente
- Los componentes Vue 3 están configurados correctamente
- Alertify.js proporciona las notificaciones
- Bootstrap 5 se carga desde CDN
- Font Awesome 6.4.0 para los iconos
- Compatible con la estructura existente del proyecto

---

**Fecha de creación:** 22 de abril de 2026
**Versión:** 1.0
**Estado:** ✅ Completo y funcional
