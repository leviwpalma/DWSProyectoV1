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
            tbody.innerHTML = `<tr><td>No se encuentran pacientes.</td></tr>`;
            return;
        }
        tbody.innerHTML = pacientes.map(p => `
        <tr>
            <td>${p.codigo_expediente}</td>
            <td>${p.nombres}</td>
            <td>${p.apellidos}</td>
            <td>${p.telefono}</td>
            <td>${p.correo ?? ''}</td>
            <td>
                <a href="/pacientes/${p.id_paciente}">Ver</a>
                <a href="/pacientes/${p.id_paciente}/edit">Editar</a>
                <form method="POST" action="/pacientes/${p.id_paciente}/delete" onsubmit="return confirm('¿Desactivar?')">
                    <button>Eliminar</button>
                </form>
            </td>
        </tr>
    `).join('');
    }
});