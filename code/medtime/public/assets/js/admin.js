const $d = document,
    $tbody = $d.getElementById('usuarios-tbody'),
    $loadingEl = $d.getElementById('tabla-loading'),
    $errorEl = $d.getElementById('tabla-error'),
    $contadorEl = $d.getElementById('total-usuarios'),
    $btnNuevo = $d.getElementById('btn-nuevo-usuario'),
    $search = $d.getElementById('search-usuarios')

const BASE = '/index.php?controller=AdminController&action='

const usuarios = []

function csrf() {
    return $d.querySelector('meta[name="csrf-token"]')?.content ?? ''
}

/* Revisar ejemplo Julián tienda informática */
async function ajax({ url, method = 'GET', data = null }) {
    try {
        const options = { method }
        if (method !== 'GET' && data) {
            options.body = new URLSearchParams({ ...data, csrf_token: csrf() })
        }

        const resp = await fetch(url, options)
        if (!resp.ok) {
            return { ok: false, status: resp.status, statusText: resp.statusText || 'Error', data: null }
        }

        const respData = await resp.json()
        return { ok: resp.ok, status: resp.status, statusText: 'OK', data: respData }

    } catch (error) {
        return { ok: false, status: 0, statusText: 'Error de red', data: null }
    }
}

/* Construye una fila de la tabla */
function renderFila(u) {
    var e = Modal._escape
    var rolLow = u.rol.toLowerCase()
    var labels = { PACIENTE: 'Paciente', PROFESIONAL: 'Profesional', ADMIN: 'Administrador' }
    var rolLabel = labels[u.rol] || u.rol
    var initials = e((u.nombre || '').charAt(0) + (u.apellidos || '').charAt(0))

    var infoExtra
    if (u.rol === 'PROFESIONAL') {
        var dispClass = u.disponible ? 'admin-badge--on' : 'admin-badge--off'
        var dispText = u.disponible ? '&#10003;' : '&#10007;'
        var dispTitle = u.disponible ? 'Disponible' : 'No disponible'
        infoExtra = '<span class="admin-espec">' + e(u.especialidad || 'Sin especificar') + '</span>' +
            '<span class="admin-badge admin-badge--disp ' + dispClass + '" title="' + dispTitle + '">' + dispText + '</span>'
    } else {
        infoExtra = '<span class="td-muted">-</span>'
    }

    var activoBadge = u.activo
        ? '<span class="admin-badge admin-badge--on">Activo</span>'
        : '<span class="admin-badge admin-badge--off">Inactivo</span>'

    return '<tr data-id="' + u.id + '">' +
        '<td class="td-nombre">' +
        '<span class="admin-avatar">' + initials + '</span>' +
        '<div>' +
        '<strong>' + e(u.nombre) + ' ' + e(u.apellidos) + '</strong>' +
        '<span class="td-email-sub">' + e(u.email) + '</span>' +
        '</div>' +
        '</td>' +
        '<td><span class="admin-badge admin-badge--rol admin-badge--' + rolLow + '">' + rolLabel + '</span></td>' +
        '<td class="td-espec">' + infoExtra + '</td>' +
        '<td>' + e(u.telefono || '-') + '</td>' +
        '<td>' + activoBadge + '</td>' +
        '<td class="td-acciones">' +
        '<button class="admin-btn admin-btn--edit" data-id="' + u.id + '" aria-label="Editar ' + e(u.nombre) + '">Editar</button>' +
        (u.rol !== 'ADMIN'
            ? ' <button class="admin-btn admin-btn--delete" data-id="' + u.id + '" data-nombre="' + e(u.nombre + ' ' + u.apellidos) + '" aria-label="Eliminar ' + e(u.nombre) + '">Eliminar</button>'
            : '') +
        '</td>' +
        '</tr>'
}

/* Renderiza la tabla completa */
function renderUsuarios(lista) {
    if (!lista.length) {
        $tbody.innerHTML = '<tr><td colspan="6" class="td-vacio">No hay usuarios registrados.</td></tr>'
        if ($contadorEl) $contadorEl.textContent = 0
        return
    }

    $tbody.innerHTML = lista.map(renderFila).join('')
    if ($contadorEl) $contadorEl.textContent = lista.length
}

