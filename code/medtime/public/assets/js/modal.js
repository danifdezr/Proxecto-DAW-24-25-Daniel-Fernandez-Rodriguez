const Modal = (function () {

    //Evitar SyntaxError. Modal se carga primero
    const $d = document

    let $overlay = null,
        _keyHandler = null

    /* Escape HTML */
    function _escape(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
    }

    /* Crea o recupera el overlay */
    function _getOverlay() {
        if (!$overlay) {
            $overlay = $d.createElement('div')
            $overlay.className = 'modal-overlay'
            $overlay.setAttribute('role', 'dialog')
            $overlay.setAttribute('aria-modal', 'true')
            $overlay.addEventListener('click', ev => {
                if (ev.target === $overlay) closeAll()
            })
            $d.body.appendChild($overlay)
        }
        return $overlay
    }

    /* Muestra un modal */
    function _show($modal) {
        const $ov = _getOverlay()
        $ov.appendChild($modal)
        $ov.getBoundingClientRect()         // forzar reflow para la transición
        $ov.classList.add('is-visible')

        _keyHandler = ev => { if (ev.key === 'Escape') closeAll() }
        $d.addEventListener('keydown', _keyHandler)

        const $focusable = $modal.querySelector('button, input, select, textarea')
        if ($focusable) $focusable.focus()
    }

    /* Modal de confirmación (Ver ejemplo Coremain Vinculo Docente)*/
    /* Enlace directo emoji peligro: https://es.piliapp.com/emoji/list/hazard/ */
    function confirm({ message, confirmLabel = 'Confirmar', cancelLabel = 'Cancelar', danger = false, onConfirm, onCancel = null }) {
        const $modal = $d.createElement('div')
        $modal.className = 'modal modal--sm'
        $modal.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">${danger ? '⚠ Confirmar acción' : 'Confirmar'}</h2>
                <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-message">${_escape(message)}</p>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn--secondary js-cancel" type="button">${_escape(cancelLabel)}</button>
                <button class="modal-btn ${danger ? 'modal-btn--danger' : 'modal-btn--primary'} js-confirm" type="button">${_escape(confirmLabel)}</button>
            </div>`

        $modal.querySelector('.modal-close').addEventListener('click', () => { close($modal); if (onCancel) onCancel() })
        $modal.querySelector('.js-cancel').addEventListener('click',  () => { close($modal); if (onCancel) onCancel() })
        $modal.querySelector('.js-confirm').addEventListener('click', () => { close($modal); onConfirm() })

        _show($modal)
        return $modal
    }

    /* Modal informativo */
    function info({ title, contentHtml }) {
        const $modal = $d.createElement('div')
        $modal.className = 'modal'
        $modal.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">${_escape(title)}</h2>
                <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">${contentHtml}</div>`

        $modal.querySelector('.modal-close').addEventListener('click', () => close($modal))
        _show($modal)
        return $modal
    }

    /* Modal con formulario */
    function form({ title, fields = [], submitLabel = 'Guardar', onSubmit }) {
        const fieldsHtml = fields.reduce((html, f) => {
            const id  = `mf-${f.name}`,
                  req = f.required ? '<span class="req" aria-hidden="true">*</span>' : ''

            let input = ''
            if (f.type === 'select') {
                const opts = f.options.reduce((acc, o) =>
                    acc + `<option value="${_escape(o.value)}"${f.value === o.value ? ' selected' : ''}>${_escape(o.label)}</option>`, '')
                input = `<select class="modal-form-select" id="${id}" name="${f.name}"${f.required ? ' required' : ''}>${opts}</select>`
            } else if (f.type === 'checkbox') {
                input = `<input type="checkbox" id="${id}" name="${f.name}" value="1"${f.value ? ' checked' : ''}>`
            } else {
                input = `<input class="modal-form-input" type="${f.type || 'text'}" id="${id}" name="${f.name}"
                    value="${_escape(f.value ?? '')}" placeholder="${_escape(f.placeholder ?? '')}"${f.required ? ' required' : ''} autocomplete="off">`
            }
            return html + `
                <div class="modal-form-group">
                    <label class="modal-form-label" for="${id}">${_escape(f.label)}${req}</label>
                    ${input}
                </div>`
        }, '')

        const $modal = $d.createElement('div')
        $modal.className = 'modal modal--wide'
        $modal.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">${_escape(title)}</h2>
                <button class="modal-close" type="button" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-form-error js-error" role="alert"></div>
                <form class="modal-form-grid js-form" novalidate>${fieldsHtml}</form>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn--secondary js-cancel" type="button">Cancelar</button>
                <button class="modal-btn modal-btn--primary js-submit" type="button">${_escape(submitLabel)}</button>
            </div>`

        const $errEl    = $modal.querySelector('.js-error'),
              $submitBtn = $modal.querySelector('.js-submit')

        $modal.querySelector('.modal-close').addEventListener('click', () => close($modal))
        $modal.querySelector('.js-cancel').addEventListener('click',  () => close($modal))

        $modal.querySelector('.js-submit').addEventListener('click', async () => {
            $errEl.classList.remove('is-visible')
            $submitBtn.disabled = true
            $submitBtn.textContent = 'Guardando…'

            const $formEl = $modal.querySelector('.js-form'),
                  data = Object.fromEntries(
                      [...$formEl.elements]
                          .filter(el => el.name)
                          .map(el => [el.name, el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value])
                  )
            try {
                await onSubmit(data, $modal, $errEl)
            } catch (err) {
                $errEl.textContent = err.message || 'Error inesperado.'
                $errEl.classList.add('is-visible')
                $submitBtn.disabled = false
                $submitBtn.textContent = submitLabel
            }
        })

        _show($modal)
        return $modal
    }

    /* Cierra un modal */
    function close($modal) {
        if ($modal && $modal.parentNode === $overlay) $overlay.removeChild($modal)
        if ($overlay && !$overlay.querySelector('.modal')) {
            $overlay.classList.remove('is-visible')
            $d.removeEventListener('keydown', _keyHandler)
        }
    }

    /* Cierra todos */
    function closeAll() {
        if ($overlay) {
            $overlay.innerHTML = ''
            $overlay.classList.remove('is-visible')
        }
        $d.removeEventListener('keydown', _keyHandler)
    }

    return { confirm, info, form, close, closeAll, _escape }

})()

window.Modal = Modal
