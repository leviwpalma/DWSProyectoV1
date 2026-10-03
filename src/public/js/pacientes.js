document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('search');
    const tbody = document.getElementById('pacientes-table');
    if (!input || !tbody) return;

    let timer = null;

    input.addEventListener('input', (e) => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const q = e.target.value.trim();
            try {
                const res = await fetch(`/api/pacientes?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const { data } = await res.json();
                renderFilas(data);
            } catch (err) {
                console.error('Error al buscar:', err);
            }
        }, 300);
    });

    function renderFilas(pacientes) {
        if (!pacientes.length) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <div class="fs-5 mb-1">📋</div>
                    No se encuentran pacientes registrados.
                </td>
            </tr>`;
            return;
        }
        tbody.innerHTML = pacientes.map(p => `
        <tr>
            <td><span class="badge bg-primary-subtle text-primary-emphasis">${p.codigo_expediente}</span></td>
            <td>${p.nombres}</td>
            <td>${p.apellidos}</td>
            <td>${p.documento_identidad ?? '—'}</td>
            <td>${p.telefono}</td>
            <td>${p.email ?? '—'}</td>
            <td class="text-end">
                <a href="/pacientes/${p.id_paciente}" class="btn btn-sm btn-outline-info">Ver</a>
                <a href="/pacientes/${p.id_paciente}/edit" class="btn btn-sm btn-outline-warning">Editar</a>
                <form method="POST" action="/pacientes/${p.id_paciente}/delete"
                      onsubmit="return confirm('¿Desactivar?')" class="d-inline">
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    `).join('');
    }
});