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
                const res = await fetch(
                    `/?url=pacientes/search&q=${encodeURIComponent(q)}`,
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                if (!res.ok) {
                    throw new Error(`Error HTTP: ${res.status}`);
                }

                const resultado = await res.json();

                renderFilas(resultado.data ?? []);

            } catch (err) {
                console.error('Error al buscar pacientes:', err);
            }
        }, 300);
    });

    function renderFilas(pacientes) {
        if (!pacientes.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i
                            class="bi bi-inbox d-block mb-2"
                            style="font-size: 1.5rem;"
                        ></i>

                        No se encuentran pacientes registrados.
                    </td>
                </tr>
            `;

            return;
        }

        tbody.innerHTML = pacientes.map(p => `
            <tr>

                <td>
                    ${escapeHtml(p.nombres)}
                </td>

                <td>
                    ${escapeHtml(p.apellidos)}
                </td>

                <td>
                    ${escapeHtml(p.codigo_expediente)}
                </td>

                <td>
                    ${escapeHtml(p.documento_identidad ?? '—')}
                </td>

                <td>
                    ${escapeHtml(p.fecha_nacimiento ?? '—')}
                </td>

                <td>
                    ${escapeHtml(p.telefono)}
                </td>

                <td>
                    ${escapeHtml(p.email ?? '—')}
                </td>

                <td class="text-end">

                    <a
                        href="/?url=pacientes/edit/${encodeURIComponent(p.id_paciente)}"
                        class="text-warning me-2"
                        title="Editar"
                    >
                        <i class="bi bi-pencil"></i>
                    </a>

                    <a
                        href="/?url=pacientes/eliminar/${encodeURIComponent(p.id_paciente)}"
                        class="text-danger"
                        title="Eliminar"
                    >
                        <i class="bi bi-trash"></i>
                    </a>

                </td>

            </tr>
        `).join('');
    }

    function escapeHtml(valor) {
        return String(valor ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
});