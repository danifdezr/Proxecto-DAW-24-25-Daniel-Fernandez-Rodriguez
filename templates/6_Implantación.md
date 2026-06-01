# FASE DE IMPLANTACIÓN

- [FASE DE IMPLANTACIÓN](#fase-de-implantación)
  - [1- Manual técnico](#1--manual-técnico)
    - [1.1- Instalación](#11--instalación)
    - [1.2- Administración do sistema](#12--administración-do-sistema)
  - [2- Manual de usuario](#2--manual-de-usuario)
  - [3- Melloras futuras](#3--melloras-futuras)

## 1- Manual técnico

### 1.1- Instalación

A aplicación distribúese **contedorizada con Docker**, polo que non require instalar manualmente PHP, Apache nin a base de datos. Calquera persoa pode descargar o código e telo funcionando con poucos comandos.

**Requirimentos de software**

- [Docker](https://www.docker.com/) e Docker Compose.
- [Git](https://git-scm.com/) para clonar o repositorio.
- Un navegador web moderno.

> **En Windows**: Docker Desktop apóiase en **WSL 2** (Windows Subsystem for Linux). Se non o tes instalado, executa nunha terminal con permisos de administrador:
> ```bash
> wsl --install
> ```
> Tras a instalación reinicia o equipo e arranca Docker Desktop antes de continuar cos pasos seguintes.

**Requirimentos de hardware**

- Equipo con capacidade para executar contedores Docker (recoméndanse 4 GB de RAM ou máis).
- Non require infraestrutura especial; pode despregarse nun equipo local ou nun servidor/VPS na nube.

**Software co que interacciona**

A aplicación lévanta tres contedores coordinados mediante `docker-compose.yml`:

| Servizo      | Imaxe                | Porto local | Función                          |
| ------------ | -------------------- | ----------- | -------------------------------- |
| `web`        | PHP 8.2 + Apache     | `8000`      | Aplicación MedTime               |
| `mariadb`    | MariaDB 11           | `3307`      | Base de datos                    |
| `phpmyadmin` | phpMyAdmin           | `8888`      | Administración da base de datos  |

**Pasos de instalación**

1. Clonar o repositorio:
   ```bash
   git clone <url-do-repositorio>
   cd code/medtime
   ```
2. Levantar o entorno:
   ```bash
   docker compose up -d --build
   ```
3. Acceder á aplicación no navegador en `http://localhost:8000`.
4. (Opcional) Administrar a base de datos en `http://localhost:8888`.

**Carga inicial de datos**

Os scripts da carpeta `sql/` execútanse **automaticamente** a primeira vez que se crea o contedor da base de datos (volume `mariadb_data`), na seguinte orde:

- `01_create_database.sql` — creación da base de datos.
- `02_schema.sql` — táboas, claves foráneas e índices.
- `03_inserts.sql` — datos de proba con datas dinámicas relativas a hoxe.

Se se desexa partir de cero, basta con eliminar o volume: `docker compose down -v` e volver levantar o entorno.

**Usuarios da aplicación**

Os datos de proba inclúen os tres roles. O contrasinal de todas as contas de demostración é `Medtime123`:

| Rol         | Email                       |
| ----------- | --------------------------- |
| Paciente    | `daniel@medtime.com`        |
| Profesional | `laura.gomez@medtime.com`   |
| Profesional | `carlos.perez@medtime.com`  |
| Administrador | `admin@medtime.com`       |


**Diagrama de despregue**

O despregue mantense conforme ao deseñado na fase anterior (arquitectura cliente-servidor), engadindo a capa de contedorización: navegador → contedor `web` (Apache + PHP) → contedor `mariadb`, todos na mesma rede interna de Docker Compose.

### 1.2- Administración do sistema

Tarefas a realizar unha vez o sistema está en funcionamento:

- **Copias de seguridade da base de datos**: realizar volcados periódicos con `mysqldump` (ou `mariadb-dump`) contra o contedor `mariadb`. Exemplo:
  ```bash
  docker exec medtime-db mariadb-dump -uroot -proot medtime > backup_medtime.sql
  ```
- **Copias de seguridade do sistema**: o código está versionado en Git; os datos persistentes residen no volume `mariadb_data`, que tamén debe incluírse na política de copias.
- **Xestión de usuarios**: realízase dende o **panel de administración** (rol `ADMIN`), que permite dar de alta, editar e desactivar/eliminar usuarios e profesionais.
- **Xestión da seguridade**: control de acceso por roles, tokens CSRF e contrasinais cifrados (BCRYPT). Recoméndase activar **HTTPS** mediante un proxy inverso nun despregue real.
- **Xestión de incidencias**: os erros de aplicación e de base de datos rexístranse mediante `error_log()`, consultables nos logs de Apache do contedor (`docker logs medtime-web`). Os accesos non autorizados quedan bloqueados polo control de roles e a validación de tokens.

## 2- Manual de usuario

A aplicación está deseñada para ser **intuitiva**, polo que non require formación específica. Distínguense tres perfís:

**Paciente**

- Rexistrarse e iniciar sesión.
- Reservar unha cita: buscar profesional → elixir día no calendario → elixir hora dispoñible → confirmar.
- Consultar as súas citas e o seu estado, así como o **historial** de visitas.
- Ver a **hora estimada** de atención e cantos pacientes ten diante o día da cita.
- Confirmar a asistencia (o mesmo día), reprogramar ou cancelar unha cita.
- Editar o seu perfil e cambiar o contrasinal.

**Profesional**

- Ver a **axenda do día** e as citas próximas.
- **Chamar ao paciente** (iniciar consulta) e **finalizar a consulta**, o que actualiza automaticamente os tempos estimados do resto da cola.
- Consultar os seus pacientes e o estado das súas citas.
- Editar o seu perfil (especialidade, duración media de consulta, dispoñibilidade).

**Administrador**

- Xestionar todos os usuarios do sistema (alta, edición, baixa) dende un panel dinámico.
- Acceder tamén ás vistas de paciente cando sexa necesario.
- Mediante o administrador é a única maneira (dentro da app) de crear un usuario profesional.

## 3- Melloras futuras

- **Envío real de notificacións**: completar a integración SMTP/Brevo para enviar por correo electrónico os recordatorios e avisos de cambio na hora estimada, aproveitando a táboa `notificacion` xa existente.
- **Actualización en tempo real**: refrescar automaticamente a hora estimada e a cola no panel do paciente (mediante *polling* AJAX ou WebSockets) sen necesidade de recargar.
- **Horarios configurables por profesional**: permitir que cada profesional defina os seus propios tramos horarios e días non laborables, no canto dos fixos actuais.
- **Estatísticas avanzadas**: panel con atrasos medios, carga de traballo e tempos reais de consulta para a dirección da clínica.
- **Despregue en produción**: configuración de HTTPS, variables de entorno para as credenciais e adaptación a un provedor na nube.
- **Aplicación móbil / PWA**: versión instalable para que os pacientes reciban avisos *push* no móbil.

[**<-Anterior**](../../README.md)
