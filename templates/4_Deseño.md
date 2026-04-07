# FASE DE DESEÑO

- [FASE DE DESEÑO](#fase-de-deseño)
  - [1- Diagrama da arquitectura](#1--diagrama-da-arquitectura)
  - [2- Casos de uso](#2--casos-de-uso)
      - [Casos de uso principais](#casos-de-uso-principais)
  - [3- Diagrama de Base de Datos](#3--diagrama-de-base-de-datos)
      - [Entidade/relación](#entidaderelación)
      - [Relacional](#relacional)
  - [4- Deseño de interface de usuarios](#4--deseño-de-interface-de-usuarios)


## 1- Diagrama da arquitectura

A aplicación seguirá unha arquitectura web clásica cliente-servidor, na que o paciente ou o persoal da clínica acceden desde o navegador ao frontend, este comunícase co backend en PHP e o backend realiza as operacións contra a base de datos MySQL. Ademais, o sistema poderá usar un servizo de correo electrónico para o envío de recordatorios e avisos de atraso.

```mermaid
flowchart LR
    A[Paciente / Persoal clínica] --> B[Navegador web]
    B --> C[Frontend<br/>HTML5 + CSS3 + JavaScript]
    C --> D[Backend<br/>PHP 8.2 MVC]
    D --> E[(MySQL)]
    D --> F[Servizo de correo<br/>SMTP / Brevo]
    E --> D
    F --> A
```

## 2- Casos de uso

A continuación represéntanse os principais casos de uso do sistema, diferenciando os actores que interveñen: paciente, persoal da clínica e administrador.
```mermaid
flowchart TD
    P[Paciente]
    C[Persoal da clínica]
    A[Administrador]

    UC1((Rexistrarse))
    UC2((Iniciar sesión))
    UC3((Reservar cita))
    UC4((Consultar citas))
    UC5((Ver hora estimada))
    UC6((Cancelar ou modificar cita))
    UC7((Actualizar estado da cita))
    UC8((Xestionar axenda))
    UC9((Consultar estatísticas))
    UC10((Xestionar usuarios))
    UC11((Configurar sistema))
    UC12((Enviar notificacións))

    P --> UC1
    P --> UC2
    P --> UC3
    P --> UC4
    P --> UC5
    P --> UC6

    C --> UC2
    C --> UC7
    C --> UC8
    C --> UC9
    C --> UC12

    A --> UC2
    A --> UC8
    A --> UC9
    A --> UC10
    A --> UC11
```

#### Casos de uso principais

| Caso de uso                | Actor principal         | Descrición                                                      |
| -------------------------- | ----------------------- | --------------------------------------------------------------- |
| Rexistrarse                | Paciente                | Crear unha conta na aplicación.                                 |
| Iniciar sesión             | Todos                   | Acceder ao sistema segundo o rol correspondente.                |
| Reservar cita              | Paciente                | Escoller profesional, data e hora dispoñible.                   |
| Consultar citas            | Paciente                | Ver citas activas e o seu estado.                               |
| Ver hora estimada          | Paciente                | Consultar a estimación actualizada da hora real de atención.    |
| Cancelar ou modificar cita | Paciente                | Cambiar ou anular unha cita existente.                          |
| Actualizar estado da cita  | Persoal da clínica      | Marcar cita como pendente, en curso ou finalizada.              |
| Xestionar axenda           | Persoal / Administrador | Organizar as citas e a dispoñibilidade dos profesionais.        |
| Consultar estatísticas     | Persoal / Administrador | Revisar atrasos medios, carga de traballo e tempos de consulta. |
| Xestionar usuarios         | Administrador           | Crear, editar ou bloquear usuarios do sistema.                  |
| Configurar sistema         | Administrador           | Definir parámetros xerais da aplicación.                        |
| Enviar notificacións       | Sistema / Persoal       | Avisar de recordatorios e cambios na hora estimada.             |

## 3- Diagrama de Base de Datos

#### Entidade/relación

```mermaid
flowchart LR
    PAC["PACIENTE<br/>----------------<br/>id_paciente (PK)<br/>id_usuario (FK)<br/>dni<br/>data_nacemento<br/>num_historial"]
    USU["USUARIO<br/>----------------<br/>id_usuario (PK)<br/>nome<br/>apelidos<br/>email<br/>telefono<br/>contrasinal_hash<br/>rol<br/>data_alta<br/>activo"]
    PRO["PROFESIONAL<br/>----------------<br/>id_profesional (PK)<br/>id_usuario (FK)<br/>especialidade<br/>num_colexiado<br/>duracion_media_consulta_min<br/>dispoñible"]
    CIT["CITA<br/>----------------<br/>id_cita (PK)<br/>id_paciente (FK)<br/>id_profesional (FK)<br/>data_hora_programada<br/>data_hora_estimada<br/>data_hora_real_inicio<br/>data_hora_real_fin<br/>estado<br/>observacions<br/>creada_en"]
    NOT["NOTIFICACION<br/>----------------<br/>id_notificacion (PK)<br/>id_cita (FK)<br/>tipo<br/>mensaxe<br/>data_envio<br/>estado_envio"]

    R1{"TEN"}
    R2{"TEN"}
    R3{"RESERVA"}
    R4{"ATENDE"}
    R5{"XERA"}

    USU ---|1:1| R1
    R1 ---|1:1| PAC

    USU ---|1:1| R2
    R2 ---|1:1| PRO

    PAC ---|1:N| R3
    R3 ---|1:1| CIT

    PRO ---|1:N| R4
    R4 ---|1:1| CIT

    CIT ---|1:N| R5
    R5 ---|1:1| NOT

```

#### Relacional
![Modelo Relacional](../doc/img/RelacionalMedTime.png)

## 4- Deseño de interface de usuarios

![Prototipo](../doc/img/Prototipo_MedTime.png)

[**<-Anterior**](../../README.md)
