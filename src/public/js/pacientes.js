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
                    <td colspan="8" class="empty-state">
                        <i
                            class="bi bi-inbox d-block mb-2"
                            style="font-size: 1.8rem;"
                        ></i>
                        No se encuentran pacientes registrados.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = pacientes.map(p => {
            const id = escapeHtml(p.id_paciente);
            const codigo = escapeHtml(p.codigo_expediente);
            const nombres = escapeHtml(p.nombres);
            const apellidos = escapeHtml(p.apellidos);
            const nombreCompleto = `${nombres} ${apellidos}`;
            const generoTexto = formatGenero(p.genero);
            const fechaNacTexto = formatFecha(p.fecha_nacimiento);
            const direccion = escapeHtml(p.direccion ?? 'Sin dirección');
            const antecedentes = escapeHtml(p.antecedentes_medicos ?? 'Sin antecedentes registrados');

            return `
                <tr>
                    <td>${nombres}</td>
                    <td>${apellidos}</td>
                    <td><strong>${codigo}</strong></td>
                    <td>${escapeHtml(p.documento_identidad ?? '—')}</td>
                    <td>${escapeHtml(p.fecha_nacimiento ?? '—')}</td>
                    <td>${escapeHtml(p.telefono)}</td>
                    <td>${escapeHtml(p.email ?? '—')}</td>
                    <td class="actions-cell">
                        <a
                            href="#"
                            class="action-icon icon-view"
                            data-bs-toggle="modal"
                            data-bs-target="#expediente-${id}"
                            title="Ver expediente"
                        >
                            <i class="bi bi-eye"></i>
                        </a>

                        <a
                            href="/?url=pacientes/edit/${id}"
                            class="action-icon icon-edit"
                            title="Editar"
                        >
                            <i class="bi bi-pencil"></i>
                        </a>

                        <a
                            href="/?url=pacientes/eliminar/${id}"
                            class="action-icon icon-delete"
                            title="Eliminar"
                        >
                            <i class="bi bi-trash"></i>
                        </a>

                        <div
                            class="modal fade expediente-modal text-start"
                            id="expediente-${id}"
                            tabindex="-1"
                            aria-hidden="true"
                        >
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0">
                                    <div class="modal-header border-0 pb-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-0">
                                        <div class="expediente-card">
                                            <div class="expediente-side">
                                                <div>
                                                    <div class="exp-label">
                                                        Expediente No.<br>
                                                        <strong>${codigo}</strong>
                                                    </div>
                                                    <div class="exp-avatar">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                </div>
                                                <div class="exp-dots">• • •</div>
                                            </div>

                                            <div class="expediente-main">
                                                <h4>${nombreCompleto}</h4>

                                                <div class="expediente-grid">
                                                    <div class="expediente-item">
                                                        <i class="bi bi-gender-ambiguous"></i>
                                                        <span>${generoTexto}</span>
                                                    </div>

                                                    <div class="expediente-item">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <span>${direccion}</span>
                                                    </div>

                                                    <div class="expediente-item">
                                                        <i class="bi bi-calendar"></i>
                                                        <span>${fechaNacTexto}</span>
                                                    </div>

                                                    <div class="expediente-item">
                                                        <i class="bi bi-telephone"></i>
                                                        <span>${escapeHtml(p.telefono)}</span>
                                                    </div>

                                                    <div class="expediente-item">
                                                        <i class="bi bi-card-text"></i>
                                                        <span>${escapeHtml(p.documento_identidad ?? 'n/d')}</span>
                                                    </div>

                                                    <div class="expediente-item">
                                                        <i class="bi bi-envelope"></i>
                                                        <span>${escapeHtml(p.email ?? 'n/d')}</span>
                                                    </div>

                                                    <div class="expediente-item" style="grid-column: 1 / -1;">
                                                        <i class="bi bi-clipboard-pulse"></i>
                                                        <span>${antecedentes}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function formatGenero(g) {
        const generos = { 'M': 'Masculino', 'F': 'Femenino'};
        return escapeHtml(generos[g] ?? 'n/d');
    }

    function formatFecha(fecha) {
        if (!fecha) return 'n/d';
        const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        const partes = fecha.split('-');
        if (partes.length !== 3) return escapeHtml(fecha);
        const y = partes[0];
        const m = parseInt(partes[1], 10) - 1;
        const d = partes[2];
        return escapeHtml(`${d}-${meses[m] ?? 'n/d'}-${y}`);
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