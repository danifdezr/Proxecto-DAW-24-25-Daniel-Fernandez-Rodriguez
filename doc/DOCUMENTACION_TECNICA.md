# MedTime — Documentación técnica para la defensa

> Documento de apoyo para la presentación del Proyecto de Fin de Ciclo (DAW 2024/2025).
> Explica **qué hace cada componente** del proyecto (Bash/Docker, SQL, PHP, HTML, CSS y JavaScript)
> para poder responder cualquier pregunta del tribunal.

---

## 1. Visión general

**MedTime** es una aplicación web de **gestión de citas médicas** para una clínica. Su valor diferencial
es que calcula y muestra al paciente una **hora estimada de atención** que se actualiza según el ritmo
real de la consulta (cuántas personas tiene delante y si hay alguien siendo atendido).

Tres perfiles de usuario:

| Rol | Qué puede hacer |
|-----|-----------------|
| **PACIENTE** | Registrarse, pedir/reprogramar/cancelar citas, confirmar asistencia, ver historial y hora estimada. |
| **PROFESIONAL** | Ver su agenda del día, llamar al paciente (iniciar consulta), finalizar consulta (recalcula tiempos), ver sus pacientes. |
| **ADMIN** | CRUD completo de usuarios (pacientes, profesionales, administradores) en un panel sin recargar (AJAX). |

**Stack:** PHP 8.2 (sin frameworks) con patrón **MVC**, **MariaDB 11**, **HTML5/CSS3/JS vanilla**, todo **contenedorizado con Docker**.

---

## 2. Stack tecnológico y por qué

- **PHP 8.2 puro (sin framework):** demuestra dominio del lenguaje y del patrón MVC implementado a mano (decisión académica, no se "esconde" la lógica tras un framework).
- **MariaDB 11:** base de datos relacional. Acceso vía **PDO** con **sentencias preparadas** (previene inyección SQL).
- **Apache** con `mod_rewrite` para front controller.
- **Docker + Docker Compose:** entorno reproducible. Se levanta con un solo comando, sin instalar PHP/Apache/BD en local.
- **JS vanilla (ES6):** sin librerías externas; AJAX con `fetch`, modales propios.

---

## 3. Estructura de directorios

```
code/medtime/
├── docker-compose.yml          # Orquesta 3 servicios: web, mariadb, phpmyadmin
├── docker/
│   ├── php/Dockerfile          # Imagen PHP 8.2 + Apache + extensiones PDO
│   └── apache/000-default.conf # VirtualHost: DocumentRoot -> public/
├── sql/                        # Se ejecutan solos al crear la BD (orden alfabético)
│   ├── 01_create_database.sql  # Charset utf8mb4
│   ├── 02_schema.sql           # Tablas, claves foráneas e índices
│   └── 03_inserts.sql          # Datos de demostración
├── public/                     # ÚNICO directorio expuesto a la web (raíz del servidor)
│   ├── index.php               # Front controller (punto de entrada)
│   ├── .htaccess               # Reescritura de URLs
│   └── assets/{css,js}/        # Recursos estáticos
└── app/                        # Código de la aplicación (NO accesible directamente)
    ├── index.php               # Router + autoload + arranque
    ├── globals.php             # Constantes, zona horaria, configuración de sesión
    ├── core/Validator.php      # Validaciones (DNI/NIE)
    ├── controllers/            # Lógica de control (un controlador por rol)
    ├── models/                 # Acceso a datos (un modelo por entidad)
    │   └── vo/                 # Value Objects (objetos de transferencia)
    └── views/                  # Plantillas HTML + PHP
```

**Punto clave de seguridad:** la raíz web es `public/`. Todo el código sensible (`app/`, `sql/`, modelos, etc.)
queda **fuera** del directorio servido, así que no se puede acceder a él desde el navegador.

---

## 4. Bash / Docker / puesta en marcha

### Comando único de arranque
```bash
docker compose up -d --build
```
- `up`: crea y arranca los contenedores.
- `-d`: *detached* (en segundo plano).
- `--build`: reconstruye la imagen de PHP a partir del Dockerfile.

### URLs resultantes
- Aplicación: `http://localhost:8000`
- phpMyAdmin: `http://localhost:8888`

