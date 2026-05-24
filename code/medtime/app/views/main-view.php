<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/main.css">
  <title>MedTime - Panel de citas</title>
  
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="#" aria-label="MedTime, ir al inicio">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>

      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="#">Inicio</a></li>
          <li><a href="#" aria-current="page">Citas</a></li>
          <li><a href="#">Historial</a></li>
          <li><a href="#">Configuración</a></li>
        </ul>
      </nav>

      <div class="user-tools" aria-label="Herramientas de usuario">
        <button class="profile-button" type="button" aria-label="Perfil del usuario, doctor David Smith">
          <img class="avatar" src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=200&q=80" alt="Foto de perfil del doctor David Smith" width="44" height="44" loading="lazy" />
          <span>Dr. David Smith</span>
          <span aria-hidden="true">⌄</span>
        </button>

        <button class="icon-button" type="button" aria-label="Notificaciones, 1 pendiente">
          <span aria-hidden="true">🔔</span>
          <span class="badge" aria-hidden="true">1</span>
        </button>
      </div>
    </div>
  </header>

  <main id="contenido-principal" class="page">
    <h1 class="sr-only">Panel principal de citas de MedTime</h1>

    <div class="dashboard">
      <section class="card hero-card" aria-labelledby="proxima-cita-titulo">
        <h2 id="proxima-cita-titulo" class="hero-title">Próxima cita médica</h2>

        <div class="appointment-layout">
          <figure class="doctor-figure">
            <div class="portrait-shell">
              <div class="portrait-inner">
                <img src="https://images.unsplash.com/photo-1594824388853-d0c4d5e93355?auto=format&fit=crop&w=800&q=80" alt="Fotografía de la doctora María González" width="500" height="500" loading="lazy" />
              </div>
            </div>
            <figcaption class="doctor-caption">Profesional asignada para la próxima consulta.</figcaption>
          </figure>

          <div>
            <p class="doctor-name">Dr. María González</p>
            <p class="doctor-speciality">Especialidad: Cardiología</p>

            <div class="appointment-meta" aria-label="Información principal de la cita">
              <p class="label">Cita:</p>
              <p class="appointment-time">17:00</p>
              <p class="delay">Retraso estimado (+30 min)</p>
            </div>

            <div class="progress" aria-hidden="true"><span></span></div>
            <p class="recommendation">Hora recomendada: 17:30</p>

            <div class="action-row" aria-label="Acciones de la cita">
              <a class="btn" href="#">Ver detalles de cita</a>
              <button class="btn btn-primary" type="button">Reprogramar</button>
              <button class="btn" type="button">Cancelar</button>
            </div>
          </div>
        </div>
      </section>

      <aside class="right-column" aria-label="Resumen lateral de actividad">
        <details class="card panel" open>
          <summary>
            <span>Historial de citas recientes</span>
            <span class="summary-icon" aria-hidden="true">⌄</span>
          </summary>

          <ul class="history-list">
            <li class="history-item">
              <span class="stethoscope" aria-hidden="true">🩺</span>
              <span>Dr. López - Pediatría</span>
              <span class="history-time">10:00</span>
              <a class="text-link" href="#">Ver detalles</a>
            </li>
            <li class="history-item">
              <span class="stethoscope" aria-hidden="true">🩺</span>
              <span>Dr. Ramírez - Dermatología</span>
              <span class="history-time">14:30</span>
              <a class="text-link" href="#">Ver detalles</a>
            </li>
            <li class="history-item">
              <span class="stethoscope" aria-hidden="true">🩺</span>
              <span>Dr. Silva - Neumología</span>
              <span class="history-time">16:15</span>
              <a class="text-link" href="#">Ver detalles</a>
            </li>
          </ul>
        </details>

        <div class="bottom-grid">
          <section class="card calendar-card" aria-labelledby="calendario-titulo">
            <div class="panel-head">
              <h2 id="calendario-titulo" class="panel-title">Calendario pequeño</h2>
              <span aria-hidden="true">⌄</span>
            </div>

            <table class="calendar" summary="Calendario mensual abreviado con el día 9 seleccionado">
              <caption>Mes actual con selección del día de la próxima cita.</caption>
              <thead>
                <tr>
                  <th scope="col">D</th>
                  <th scope="col">L</th>
                  <th scope="col">M</th>
                  <th scope="col">X</th>
                  <th scope="col">J</th>
                  <th scope="col">V</th>
                  <th scope="col">S</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td><td></td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
                </tr>
                <tr>
                  <td>6</td><td>7</td><td>8</td><td><span class="selected-day">9</span></td><td>10</td><td>11</td><td>12</td>
                </tr>
                <tr>
                  <td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td>
                </tr>
                <tr>
                  <td>20</td><td>21</td><td>22</td><td>23</td><td>24</td><td>25</td><td>26</td>
                </tr>
                <tr>
                  <td>27</td><td>28</td><td>29</td><td>30</td><td></td><td></td><td></td>
                </tr>
              </tbody>
            </table>
          </section>

          <details class="card notifications-card panel" open>
            <summary>
              <span>Panel de notificaciones</span>
              <span class="summary-icon" aria-hidden="true">⌄</span>
            </summary>

            <ul class="notification-list">
              <li class="notification-item">
                <span class="notice-icon notice-alert" aria-hidden="true">!</span>
                <div>
                  <p class="notice-title">Cambio de hora en consulta con Dr. González: nueva cita a las 17:30</p>
                  <p class="notice-time">Hace 5 min</p>
                  <div class="notice-actions">
                    <button class="btn btn-primary" type="button">Aceptar</button>
                    <button class="btn" type="button">Revisar</button>
                  </div>
                </div>
              </li>
              <li class="notification-item">
                <span class="notice-icon notice-ok" aria-hidden="true">✓</span>
                <div>
                  <p class="notice-title">Confirmación de cita con Dr. López</p>
                  <p class="notice-time">Hace 30 min</p>
                  <div class="notice-actions">
                    <a class="btn" href="#">Ver detalles</a>
                  </div>
                </div>
              </li>
            </ul>
          </details>
        </div>
      </aside>
    </div>
  </main>
</body>
</html>