/* Carga usuarios desde la API */
async function loadUsuarios() {
    $loadingEl.hidden = false
    $errorEl.hidden = true
    $tbody.innerHTML = ''

    const resp = await ajax({ url: BASE + 'getUsuarios' })

    $loadingEl.hidden = true

    if (!resp.ok || !resp.data || !resp.data.success) {
        $errorEl.hidden = false
        $errorEl.textContent = 'Error al cargar usuarios: ' + (resp.data ? resp.data.error : resp.statusText)
        return
    }

    usuarios.splice(0, usuarios.length, ...resp.data.data)
    renderUsuarios(usuarios)
}

/* Campos base para el formulario */
function camposBase(u) {
    u = u || {}
    return [
        { name: 'nombre', label: 'Nombre', type: 'text', value: u.nombre || '', required: true },
        { name: 'apellidos', label: 'Apellidos', type: 'text', value: u.apellidos || '', required: true },
        { name: 'email', label: 'Email', type: 'email', value: u.email || '', required: true },
        { name: 'telefono', label: 'Telefono', type: 'tel', value: u.telefono || '', required: false },
        {
            name: 'rol', label: 'Rol', type: 'select', value: u.rol || 'PACIENTE', required: true,
            options: [
                { value: 'PACIENTE', label: 'Paciente' },
                { value: 'PROFESIONAL', label: 'Profesional' },
                { value: 'ADMIN', label: 'Administrador' }
            ]
        },
        { name: 'activo', label: 'Cuenta activa', type: 'checkbox', value: u.activo !== undefined ? u.activo : true }
    ]
}

/* Campos de profesional */
function camposProf(u) {
    u = u || {}
    return [
        { name: 'especialidad', label: 'Especialidad', type: 'text', value: u.especialidad || '', required: false, placeholder: 'p.ej. Cardiologia' },
        { name: 'duracion_min', label: 'Duracion consulta (min)', type: 'number', value: String(u.duracion_min || 20), required: false },
        { name: 'disponible', label: 'Disponible para citas', type: 'checkbox', value: u.disponible !== false }
    ]
}

/* Campos de paciente */
function camposPac() {
    return [
        { name: 'dni', label: 'DNI / NIE', type: 'text', value: '', required: true, placeholder: '12345678A' },
        { name: 'fecha_nacimiento', label: 'Fecha de nacimiento', type: 'date', value: '', required: true }
    ]
}

/* Marca grupos de campos con una clase CSS dada */
function marcarCampos($modal, nombres, clase) {
    nombres.forEach(function (name) {
        var $input = $modal.querySelector('[name="' + name + '"]')
        if ($input) {
            var $group = $input.closest('.modal-form-group')
            if ($group) $group.classList.add(clase)
        }
    })
}

function marcarCamposProf($modal) {
    marcarCampos($modal, ['especialidad', 'duracion_min', 'disponible'], 'pro-field')
}

function marcarCamposPac($modal) {
    marcarCampos($modal, ['dni', 'fecha_nacimiento'], 'pac-field')
}

/* Muestra / oculta campos según el rol */
function bindRolToggle($modal) {
    var $sel = $modal.querySelector('[name="rol"]')
    if (!$sel) return

    function toggle() {
        var rol = $sel.value

        var showProf = rol === 'PROFESIONAL'
        $modal.querySelectorAll('.pro-field').forEach(function ($f) {
            $f.style.display = showProf ? '' : 'none'
        })
        var $sepProf = $modal.querySelector('.pro-fields-sep')
        if ($sepProf) $sepProf.style.display = showProf ? '' : 'none'

        var showPac = rol === 'PACIENTE'
        $modal.querySelectorAll('.pac-field').forEach(function ($f) {
            $f.style.display = showPac ? '' : 'none'
        })
        var $sepPac = $modal.querySelector('.pac-fields-sep')
        if ($sepPac) $sepPac.style.display = showPac ? '' : 'none'
    }

    toggle()
    $sel.addEventListener('change', toggle)
}