### Reset total (borra datos)
```bash
docker compose down -v && docker compose up -d --build
```
- `-v`: elimina también el volumen `mariadb_data`, por lo que los scripts SQL vuelven a ejecutarse.

### `docker-compose.yml` — los 3 servicios

1. **web** — PHP 8.2 + Apache.
   - Se construye desde `docker/php/Dockerfile`.
   - Puerto `8000:80` (host:contenedor).
   - Monta el proyecto en `/var/www/html` y sustituye el VirtualHost de Apache.
   - `depends_on` con `condition: service_healthy`: **no arranca hasta que la BD esté lista** (healthcheck).

2. **mariadb** — base de datos.
   - Imagen `mariadb:11`.
   - Variables de entorno crean la BD `medtime` y el usuario `medtime/medtime`.
   - Puerto `3307:3306` (3307 en el host para no chocar con un MySQL local).
   - Monta `./sql` en `/docker-entrypoint-initdb.d`: **MariaDB ejecuta esos `.sql` automáticamente la primera vez** que se crea el volumen.
   - `healthcheck`: hace `ping` a la BD cada 5s hasta que responde.

3. **phpmyadmin** — administración visual de la BD por el navegador.

### Dockerfile (`docker/php/Dockerfile`)
```dockerfile
FROM php:8.2-apache
RUN apt-get update && apt-get install -y libzip-dev zip unzip default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite
WORKDIR /var/www/html
```
- Parte de la imagen oficial PHP 8.2 con Apache.
- Instala las extensiones **PDO / pdo_mysql** (acceso a BD) y **mysqli**.
- `a2enmod rewrite`: activa `mod_rewrite`, necesario para el `.htaccess`.

### Apache VirtualHost (`docker/apache/000-default.conf`)
- `DocumentRoot /var/www/html/public` → el servidor solo sirve `public/`.
- `AllowOverride All` → permite que el `.htaccess` funcione.

---

## 5. Base de datos (SQL)

### `01_create_database.sql`
Fija el charset de la BD a `utf8mb4` (soporta tildes, ñ y emojis correctamente).

### `02_schema.sql` — modelo de datos

5 tablas. El `DROP TABLE IF EXISTS` inicial (en orden inverso a las dependencias) garantiza idempotencia.

| Tabla | Descripción | Relaciones |
|-------|-------------|------------|
| **usuario** | Datos comunes a todos: nombre, email (UNIQUE), `contrasena_hash`, `rol`, `activo`. | Tabla base. |
| **paciente** | Datos específicos: `dni` (UNIQUE), `fecha_nacimiento`, `num_historial`. | 1:1 con usuario (`id_usuario` UNIQUE, FK `ON DELETE CASCADE`). |
| **profesional** | `especialidad`, `num_colegiado`, `duracion_media_consulta_min`, `disponible`. | 1:1 con usuario. |
| **cita** | `fecha_hora_programada`, `fecha_hora_estimada`, `fecha_hora_real_inicio`, `fecha_hora_real_fin`, `estado`, `observaciones`. | N:1 con paciente y con profesional. |
| **notificacion** | `tipo`, `mensaje`, `estado`. (Modelo preparado, envío real pendiente). | N:1 con cita. |

**Decisiones de diseño a destacar:**
- **Herencia "tabla por subtipo":** `usuario` guarda lo común; `paciente`/`profesional` extienden con sus campos. Un mismo usuario tiene un rol.
- **`ON DELETE CASCADE`:** al borrar un usuario, se borran en cascada su perfil y sus citas/notificaciones. Por eso el admin puede eliminar usuarios sin dejar datos huérfanos.
- **Índices** (`CREATE INDEX`) en las claves foráneas y en `fecha_hora_programada` para acelerar las consultas de agenda.
- Los 4 estados de cita son: `PENDIENTE`, `CONFIRMADA`, `EN_CONSULTA`, `FINALIZADA`, `CANCELADA`.

### `03_inserts.sql` — datos de demostración
- 5 usuarios. Contraseña de todos: **`Medtime123`** (guardada como hash bcrypt).
- Las citas usan **fechas dinámicas** (`CURDATE()`, `DATE_ADD`, `DATE_SUB`): así la demo siempre tiene citas "hoy", "próximos días" e "historial" sin importar cuándo se levante el proyecto.

