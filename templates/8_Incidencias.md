# INCIDENCIAS E TAREFAS
- [INCIDENCIAS E TAREFAS](#incidencias-e-tarefas)
  - [1- Incidencias](#1--incidencias)
  - [2- Tarefas](#2--tarefas)


## 1- Incidencias

| #  | Descrición                                                                 | Estado    | Solución aplicada                                                                 |
| -- | -------------------------------------------------------------------------- | --------- | -------------------------------------------------------------------------------- |
| 1  | A aplicación non conectaba coa base de datos (`localhost`)                  | Resolta   | Usar o nome do servizo `mariadb` como host e un *healthcheck* con `depends_on`.   |
| 2  | Podíanse reservar dúas citas no mesmo oco horario                          | Resolta   | Detección de conflitos por intervalos en `generarSlots()`.                        |
| 3  | Os atrasos só afectaban á cita seguinte, non á cola completa               | Resolta   | Reescritura de `recalcularEstimaciones()` cun *cursor* que propaga o atraso.      |
| 4  | As horas non coincidían coa hora local                                     | Resolta   | `date_default_timezone_set('Europe/Madrid')` e `TZ` no contedor da BD.            |
| 5  | Os datos de proba quedaban obsoletos con datas fixas                       | Resolta   | *Inserts* con datas dinámicas relativas a `CURDATE()`.                            |
| 6  | Formularios vulnerables a CSRF                                             | Resolta   | Token CSRF por sesión validado con `hash_equals()` en todas as peticións POST.    |
| 7  | Posible acceso a accións doutro rol manipulando a URL                      | Resolta   | Control de acceso con `requireAuth()` / `requireRol()` e lista branca de controladores. |
| 8  | O envío real de correos de notificación non quedou implementado            | Pendente  | Queda como mellora futura (integración SMTP/Brevo).                              |

## 2- Tarefas

**Realizadas**

- [x] Configuración do entorno con Docker (Apache + PHP 8.2, MariaDB 11, phpMyAdmin).
- [x] Deseño e creación do esquema da base de datos (táboas, claves foráneas e índices).
- [x] Implementación da arquitectura MVC con autoloader e front controller.
- [x] Sistema de autenticación, rexistro e xestión de sesións.
- [x] Validacións (email, contrasinal, DNI/NIE) e medidas de seguridade (CSRF, BCRYPT, roles).
- [x] Módulo de pacientes: reserva, calendario, confirmación, reprogramación e cancelación de citas.
- [x] Módulo de profesionais: axenda, inicio/fin de consulta e perfil.
- [x] Panel de administración con API JSON para a xestión de usuarios.
- [x] Carga de datos de proba e probas funcionais por rol.

**Pendentes / futuras**

- [ ] Envío real de notificacións por correo (SMTP/Brevo).
- [ ] Actualización en tempo real da hora estimada no panel do paciente.
- [ ] Horarios e días non laborables configurables por profesional.
- [ ] Panel de estatísticas (atrasos medios, carga de traballo).
- [ ] Despregue en produción con HTTPS e variables de entorno.

[**<-Anterior**](../../README.md)
