const $d = document

/* Muestra confirmación */
function pedirConfirmacion(mensaje, confirmLabel, isDanger, onConfirm) {
    try {
        if (typeof Modal !== 'undefined' && typeof Modal.confirm === 'function') {
            Modal.confirm({
                message: mensaje,
                confirmLabel: confirmLabel || 'Confirmar',
                danger: isDanger,
                onConfirm: onConfirm
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
    $d.querySelectorAll('button[data-confirm]').forEach(function ($btn) {
        $btn.addEventListener('click', function (ev) {
            ev.preventDefault()

            var $form = $btn.closest('form')
            var mensaje = $btn.dataset.confirm
            var label = $btn.dataset.confirmLabel || 'Confirmar'
            var danger = $btn.dataset.confirmDanger === 'true'

            pedirConfirmacion(mensaje, label, danger, function () {
                if ($form) HTMLFormElement.prototype.submit.call($form)
            })
        })
    })
}

/* Interceptar formularios con data-confirm en el form */
function initFormConfirm() {
    $d.querySelectorAll('form[data-confirm]').forEach(function ($form) {
        $form.addEventListener('submit', function (ev) {
            ev.preventDefault()

            var mensaje = $form.dataset.confirm
            var label = $form.dataset.confirmLabel || 'Confirmar'
            var danger = $form.dataset.confirmDanger === 'true'

            pedirConfirmacion(mensaje, label, danger, function () {
                HTMLFormElement.prototype.submit.call($form)
            })
        })
    })
}

/* Modal de detalles de cita (data-cita-detalle) */
/* Emojis: https://es.piliapp.com/emoji/list/#tag*/
function initDetailModals() {
    $d.querySelectorAll('[data-cita-detalle]').forEach(function ($el) {
        $el.style.cursor = 'pointer'
        $el.addEventListener('click', function () {
            var d = $el.dataset

            var estadoLabels = {
                PENDIENTE: '🟡 Pendiente',
                CONFIRMADA: '🟢 Confirmada',
                FINALIZADA: '🔵 Finalizada',
                CANCELADA: '🔴 Cancelada',
                EN_CONSULTA: '🟠 En consulta'
            }
            var estadoLabel = estadoLabels[d.citaEstado] || d.citaEstado

            var retraso = (d.citaHoraEstimada && d.citaHoraEstimada !== d.citaHora)
                ? '<br><small style="color:#92400e">Hora estimada: ' + (typeof Modal !== 'undefined' ? Modal._escape(d.citaHoraEstimada) : d.citaHoraEstimada) + '</small>'
                : ''

            var obs = d.citaObs
                ? '<tr><th>Observaciones</th><td>' + (typeof Modal !== 'undefined' ? Modal._escape(d.citaObs) : d.citaObs) + '</td></tr>'
                : ''

            var e = typeof Modal !== 'undefined' ? Modal._escape.bind(Modal) : function (s) { return s }

            var html = '<table class="modal-detail">' +
                '<tr><th>Profesional</th><td>' + e(d.citaProf) + '</td></tr>' +
                '<tr><th>Especialidad</th><td>' + e(d.citaEspec) + '</td></tr>' +
                '<tr><th>Fecha</th><td>' + e(d.citaFecha) + '</td></tr>' +
                '<tr><th>Hora</th><td>' + e(d.citaHora) + retraso + '</td></tr>' +
                '<tr><th>Estado</th><td>' + estadoLabel + '</td></tr>' +
                obs +
                '</table>'

            try {
                if (typeof Modal !== 'undefined') {
                    Modal.info({ title: 'Detalle de la cita', contentHtml: html })
                }
            } catch (err) { /* silenciar */ }
        })
    })
}

function init() {
    initConfirmButtons()
    initFormConfirm()
    initDetailModals()
}

$d.readyState === 'loading'
    ? $d.addEventListener('DOMContentLoaded', init)
    : init()