Cuentas demo:
- Paciente: `daniel@medtime.com`
- Profesional: `laura.gomez@medtime.com`
- Admin: `admin@medtime.com`

---

## 6. Arquitectura MVC y flujo de una petición

```
Navegador
   │  GET/POST  /index.php?controller=X&action=Y
   ▼
public/index.php          → solo hace require de app/index.php
   ▼
app/index.php             → arranca sesión, autoload, lee ?controller y ?action
   │                         valida contra whitelist de controladores
   ▼
Controlador (ej. PacienteController->panel())
   │  pide datos
   ▼
Modelo (ej. CitaModel)    → PDO + sentencias preparadas → MariaDB
   │  devuelve arrays / Value Objects
   ▼
Vista (ej. paciente-view.php) → genera HTML escapado
   ▼
Respuesta HTML al navegador
```

**Patrón Front Controller:** todas las peticiones entran por `index.php`; el `.htaccess` redirige cualquier
URL que no sea un archivo/carpeta real hacia `index.php`.

### `public/.htaccess`
```apache
Options -Indexes                              # No listar directorios
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f           # Si NO es un archivo real...
RewriteCond %{REQUEST_FILENAME} !-d           # ...ni un directorio real...
RewriteRule ^ index.php [QSA,L]               # ...lo gestiona index.php (QSA conserva ?query)
```

---

## 7. Núcleo de la aplicación (PHP)

### `app/globals.php`
- `date_default_timezone_set('Europe/Madrid')`.
- Constantes de rutas (`ROOT_PATH`, `MODEL_PATH`, `VIEW_PATH`, `CONTROLLER_PATH`).
- **Endurece la sesión** antes de `session_start()`:
  - `session.cookie_httponly = 1` → la cookie no es accesible por JavaScript (protege ante XSS).
  - `session.cookie_samesite = Strict` → protege ante CSRF entre sitios.
  - `session.use_strict_mode = 1` → rechaza IDs de sesión no generados por el servidor (anti session fixation).

### `app/index.php` — router + autoload
- **Autoload** (`spl_autoload_register`): convierte el namespace de una clase (`app\controllers\PacienteController`) en una ruta de archivo y la incluye. Evita escribir `require` manuales.
- **Whitelist de controladores:** solo se aceptan controladores de una lista cerrada. Si llega un `?controller` desconocido → `ErrorController` (404). Esto evita que se instancie cualquier clase arbitraria.
- Lee `?controller` (por defecto `AuthController`) y `?action` (por defecto `showLogin`).
- Comprueba con `class_exists` y `method_exists` y llama a `$objeto->$action()`.
- Todo envuelto en `try/catch (\Throwable)`: cualquier error se registra (`error_log`) y se muestra el 404 (no se filtran trazas al usuario).

### `app/core/Validator.php`
- `dniValido()`: valida **DNI y NIE** españoles.
  - Para NIE sustituye la letra inicial (X→0, Y→1, Z→2).
  - Comprueba formato `8 dígitos + letra`.
  - Verifica la **letra de control por módulo 23** (`LETRAS[numero % 23]`).

### `app/models/Model.php` (clase base de modelos)
- Método `getConnection()`: crea la conexión **PDO** a MariaDB (`host=mariadb`, charset `utf8mb4`).
- Si falla la conexión, lo registra y muestra un mensaje genérico (no expone credenciales).
- Todos los modelos heredan de aquí.

---

## 8. Controladores (PHP)

### `Controller.php` (clase base de todos los controladores)
Métodos protegidos reutilizados por el resto:
- **`__construct`**: instancia la `View` y **genera un token CSRF** (`random_bytes(32)`) en sesión si no existe.
- **`redirect()`**: redirección HTTP a `controller/action` con parámetros (URL-encoded). Devuelve `never` y hace `exit`.
- **`validateCsrfToken()`**: compara el token del formulario con el de sesión usando `hash_equals` (comparación en tiempo constante). Si no coincide → HTTP 403.
- **`requireAuth()`**: si no hay usuario en sesión, redirige al login.
- **`requireGuest()`**: si ya hay sesión, no deja ver login/registro.
- **`requireRol(...$roles)`**: control de acceso por rol. Si el rol no está permitido, redirige.
- **`consumeFlash()`**: lee y borra los mensajes flash (`error`, `success`, `old`) guardados en sesión. Patrón **Post/Redirect/Get** para que los mensajes sobrevivan a una redirección y no se repitan al refrescar.

