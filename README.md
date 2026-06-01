# MedTime — Xestión intelixente de citas médicas

- [MedTime — Xestión intelixente de citas médicas](#medtime--xestión-intelixente-de-citas-médicas)
  - [Taboleiro do proxecto](#taboleiro-do-proxecto)
  - [Descrición](#descrición)
  - [Instalación / Posta en marcha](#instalación--posta-en-marcha)
  - [Uso](#uso)
  - [Sobre o autor](#sobre-o-autor)
  - [Licenza](#licenza)
  - [Índice](#índice)
  - [Guía de contribución](#guía-de-contribución)
  - [Links](#links)

## Taboleiro do proxecto

**Estado: finalizado** ✅ — Proxecto de Fin de Ciclo do CS de *Desenvolvemento de Aplicacións Web (DAW)*, curso 2024/2025.

## Descrición

**MedTime** é unha aplicación web para a **xestión de citas nunha clínica** que pon o foco nun problema cotián: a **incerteza nas salas de espera**. Ademais de permitir reservar, consultar e xestionar citas, MedTime calcula e amosa ao paciente unha **hora estimada de atención** que se actualiza segundo o ritmo real da consulta, indicándolle cantas persoas ten diante e se hai alguén sendo atendido nese momento.

A aplicación organiza tres perfís de usuario:

- **Paciente**: rexístrase, reserva citas escollendo profesional, día e hora dispoñible, consulta o seu historial e a hora estimada, e pode confirmar, reprogramar ou cancelar.
- **Profesional**: xestiona a súa axenda diaria, chama ao paciente e finaliza a consulta; ao facelo, MedTime **recalcula automaticamente os tempos de espera** do resto da cola.
- **Administrador**: xestiona todos os usuarios do sistema dende un panel dinámico.

Está desenvolvida en **PHP 8.2** seguindo o patrón **Modelo-Vista-Controlador (MVC)** sen frameworks externos, con **MariaDB** como base de datos, **HTML5, CSS3 e JavaScript** no frontend, e todo o entorno **contedorizado con Docker** para que poña en marcha cun único comando.

## Instalación / Posta en marcha

A aplicación está totalmente **contedorizada con Docker**, polo que non é necesario instalar PHP, Apache nin a base de datos no equipo.

**Requisitos previos**

- [Docker](https://www.docker.com/) e Docker Compose.
- [Git](https://git-scm.com/).
- En **Windows**, Docker Desktop necesita **WSL 2**. Se non o tes, executa nunha terminal de administrador `wsl --install`, reinicia e arranca Docker Desktop.

**Posta en marcha (un só comando)**

```bash
git clone <url-do-repositorio>
cd Proxecto-DAW-24-25-Daniel-Fernandez-Rodriguez/code/medtime
docker compose up -d --build
```

Cando rematen de levantarse os contedores, abre no navegador:

- Aplicación: <http://localhost:8000>
- phpMyAdmin (administración da BD): <http://localhost:8888>

Os scripts de `sql/` execútanse automaticamente a primeira vez, creando o esquema e uns **datos de proba**. O contrasinal de todas as contas de demostración é `Medtime123`:

| Rol           | Email                      |
| ------------- | -------------------------- |
| Paciente      | `daniel@medtime.com`       |
| Profesional   | `laura.gomez@medtime.com`  |
| Administrador | `admin@medtime.com`        |

> Para empezar de cero (borrando os datos): `docker compose down -v && docker compose up -d --build`.

## Uso

- **Como paciente**: inicia sesión, pulsa en *Nova cita*, busca o profesional, elixe un día no calendario e unha hora libre. No teu panel verás a próxima cita e, o mesmo día, **a hora estimada actualizada** e cantos pacientes tes diante. Podes confirmar a asistencia, reprogramar ou cancelar.
- **Como profesional**: no panel tes a axenda do día. Pulsa *Chamar paciente* para iniciar a consulta e *Finalizar* ao rematar; os tempos estimados do resto de citas axústanse sós.
- **Como administrador**: dende o panel podes crear, editar e eliminar usuarios (pacientes, profesionais e administradores) sen recargar a páxina.

## Sobre o autor

**Daniel Fernández Rodríguez**, estudante do Ciclo Superior de *Desenvolvemento de Aplicacións Web (DAW)*.

Este proxecto nace da motivación de aplicar o desenvolvemento web full-stack a un problema real e pouco atendido —a xestión da espera nas consultas médicas—, traballando o backend en PHP, o modelado de datos en SQL e a contedorización con Docker.

- 📧 Contacto: [danifdezrbach@gmail.com](mailto:danifdezrbach@gmail.com)

## Licenza

Este proxecto distribúese baixo a **licenza MIT**. Consulta o ficheiro [LICENSE](LICENSE.md) para os detalles completos. Ao tratarse dunha licenza libre e permisiva, autorízase o uso, copia, modificación e distribución da obra, mantendo o aviso de copyright e da licenza.

## Índice

1. [Anteproyecto](templates/1_Anteproxecto.md)
2. [Empresa](templates/2_Empresa.md)
3. [Análise](templates/3_Analise.md)
4. [Deseño](templates/4_Deseño.md)
5. [Codificación e probas](templates/5_Codificacion_e_probas.md)
6. [Implantación](templates/6_Implantación.md)
7. [Referencias](templates/7_Referencias.md)
8. [Incidencias](templates/8_Incidencias.md)

## Guía de contribución

As contribucións son benvidas. Para colaborar:

1. Fai un *fork* do repositorio e crea unha rama descritiva (`git checkout -b mellora-notificacions`).
2. Mantén o estilo do código existente (PHP con patrón MVC, sentencias preparadas e escape da saída).
3. Proba os cambios no entorno Docker antes de propoñelos.
4. Abre un *Pull Request* describindo o cambio e o motivo.

Algunhas liñas abertas onde axudar: envío real de notificacións por correo, actualización en tempo real da hora estimada no panel do paciente, horarios configurables por profesional e panel de estatísticas. Consulta [Incidencias e tarefas](templates/8_Incidencias.md).

## Links

- [Documentación PHP](https://www.php.net/manual/es/)
- [MariaDB](https://mariadb.org/)
- [Docker](https://docs.docker.com/)
- [MDN Web Docs](https://developer.mozilla.org/es/)
