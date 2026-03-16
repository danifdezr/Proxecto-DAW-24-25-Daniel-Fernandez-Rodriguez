# 1- Empresa

- [1- Empresa](#1--empresa)
  - [1.1- Idea de negocio](#11--idea-de-negocio)
  - [1.2- Xustificación da idea](#12--xustificación-da-idea)
    - [Orixe da idea](#orixe-da-idea)
    - [Necesidade que cobre](#necesidade-que-cobre)
    - [Situación do mercado](#situación-do-mercado)
    - [Análise DAFO](#análise-dafo)
  - [1.3- Segmento de clientes](#13--segmento-de-clientes)
    - [Cliente (quen paga)](#cliente-quen-paga)
    - [Usuario (quen usa)](#usuario-quen-usa)
    - [Cuantificación do mercado obxectivo](#cuantificación-do-mercado-obxectivo)
  - [1.4- Competencia](#14--competencia)
  - [1.5- Proposta de valor](#15--proposta-de-valor)
  - [1.6- Forma xurídica](#16--forma-xurídica)
  - [1.7- Investimentos](#17--investimentos)
    - [Equipamento e infraestrutura necesaria](#equipamento-e-infraestrutura-necesaria)
    - [1.7.1- Custos](#171--custos)
    - [1.7.2- Ingresos](#172--ingresos)
  - [1.8- Viabilidade](#18--viabilidade)
    - [1.8.1- Viabilidade técnica](#181--viabilidade-técnica)
    - [1.8.2 - Viabilidade económica](#182---viabilidade-económica)
    - [1.8.3- Conclusión](#183--conclusión)



## 1.1- Idea de negocio

O produto central é **MedTime**, unha aplicación web de xestión de citas médicas dirixida a clínicas e centros sanitarios privados que, ademais de xestionar o ciclo completo dunha cita (reserva, modificación, cancelación), incorpora un módulo de **estimación dinámica do atraso** en consulta. Este módulo analiza o histórico de tempos de atención de cada profesional e o estado actual da axenda para calcular e mostrarlle ao paciente unha **hora de chegada recomendada**, actualizada en tempo real.

O **valor engadido** fronte á competencia é precisamente esta estimación de atraso: se un paciente ten cita ás 17:00 pero a consulta acumula 40 minutos de demora, a aplicación infórmalle para que chegue ás 17:35, evitando esperas innecesarias. Ademais, ofrece notificacións automáticas por correo electrónico cando o atraso estimado varía de forma significativa.

O **produto aumentado** inclúe:
- Panel de administración para o persoal da clínica: xestión de axendas por profesional, estatísticas de atrasos e informes de ocupación.
- Historial de citas para o paciente.
- Sistema de recordatorios automáticos previos á cita.


## 1.2- Xustificación da idea

### Orixe da idea

A idea xorde da experiencia cotiá de moitos pacientes que, chegando puntualmente á súa cita médica, se atopan con esperas de 30 a 60 minutos na sala de espera, sen recibir ningún tipo de información sobre o atraso acumulado. Esta situación, moi frecuente tanto en centros privados como públicos, implica perda de tempo, frustración e, nalgúns casos, abandono da cita.

### Necesidade que cobre

A necesidade principal é **reducir o tempo de espera presencial percibido polo paciente**, ofrecéndolle información veraz e actualizada sobre cando será realmente atendido, para que poida organizar mellor o seu tempo.

### Situación do mercado

O mercado de software de programación de citas médicas a nivel mundial situouse en **640 millóns de dólares en 2026**, cun crecemento proxectado ata os **2.930 millóns en 2030**, segundo datos de Business Research Insights. Pola súa banda, o mercado global de apps de mHealth estímase en **45.140 millóns de dólares en 2026**, con previsión de alcanzar os **113.200 millóns en 2034**.

En España, os problemas de demora son reais e documentados:
- O **69,8% dos pacientes** da sanidade pública esperaron de media **9,12 días** para obter cita en Atención Primaria en 2023, un incremento respecto aos 8,8 días de 2022.
- A espera media para unha primeira consulta especializada no SNS é de **87 días** segundo o Informe Anual do Sistema Nacional de Saúde 2024.
- Na sanidade privada, aínda que máis áxil, o tempo de espera en urxencias pode chegar ata os 30 minutos, e a espera media para consulta especializada é de **14,6 días**.

Existen plataformas de xestión de citas (Doctoralia, Setmore, Docplanner), pero ningunha delas ofrece de forma nativa e específica un **módulo de estimación do atraso en tempo real** para a consulta do día. O segmento de clínicas privadas pequenas e medianas está **insuficientemente atendido** neste aspecto concreto.

### Análise DAFO

| | **Positivo** | **Negativo** |
|---|---|---|
| **Interno** | **Fortalezas**: funcionalidade diferencial de estimación de atraso; tecnoloxía coñecida polo equipo (PHP, MySQL); baixo custo de desenvolvemento e mantemento | **Debilidades**: proxecto inicial dun só desenvolvedor; sen recursos para marketing; produto sen validación real con usuarios aínda |
| **Externo** | **Oportunidades**: mercado de software médico en forte crecemento; dixitalización crecente das clínicas privadas; 26% da poboación española ten seguro privado | **Ameazas**: competidores consolidados con gran base de usuarios (Doctoralia, Docplanner); posible resistencia das clínicas ao cambio de software |

## 1.3- Segmento de clientes

O produto vai dirixido a dous tipos de perfís diferenciados:

### Cliente (quen paga)
**Clínicas e centros médicos privados** de tamaño pequeno e mediano en España, que non usen aínda software de xestión de citas ou que usen ferramentas xenéricas sen módulo de estimación de atrasos. En España operan actualmente **431 hospitais privados** e máis de **25.000 clínicas ambulatorias e consultas privadas** (fisioterapia, odontoloxía, dermatoloxía, psicoloxía, etc.), constituíndo o principal mercado obxectivo.

O gasto sanitario privado en España alcanzou os **34.056 millóns de euros en 2025**, representando o **2,48% do PIB**, o que reflicte un sector de gran tamaño con marxe para a inversión en tecnoloxía de xestión.

### Usuario (quen usa)
- **Persoal administrativo** da clínica: para xestionar as axendas, introducir tempos de consulta reais e supervisar atrasos.
- **Pacientes**: para consultar as súas citas, ver a hora estimada de chegada recomendada e recibir notificacións.

### Cuantificación do mercado obxectivo
Focalizando no segmento de clínicas privadas medianas (de 3 a 20 profesionais), estímase un universo de entre **8.000 e 12.000 centros** en España susceptibles de adoptar un software de xestión de citas con módulo de atrasos.

## 1.4- Competencia

O mercado de software de xestión de citas médicas está dominado por varios actores consolidados:

| **Competidor** | **Posición** | **Fortalezas** | **Carencias fronte a MedTime** |
|---|---|---|---|
| **Doctoralia (Docplanner)** | Líder absoluto en España (+120.000 profesionais rexistrados). Presenza en 30 países. | Marketplace con gran visibilidade, recordatorios automáticos, reduce ausencias un 65% | Non ofrece estimación dinámica do atraso en consulta en tempo real |
| **Setmore** | Solución gratuíta internacional con boa penetración en clínicas pequenas | Plataforma fácil de usar, plan gratuíto dispoñible | Orientado á reserva de citas, sen módulo de xestión de atrasos nin estimación |
| **AgendaPro** | En crecemento en España e Latinoamérica para clínicas | Integración con WhatsApp, recordatorios SMS | Sen funcionalidade de estimación de demora real na consulta |
| **Waitlist.me** | Especializado en xestión de listas de espera (non só médico) | Estimación de espera para colas en tempo real | Non integrado cun sistema de xestión de citas médicas completo |

A principal **ameaza substitutiva** é que as clínicas poidan optar por solucións xenéricas de calendario (Google Calendar, Microsoft Outlook) ou por ferramentas de mensaxería directa co paciente. Con todo, estes non ofrecen módulo de estimación de atrasos nin xestión centralizada de axendas.

## 1.5- Proposta de valor


A proposta de valor de **MedTime** céntrase nun elemento diferencial claro que os competidores actuais non cobren: a **estimación dinámica e transparente do atraso en consulta**.

- **Diferenciación principal**: Mentres Doctoralia ou Setmore informan da hora da cita, MedTime informa ao paciente de *cando será realmente atendido*, calculando o atraso acumulado na xornada en función de datos históricos e do estado actual da axenda.
- **Melloras fronte á competencia**:
  - Notificacións proactivas cando o atraso supera un limiar configurable (por exemplo +20 minutos).
  - Panel de estatísticas de atrasos por profesional, moi útil para que a clínica mellore a súa organización interna.
  - Prezo competitivo para pequenas clínicas que non necesitan o ecosistema completo de Doctoralia.
- **Valor para o mercado**: Mellora a experiencia do paciente, reduce o tempo de espera presencial percibido e aumenta a eficiencia operativa da clínica.
- **Por que contratarán este produto?** Porque é a única solución centrada especificamente en resolver o problema dos atrasos, cunha implantación sinxela e un custo accesible para clínicas pequenas e medianas.

## 1.6- Forma xurídica

A forma xurídica elixida para o inicio da actividade é a de **traballador autónomo**.

Esta elección xustifícase porque o proxecto parte dun único desenvolvedor, con ingresos iniciais incertos e reducidos, polo que a figura do autónomo ofrece **menor carga administrativa**, **trámites máis sinxelos** e **sen necesidade de capital mínimo** nin gastos notariais, o que permite comezar a actividade rapidamente e con baixo risco. Ademais, nos primeiros anos de actividade a **cota de autónomo bonificada** (tarifa plana) reduce significativamente os custos fixos. No caso de que o negocio crezca e os ingresos así o xustifiquen, valoraríase a conversión a **Sociedade Limitada Unipersonal (SLU)** para obter vantaxes fiscais polo Imposto de Sociedades (25%) e limitar a responsabilidade patrimonial persoal.


## 1.7- Investimentos


### Equipamento e infraestrutura necesaria

| **Elemento** | **Descrición** | **Custo estimado** |
|---|---|---|
| Ordenador de traballo | Equipo portátil ou sobremesa para o desenvolvemento (xa dispoñible) | 0 € (xa dispoñible) |
| Dominio web | Rexistro dun dominio `.es` ou `.com` para a aplicación | ~10–15 €/ano |
| Hosting compartido PHP + MySQL | Servidor de hospedaxe para despregue da aplicación (ex. SiteGround, Hostinger) | ~25–60 €/ano (~2–5 €/mes) |
| Certificado SSL | Incluído na maioría dos hostings modernos | 0 € (incluído) |
| Servizo de correo transaccional | Para envío de notificacións (ex. Brevo/Sendinblue plan gratuíto) | 0 € (plan gratuíto ata 300 emails/día) |
| Software de desenvolvemento | VSCode, Git, navegadores (ferramentas gratuítas) | 0 € |
| **Total inicial** | | **~35–75 €** |

### 1.7.1- Custos

**Custos fixos:**

| **Concepto** | **Importe anual** |
|---|---|
| Hosting + dominio | ~70 €/ano |
| Cota de autónomo (tarifa plana primeiro ano) | ~960 €/ano (80 €/mes) |
| Seguro de responsabilidade civil (estimado) | ~150 €/ano |
| **Total custos fixos** | **~1.180 €/ano** |

**Custos variables (en función do número de clientes):**

| **Concepto** | **Importe estimado** |
|---|---|
| Servizo de SMS/notificacións (se se activa) | ~0,05 €/SMS enviado |
| Hosting de maior capacidade ao crecer | +20–50 €/mes ao superar 10 clientes activos |
| Xestión fiscal e contable (asesoría) | ~50 €/mes (~600 €/ano) |

### 1.7.2- Ingresos

O modelo de negocio proposto é **SaaS (Software as a Service)**, cunha cota mensual por centro sanitario cliente:

| **Plan** | **Funcionalidades** | **Prezo/mes** |
|---|---|---|
| **Básico** | Ata 2 profesionais, xestión de citas, estimación de atrasos | 29 €/mes |
| **Estándar** | Ata 5 profesionais, notificacións por correo, estatísticas básicas | 59 €/mes |
| **Avanzado** | Profesionais ilimitados, estatísticas avanzadas, soporte prioritario | 99 €/mes |

**Previsión de ventas (escenario conservador):**

| **Ano** | **Clientes activos** | **Ingreso mensual medio** | **Ingresos anuais** |
|---|---|---|---|
| Ano 1 | 3 clientes | 59 €/cliente | ~2.124 € |
| Ano 2 | 10 clientes | 59 €/cliente | ~7.080 € |
| Ano 3 | 25 clientes | 65 €/cliente | ~19.500 € |x`

## 1.8- Viabilidade

### 1.8.1- Viabilidade técnica

Dende o punto de vista técnico, o proxecto é plenamente viable:

- **Recursos humanos**: o proxecto é desenvolvido por un único programador con coñecementos de PHP, MySQL, HTML, CSS e JavaScript, tecnoloxías directamente aplicables ao produto.
- **Medios de produción**: o equipamento necesario xa está dispoñible. A infraestrutura de hosting é accesible por menos de 5 €/mes, suficiente para o prototipo inicial.
- **Impedimentos técnicos**: non existe ningún impedimento técnico significativo. O cálculo de estimación de atrasos baséase en operacións matemáticas sobre datos almacenados en base de datos relacional, tecnoloxía ben coñecida e documentada. A parte máis complexa será o deseño correcto do modelo de datos para gardar os tempos de atención reais e calcular promedios con precisión, pero trátase dun reto técnico abordable no prazo establecido.
- **Escalabilidade**: o sistema pode crecer engadindo máis funcionalidades (integración con WhatsApp, app móbil, IA para predición) sen necesidade de reescribir toda a base.

### 1.8.2 - Viabilidade económica

Contrastando custos e ingresos:

| **Concepto** | **Ano 1** | **Ano 2** | **Ano 3** |
|---|---|---|---|
| **Ingresos** | 2.124 € | 7.080 € | 19.500 € |
| **Custos fixos** | 1.180 € | 1.780 € | 2.380 € |
| **Resultado bruto** | **+944 €** | **+5.300 €** | **+17.120 €** |

O proxecto alcanza o **punto de equilibrio** a partir de **tan só 3 clientes con plan Estándar** (3 × 59 €/mes = 177 €/mes > ~98 €/mes de custos fixos). Isto fai que a barreira de entrada para a rendibilidade sexa moi baixa.

No primeiro ano, os ingresos son modestos (etapa de captación e validación do produto), pero os custos son igualmente baixos grazas á estrutura lixeira do negocio (traballo en remoto, sen local físico, infraestrutura cloud económica).

### 1.8.3- Conclusión

O proxecto é **economicamente e tecnicamente viable**:

- Os **beneficios superan os custos** a partir dun número reducido de clientes (mínimo 3), o que reduce considerablemente o risco empresarial.
- A estrutura de custos é moi lixeira ao tratarse dun produto 100% dixital sen necesidade de local físico nin empregados adicionais no inicio.
- No caso de que os ingresos do primeiro ano non cubrisen a totalidade dos custos, a diferenza é asumible polo emprendedor sen necesidade de financiamento externo, aínda que tamén existirían opcións de **subvencións públicas** para emprendedores novos e proxectos de dixitalización sanitaria (programas IGAPE en Galicia, convocatorias Kit Digital, etc.).
- O mercado obxectivo ten un tamaño suficiente (miles de clínicas privadas en España) e a proposta de valor diferencial da estimación de atrasos cubre unha necesidade real non atendida polos competidores actuais.

[**<-Anterior**](../../README.md)