### `AuthController.php` — autenticación
- `showLogin()` / `showRegister()`: muestran formularios (solo si no hay sesión).
- **`login()`**:
  - Valida método POST y token CSRF.
  - Busca usuario por email.
  - `password_verify()` contra el hash bcrypt (nunca compara contraseñas en claro).
  - Comprueba que la cuenta esté `activo`.
  - `session_regenerate_id(true)` tras login (anti session fixation).
  - Guarda en sesión solo datos no sensibles (id, nombre, email, rol).
- **`register()`**: registra **pacientes**. Valida campos, formato de email, longitud de contraseña (≥8), coincidencia, DNI válido y email no duplicado. Crea el usuario con `password_hash(..., PASSWORD_BCRYPT)` y luego el paciente con un `num_historial` autogenerado (`HIST-000001`).
- **`logout()`**: vacía `$_SESSION`, borra la cookie de sesión y destruye la sesión.

### `MainController.php` — distribuidor
- `listMain()`: tras el login, redirige al panel que corresponde según el rol (PROFESIONAL → su panel, ADMIN → panel admin, resto → panel paciente).

### `PacienteController.php` — núcleo funcional del paciente
- **`panel()`**: panel principal. Calcula próxima cita, citas activas, visitas finalizadas, historial, y —si la próxima cita es hoy— la **info de cola** (pacientes delante + si hay alguien en consulta).
- **`listCitas()`**: lista todas las citas con filtro por estado (`?estado=`) y contadores por estado.
- **`confirmarCita()`**: solo permite confirmar citas `PENDIENTE` propias y **únicamente el mismo día**.
- **`cancelarCita()`**: cancela citas propias que no estén ya `FINALIZADA`/`CANCELADA`.
- **`nuevaCita()`**: buscador de profesionales (`?q=`).
- **`disponibilidad()`**: genera el **calendario de 35 días** y los **huecos (slots)** del día elegido.
- **`guardarCita()`**: valida que el hueco siga libre, que sea futuro, y crea la cita (estado `PENDIENTE`).
- **`mostrarReprogramar()` / `guardarReprogramacion()`**: igual que pedir cita, pero sobre una cita existente; exige que la nueva hora sea ≥1h en el futuro y, si es el mismo día, obliga a elegir hora distinta.
- **`perfil()` / `actualizarPerfil()` / `cambiarContrasena()`**: gestión de la cuenta.
- **Helpers privados** (la "salsa" del proyecto):
  - **`generarSlots()`**: dado un profesional, fecha y duración de consulta, calcula los huecos libres.
    - Horario: 09:00–14:00 y 16:00–20:00. **Domingos cerrado**.
    - Trocea el horario en bloques de `duración` minutos.
    - Descarta huecos pasados (exige ≥1h de antelación) y los que **solapan** con citas existentes (comprobación de intervalos `inicio < fin_existente && fin > inicio_existente`).
  - **`generarSemanas()`**: construye la rejilla del calendario (empieza en lunes) y, con caché, cuenta cuántos huecos tiene cada día (para pintar el número en cada celda).

### `ProfesionalController.php` — agenda del profesional
- **`listProfesional()`**: panel con las citas de hoy, próximas, la cita en consulta y contadores por estado.
- **`listCitas()` / `listPacientes()`**: listados (con filtros y agregados).
- **`llamarPaciente()`**: inicia la consulta (`EN_CONSULTA`). **Solo permite una consulta activa a la vez**: si ya hay una en curso, no deja llamar a otro.
- **`finalizarConsulta()`**: marca la cita como `FINALIZADA` y **dispara el recálculo de estimaciones** (`recalcularEstimaciones`) para el resto de la cola del día.
- **`perfil()` / `actualizarPerfil()` / `cambiarContrasena()`**: el profesional puede editar su especialidad, duración de consulta y disponibilidad.

