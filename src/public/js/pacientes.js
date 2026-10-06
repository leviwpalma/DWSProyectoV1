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
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="bi bi-inbox d-block mb-2" style="font-size: 1.5rem;"></i>
                    No se encuentran pacientes registrados.
                </td>
            </tr>`;
            return;
        }
        tbody.innerHTML = pacientes.map(p => `
        <tr>
            <td>${p.nombres}</td>
            <td>${p.apellidos}</td>
            <td>${p.codigo_expediente}</td>
            <td>${p.documento_identidad ?? '—'}</td>
            <td>${p.fecha_nacimiento ?? '—'}</td>
            <td>${p.telefono}</td>
            <td>${p.email ?? '—'}</td>
            <td class="text-end">
                <a href="/pacientes/${p.id_paciente}/edit"
                   class="text-warning me-2"
                   title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="/pacientes/${p.id_paciente}/delete"
                   class="text-danger"
                   title="Eliminar">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
    `).join('');
    }
});