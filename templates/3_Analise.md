# Requerimientos do sistema

- [Requerimientos do sistema](#requerimientos-do-sistema)
  - [1- Descrición Xeral](#1--descrición-xeral)
  - [2- Funcionalidades](#2--funcionalidades)
  - [3- Tipos de usuarios](#3--tipos-de-usuarios)
  - [4- Contorno operacional](#4--contorno-operacional)
  - [5- Normativa](#5--normativa)
  - [6- Melloras futuras](#6--melloras-futuras)

> *EXPLICACION*: Este documento describe os requirimentos para "nome do proxecto" especificando que funcionalidade ofrecerá e de que xeito.

## 1- Descrición Xeral

**MedTime** é unha aplicación web para a xestión de citas médicas en clínicas privadas que permite reservar, modificar e cancelar citas, destacando polo seu módulo de **estimación dinámica de atrasos**. O sistema calcula en tempo real a hora probable de atención do paciente en función do histórico de tempos de consulta de cada profesional e do estado actual da axenda, informando ao usuario da **hora de chegada recomendada** para evitar esperas innecesarias na sala de espera.

A aplicación está deseñada para clínicas pequenas e medianas (1-20 profesionais), ofrecendo un panel de administración para o persoal e unha interface sinxela para os pacientes. Desenvolta en PHP con patrón MVC e base de datos MySQL, garante accesibilidade desde calquera navegador web moderno.

## 2- Funcionalidades

| **Acción** | **Descrición** | **Actores** | **Datos entrada** | **Proceso** | **Datos saída** |
|------------|----------------|-------------|-------------------|-------------|-----------------|
| **Rexistro de usuario** | Creación de contas para pacientes | Paciente | Nome, email, teléfono, contrasinal | Validación email único, hash contrasinal, inserción BD | Confirmación rexistro, email de verificación |
| **Autenticación** | Inicio de sesión | Paciente, Persoal | Email, contrasinal | Verificación credenciais BD | Token sesión, redirección ao dashboard |
| **Reservar cita** | Selección de data/hora/dispoñibilidade | Paciente | Data, hora, profesional | Consulta axenda dispoñible, inserción cita BD | Confirmación reserva, PDF resumo |
| **Ver citas** | Consulta de citas activas/proxectadas | Paciente | ID usuario | Consulta BD citas por usuario | Lista citas con hora oficial e estimada |
| **Estimar atraso** | Cálculo hora chegada recomendada | Paciente, Persoal | ID cita, hora actual | Consulta citas previas + tempos reais + promedios | Hora estimada de atención + hora chegada recomendada |
| **Actualizar estado cita** | Marcar cita "en curso/rematada" | Persoal | ID cita, tempo real empregado | Actualización BD + recalculo atrasos posteriores | Confirmación + actualización axenda global |
| **Xestionar axenda** | Vista/editar axenda por profesional | Persoal | ID profesional, data | Consulta BD citas por data/profesional | Calendario visual con cores por estado |
| **Estadísticas atrasos** | Informes de tempos medios por profesional | Persoal | ID profesional, período | Agregación datos BD (media, max, min atraso) | Gráficos e táboas estatísticas |
| **Notificacións** | Envio avisos por email | Sistema | ID cita, atraso significativo | Xeración email automático | Email enviado ao paciente |
| **Recuperar contrasinal** | Reset de contrasinal | Paciente | Email | Xeración token temporal BD | Link de reset por email |


## 3- Tipos de usuarios

- **Paciente (usuario rexistrado)**: Pode rexistrarse, iniciar sesión, ver/modificar as súas citas, consultar a hora estimada de chegada recomendada e recibir notificacións. Non ten acceso á xestión de axendas nin datos doutros usuarios.
- **Persoal da clínica (autenticado)**: Pode xestionar axendas de profesionais, actualizar estados de citas, introducir tempos reais de consulta e consultar estatísticas de atrasos. Ten acceso restrito a datos do seu centro.
- **Administrador do centro**: Todos os permisos do persoal + xestión de usuarios do centro, configuracións (limiares de atraso, templates de email) e visualización de informes globais.
- **Usuario anónimo**: Pode ver información xeral da aplicación (demo, contacto), pero non acceder a funcionalidades persoais nin reservar citas.

## 4- Contorno operacional

**Requisitos mínimos para o usuario final:**
- Navegador web moderno: Chrome 100+, Firefox 100+, Safari 15+, Edge 100+
- Resolución mínima: 1024x768 píxeles
- Conexión a Internet: 1 Mbps (recomendado 5 Mbps para actualizacions en tempo real)
- Sistema operativo: Windows 10+, macOS 11+, Android 9+, iOS 14+

**Non se require hardware nin software adicional**. A aplicación é 100% web, responsive e optimizada para móbiles e tablets.

**Para administración da clínica:**
- Acceso ao panel desde calquera dispositivo con navegador.
- Recomendado: ordenador de escritorio para xestión de múltiples axendas.

## 5- Normativa

O proxecto **MedTime** cumpre integralmente coa normativa vixente en materia de protección de datos:

### Lei Orgánica 3/2018 (LOPDGDD)
- **Responsable do tratamento**
- **Fin do tratamento**: Xestión de citas médicas, estimación de atrasos e notificacións ao paciente.
- **Base xurídica**: Consentimento explícito do usuario (art. 6.1.a RGPD) e execución de contrato (art. 6.1.b RGPD).

### Reglamento (UE) 2016/679 (RGPD)
- Dereitos ARCO (acceso, rectificación, cancelación, oposición) implementados mediante formularios específicos.
- Dereito á portabilidade e limitación do tratamento.
- Nomeamento de Delegado de Protección de Datos (DPD) no caso de tratamento a gran escala de datos sensibles de saúde.

### Elementos obrigatorios na web:
- **Aviso legal**: Identificación do titular, datos rexistrais, datos fiscais.
- **Política de privacidade**: fins do tratamento, plazos de conservación (5 anos por cita), dereitos dos interesados, destinatarios.
- **Política de cookies**: Cookies técnicas e de sesión (non analíticas nin de publicidade).

### Medidas de seguridade implementadas:
- Cifrado contrasinais (bcrypt).
- Conexións HTTPS obrigadas.
- Autenticación por sesión con timeout.
- Logs de accesos auditables.
- Backup automatizado semanal da base de datos.
## 6- Melloras futuras

1. **App móbil nativa** (iOS/Android): Push notifications en tempo real para cambios de atraso.
2. **Integración WhatsApp/Telegram**: Notificacións por mensaxería instantánea.
3. **IA preditiva**: Modelo de machine learning para prever atrasos baseándose en patróns históricos (días da semana, hora, tipo de consulta).
4. **Video consulta integrada**: Calendly + Zoom/Twilio para consultas remotas.
5. **API aberta**: Para integración con software de xestión clínica existente (HIS).
6. **Multilingüe**: Soporte para portugués e inglés (expansión a Portugal e Latinoamérica).
7. **Pagos online**: Stripe/PayPal para citas de pago directo.

[**<-Anterior**](../../README.md)