### `AdminController.php` — API JSON + CRUD AJAX
A diferencia de los demás, **responde en JSON** (helper `json()`), porque el panel admin es dinámico.
- **`panel()`**: renderiza la vista (tabla vacía que se rellena por AJAX).
- **`getUsuarios()`**: devuelve todos los usuarios (con datos de profesional si aplica) en JSON.
- **`crearUsuario()` / `actualizarUsuario()` / `eliminarUsuario()`**: CRUD. Validan rol, email único, contraseña, DNI (si es paciente). Crean también el registro `paciente`/`profesional` según el rol.
- **Reglas de seguridad de negocio:**
  - No puedes **eliminar tu propia cuenta**.
  - No puedes eliminar el **único administrador** del sistema (`countByRol('ADMIN') <= 1`).
- Todas las acciones exigen rol ADMIN, método POST y token CSRF.

### `ErrorController.php`
- `pageNotFound()`: cabecera HTTP 404 + vista `page_not_found-view.php`.

---

## 9. Modelos (PHP) — acceso a datos

Todos extienden `Model`, usan **PDO con sentencias preparadas** (`prepare` + `bindValue`), envuelven en
`try/catch (PDOException)` con `error_log`, y cierran la conexión en `finally` (`$db = null`).

### `UsuarioModel.php`
`getUsuarioById`, `getUsuarioByEmail`, `getUsuariosConProfesional` (LEFT JOIN para el panel admin),
`crearUsuario`, `actualizarPerfil`, `actualizarUsuario`, `cambiarContrasena`, `eliminarUsuario`,
`countByRol`. Convierte filas a `UsuarioVo`.

### `PacienteModel.php`
`getPacienteByIdUsuario` (clave: une `paciente`+`usuario`), `crearPaciente`,
`getPacientesByProfesional` (con `COUNT`/`MAX`/`MIN` para total de citas, última y próxima cita).

### `ProfesionalModel.php`
`getProfesionalById`, `getProfesionalByIdUsuario`, `buscarProfesionales` (búsqueda `LIKE` por nombre/apellidos/especialidad, **solo disponibles**), `crearProfesional`, `actualizarDatosProfesional`.

### `CitaModel.php` — el modelo más importante
Define dos plantillas SQL reutilizables: `sqlConProfesional()` (citas vistas por el paciente, con datos del médico) y `sqlConPaciente()` (citas vistas por el médico, con datos del paciente).

Consultas destacadas:
- `getProximaCitaPaciente`, `getCitasProximasPaciente`, `getHistorialPaciente`, `getCitasByPacienteConProfesional`.
- `getCitasByProfesionalHoy`, `...Proximas`, `getCitaEnConsulta`, `getPacientesDelante`.
- `crearCita`, `cambiarEstadoCita`, `iniciarCita`, `finalizarCita`, `reprogramarCita`.

**Algoritmo estrella — `recalcularEstimaciones()`** (el corazón del proyecto):
1. Recupera todas las citas del profesional ese día (no canceladas), ordenadas por hora.
2. Mantiene un **cursor** = momento en que el médico queda libre.
3. Por cada cita:
   - Si está **FINALIZADA**, avanza el cursor al máximo entre su hora teórica de fin y su fin real.
   - Si está **pendiente/confirmada**, su hora estimada = `max(hora_programada, cursor)`. Si la estimada es mayor que la programada (hay retraso), la guarda en `fecha_hora_estimada`; si no, la deja en `NULL` (va en hora). Luego avanza el cursor sumando la duración media.
4. Persiste las nuevas estimaciones.

Así, cuando un médico finaliza una consulta antes o después de lo previsto, **toda la cola se reajusta** y el paciente ve su nueva hora estimada y el retraso.

`getPacientesDelante()`: cuenta cuántas citas hay antes (misma fecha, hora anterior, no finalizadas/canceladas) → alimenta el widget de cola.

### `NotificacionModel.php`
Modelo preparado (`getNotificacionById`, `getNotificaciones`) con un `// TODO`: el **envío real de notificaciones** queda como línea de mejora futura.

### `vo/` — Value Objects
`UsuarioVo`, `PacienteVo`, `ProfesionalVo`, `CitaVo`, `NotificacionVo`. Son **objetos de transferencia de datos** (DTO): propiedades tipadas + getters/setters. Los modelos convierten cada fila de la BD (`rowToVo`) en un objeto, de forma que el resto del código trabaja con objetos en lugar de arrays sueltos. Ventaja: tipado, autocompletado y desacople de la estructura de la tabla.

