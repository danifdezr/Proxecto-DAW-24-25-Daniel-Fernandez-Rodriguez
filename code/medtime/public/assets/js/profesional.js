const $d = document,
      $contadorEl = $d.getElementById('contador-pendientes')

/* Muestra confirmación: modal si está disponible, native confirm como fallback */
function pedirConfirmacion(mensaje, confirmLabel, isDanger, onConfirm) {
    try {
        if (typeof Modal !== 'undefined' && typeof Modal.confirm === 'function') {
            Modal.confirm({
                message:      mensaje,
                confirmLabel: confirmLabel || 'Confirmar',
                danger:       isDanger,
                onConfirm:    onConfirm
            })
        } else {
            if (window.confirm(mensaje)) onConfirm()
        }
    } catch (e) {
        if (window.confirm(mensaje)) onConfirm()
    }
}

/* Interceptar botones con data-confirm */
function initConfirmButtons() {
    $d.querySelectorAll('button[data-confirm]').forEach(function($btn) {
        $btn.addEventListener('click', function(ev) {
            ev.preventDefault()

            var $form  = $btn.closest('form')
            var msg    = $btn.dataset.confirm
            var label  = $btn.dataset.confirmLabel  || 'Confirmar'
            var danger = $btn.dataset.confirmDanger === 'true'

            pedirConfirmacion(msg, label, danger, function() {
                if ($form) HTMLFormElement.prototype.submit.call($form)
            })
        })
    })
}

/* Modal de detalles de cita */
/* Ver enlace emojis paciente.js */
function initDetailModals() {
    $d.querySelectorAll('[data-cita-detalle]').forEach(function($el) {
        $el.addEventListener('click', function() {
            var d = $el.dataset

            var estadoLabels = {
                PENDIENTE:   '🟡 Pendiente',
                CONFIRMADA:  '🟢 Confirmada',
                FINALIZADA:  '🔵 Finalizada',
                CANCELADA:   '🔴 Cancelada',
                EN_CONSULTA: '🟠 En consulta'
            }
            var estadoLabel = estadoLabels[d.citaEstado] || d.citaEstado

            var e   = typeof Modal !== 'undefined' ? Modal._escape.bind(Modal) : function(s) { return s }
            var obs = d.citaObs    ? '<tr><th>Observaciones</th><td>' + e(d.citaObs) + '</td></tr>' : ''
            var ini = d.citaInicio ? '<tr><th>Inicio real</th><td>' + e(d.citaInicio) + '</td></tr>' : ''

            var html = '<table class="modal-detail">' +
                '<tr><th>Paciente</th><td>' + e(d.citaPaciente) + '</td></tr>' +
                '<tr><th>Hora programada</th><td>' + e(d.citaHora) + '</td></tr>' +
                '<tr><th>Estado</th><td>' + estadoLabel + '</td></tr>' +
                ini + obs +
                '</table>'

            try {
                if (typeof Modal !== 'undefined') {
                    Modal.info({ title: 'Detalle de la cita', contentHtml: html })
                }
            } catch (err) { /* silenciar */ }
        })
    })
}

function initPollPendientes() {
    if (!$contadorEl) return

    setInterval(function() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.text() })
            .then(function(html) {
                var $tmp = $d.createElement('div')
                $tmp.innerHTML = html
                var $nuevo = $tmp.getElementById('contador-pendientes')
                if ($nuevo) $contadorEl.textContent = $nuevo.textContent
            })
            .catch(function() {})
    }, 60000)
}

function init() {
    initConfirmButtons()
    initDetailModals()
    initPollPendientes()
}

$d.readyState === 'loading'
    ? $d.addEventListener('DOMContentLoaded', init)
    : init()