/* Inserta separador visual antes de un grupo de campos */
function insertarSeparador($modal, clase, texto) {
    var $primero = $modal.querySelector('.' + clase)
    if (!$primero) return

    var $sep = $d.createElement('div')
    $sep.className = 'modal-form-sep ' + clase + 's-sep'
    $sep.innerHTML = '<span>' + texto + '</span>'
    $primero.before($sep)
}

/* Crear usuario */
function abrirCrear() {
    var fields = camposBase()
    fields.push({ name: 'password', label: 'Contrasena', type: 'password', value: '', required: true, placeholder: 'Minimo 8 caracteres' })
    fields = fields.concat(camposProf())
    fields = fields.concat(camposPac())

    var $modal = Modal.form({
        title: 'Nuevo usuario',
        submitLabel: 'Crear usuario',
        fields: fields,
        onSubmit: async function (data, $m) {
            var resp = await ajax({ url: BASE + 'crearUsuario', method: 'POST', data: data })
            if (!resp.data || !resp.data.success) throw new Error(resp.data ? resp.data.error : resp.statusText)
            Modal.close($m)
            await loadUsuarios()
        }
    })

    marcarCamposProf($modal)
    insertarSeparador($modal, 'pro-field', 'Datos profesionales')
    marcarCamposPac($modal)
    insertarSeparador($modal, 'pac-field', 'Datos del paciente')
    bindRolToggle($modal)
}

/* Editar usuario */
function abrirEditar(id) {
    var u = usuarios.find(function (x) { return String(x.id) === String(id) })
    if (!u) return

    var fields = camposBase(u)
    if (u.rol === 'PROFESIONAL') {
        fields = fields.concat(camposProf(u))
    }

    var $modal = Modal.form({
        title: 'Editar - ' + u.nombre + ' ' + u.apellidos,
        submitLabel: 'Guardar cambios',
        fields: fields,
        onSubmit: async function (data, $m) {
            data.id = id
            var resp = await ajax({ url: BASE + 'actualizarUsuario', method: 'POST', data: data })
            if (!resp.data || !resp.data.success) throw new Error(resp.data ? resp.data.error : resp.statusText)
            Modal.close($m)
            await loadUsuarios()
        }
    })

    marcarCamposProf($modal)
    //Revisar
    insertarSeparador($modal)
    bindRolToggle($modal)
}

/* Eliminar usuario */
function confirmarEliminar(id, nombre) {
    Modal.confirm({
        message: 'Seguro que quieres eliminar a ' + nombre + '? Esta accion no se puede deshacer.',
        confirmLabel: 'Si, eliminar',
        danger: true,
        onConfirm: async function () {
            var resp = await ajax({ url: BASE + 'eliminarUsuario', method: 'POST', data: { id: id } })
            if (!resp.data || !resp.data.success) {
                Modal.info({ title: 'Error', contentHtml: '<p>' + Modal._escape(resp.data ? resp.data.error : resp.statusText) + '</p>' })
                return
            }
            await loadUsuarios()
        }
    })
}

/* Eventos */
if ($tbody) {
    $tbody.addEventListener('click', function (ev) {
        if (ev.target.classList.contains('admin-btn--edit')) abrirEditar(ev.target.dataset.id)
        if (ev.target.classList.contains('admin-btn--delete')) confirmarEliminar(ev.target.dataset.id, ev.target.dataset.nombre)
    })
}

if ($btnNuevo) {
    $btnNuevo.addEventListener('click', abrirCrear)
}

if ($search) {
    $search.addEventListener('input', function () {
        var q = $search.value.toLowerCase()
        $tbody.querySelectorAll('tr[data-id]').forEach(function ($tr) {
            $tr.hidden = !$tr.textContent.toLowerCase().includes(q)
        })
    })
}

$d.readyState === 'loading'
    ? $d.addEventListener('DOMContentLoaded', loadUsuarios)
    : loadUsuarios()