---

## 10. Vistas (HTML + PHP)

Las vistas son plantillas PHP que generan HTML. Patrón común:
- Recogen los datos del array `$data` al inicio (con `?? null`).
- HTML5 semántico: `<header>`, `<main>`, `<nav>`, `<section>`, `<aside>`, `<footer>`.
- **Accesibilidad:** `lang="es"`, `aria-*`, `role`, enlace "Saltar al contenido", clases `sr-only` (solo lectores de pantalla).
- **Seguridad:** **toda** salida dinámica pasa por `htmlspecialchars()` (anti XSS).
- **Token CSRF** incrustado como `<input hidden>` en cada formulario y como `<meta name="csrf-token">` en las páginas con AJAX.
- **Cache busting:** los `<link>`/`<script>` llevan `?v=<?= filemtime(...) ?>`; al cambiar un archivo CSS/JS cambia la URL y el navegador no sirve una versión cacheada antigua.

Vistas principales:
- `login-view.php`, `register-view.php`: formularios de autenticación.
- `paciente-view.php`: panel del paciente (próxima cita, stats, **widget de cola**, alerta de retraso, historial).
- `paciente-citas-view.php`, `paciente-perfil-view.php`.
- `nueva-cita-view.php`: buscador de profesionales.
- `disponibilidad-view.php` / `reprogramar-view.php`: **calendario interactivo** + rejilla de huecos + formulario de confirmación.
- `profesional-view.php`, `profesional-citas-view.php`, `profesional-pacientes-view.php`, `profesional-perfil-view.php`.
- `admin-view.php`: cabecera + buscador + tabla que se rellena por AJAX.
- `page_not_found-view.php`: error 404.
- `View.php`: clase que hace `include` de la plantilla (`showView('paciente', $data)` → incluye `paciente-view.php`).

### El calendario (`disponibilidad-view.php`) — sin JS
Es **navegación por enlaces** (server-side), no JavaScript:
- Cada día con huecos es un `<a>` con `&fecha=...`. Al pulsarlo, recarga la página y el controlador genera los huecos de ese día.
- Cada hora libre es otro `<a>` con `&hora=...`. Al elegirla, aparece el formulario de confirmación.
- Días sin huecos o domingos se pintan deshabilitados. Cada celda muestra el nº de huecos.

---

## 11. CSS

Organización por archivos (≈4.200 líneas en total):

| Archivo | Uso |
|---------|-----|
| `cssPrincipal.css` | **Base compartida**: variables, reset, layout, header, footer, botones, tarjetas, badges de estado, alertas. |
| `paciente.css` | Estilos del paciente (panel, calendario, slots, widget de cola). |
| `profesional.css` | Estilos del profesional (agenda, cola, tarjetas de cita). |
| `admin.css` | Tabla de usuarios, badges de rol, avatares, búsqueda. |
| `login.css` / `registro.css` | Páginas de autenticación. |
| `modal.css` | Sistema de modales (overlay, animaciones, formularios en modal). |

Técnicas a destacar:
- **Variables CSS** (`:root { --color-primary: ... }`): paleta y radios/sombras centralizados → tema coherente y fácil de cambiar.
- **Sistema de estados por color**: cada estado de cita tiene su trío bg/border/text (pending, confirmed, done, cancelled).
- **Responsive** con `box-sizing: border-box`, layouts flexibles (Grid/Flex) y media queries.
- Sin frameworks CSS: todo escrito a mano.

---

## 12. JavaScript (vanilla ES6)

Cuatro archivos, sin dependencias externas:

### `modal.js` — sistema de modales reutilizable (patrón módulo/IIFE)
Se carga **el primero**. Expone `window.Modal` con:
- `Modal.confirm({...})`: diálogo de confirmación (con variante "peligro").
- `Modal.info({...})`: diálogo informativo (p. ej. detalle de cita).
- `Modal.form({...})`: genera un **formulario dinámico** a partir de una definición de campos (text, email, select, checkbox…), con validación y manejo de errores.
- `Modal._escape()`: escape HTML (anti XSS también en cliente).
- Gestiona overlay, foco, tecla `Escape` y cierre al hacer click fuera.

### `admin.js` — panel de administración (SPA ligera)
- `ajax()`: wrapper sobre `fetch` que añade el **token CSRF** (leído del `<meta>`) a las peticiones POST.
- `loadUsuarios()`: pide `getUsuarios` y pinta la tabla (`renderFila`/`renderUsuarios`).
- `abrirCrear()` / `abrirEditar()`: abren `Modal.form` con campos que **cambian según el rol** (campos de profesional/paciente se muestran u ocultan con `bindRolToggle`).
- `confirmarEliminar()`: usa `Modal.confirm` y llama a `eliminarUsuario`.
- Búsqueda en vivo: filtra filas de la tabla por texto sin recargar.
- Todo el CRUD es **AJAX** → la página nunca se recarga.

### `paciente.js`
- Intercepta botones/formularios con `data-confirm` y muestra un modal de confirmación (con **fallback** a `window.confirm` si el modal no cargó). Así, cancelar una cita pide confirmación.
- `initDetailModals()`: al pulsar una cita (`data-cita-detalle`), abre un modal con su detalle (estado con emoji, hora estimada, observaciones).

### `profesional.js`
- Mismo patrón de confirmación y modales de detalle.
- **`initPollPendientes()`**: cada 60 s hace `fetch` de la propia página y actualiza solo el **contador de pendientes** del contador en cabecera (auto-refresco ligero sin recargar).

---

## 13. Seguridad — resumen para el tribunal

| Amenaza | Mitigación en MedTime |
|---------|----------------------|
| **Inyección SQL** | PDO con **sentencias preparadas** y `bindValue` en todas las consultas. |
| **XSS** | `htmlspecialchars()` en toda salida del servidor + `_escape()` en cliente. |
| **CSRF** | Token aleatorio por sesión (`random_bytes`), incrustado en cada formulario y validado con `hash_equals`. Cookie `SameSite=Strict`. |
| **Contraseñas** | Hash **bcrypt** (`password_hash` / `password_verify`); nunca se guardan en claro. |
| **Robo de cookie de sesión** | `cookie_httponly` (no accesible por JS). |
| **Session fixation** | `session_regenerate_id(true)` al iniciar sesión + `use_strict_mode`. |
| **Acceso a código fuente** | Raíz web = `public/`; `app/` y `sql/` quedan fuera. `.htaccess` con `Options -Indexes`. |
| **Escalada de privilegios** | `requireAuth` + `requireRol` en cada acción; whitelist de controladores. |
| **Reglas de negocio** | No borrar la propia cuenta ni el único admin; una sola consulta activa por profesional; confirmar solo el día de la cita. |

---

## 14. Posibles preguntas

- **¿Por qué sin framework?** Para demostrar comprensión real del MVC y del ciclo petición-respuesta, implementándolo a mano.
- **¿Cómo se calcula la hora estimada?** Algoritmo `recalcularEstimaciones`: un cursor acumula la hora en que el médico queda libre; cada cita pendiente hereda `max(hora programada, cursor)` y suma la duración media. Se dispara al finalizar cada consulta.
- **¿Cómo evitas dobles reservas en el mismo hueco?** `generarSlots` solo ofrece huecos que no solapan con citas existentes, y `guardarCita` **revalida** que el hueco siga libre justo antes de insertar.
- **¿Por qué los datos demo usan `CURDATE()`?** Para que la demostración tenga siempre citas de hoy, futuras e historial, independientemente del día.
- **¿Qué pasa si MariaDB tarda en arrancar?** El servicio `web` espera al `healthcheck` de la BD (`depends_on: condition: service_healthy`).
- **¿Cómo se actualiza el panel del paciente en tiempo real?** Hoy el widget de cola se recalcula al recargar; el profesional tiene un *polling* cada 60 s. La actualización en vivo del paciente es una **línea de mejora** declarada.
- **¿Qué quedó como mejora futura?** Envío real de notificaciones (email/push), tiempo real en el panel del paciente, horarios configurables por profesional y panel de estadísticas.

---

## 15. Documentación complementaria del proyecto

En `templates/` están las memorias del proyecto: Anteproyecto, Empresa, Análisis, Diseño,
Codificación y pruebas, Implantación, Referencias e Incidencias. En `doc/img/` están el prototipo,
el modelo relacional y capturas del resultado.
</content>
</invoke>
