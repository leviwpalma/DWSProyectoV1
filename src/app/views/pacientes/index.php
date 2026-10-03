<?php $title = 'Pacientes | UnionDental';
require __DIR__ . '/../layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Lista de pacientes</h2>
        <small class="text-muted">Gestión de expedientes clínicos</small>
    </div>
    <a href="/pacientes/create" class="btn btn-primary">
        + Nuevo paciente
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <label class="form-label text-muted small">Buscar por nombre, teléfono, documento o N° de expediente</label>
        <input type="text"
            id="search"
            class="form-control form-control-lg"
            placeholder="Escribe para buscar..."
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Expediente</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="pacientes-table">
                <?php if (empty($pacientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <div class="fs-5 mb-1">📋</div>
                            No se encuentran pacientes registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pacientes as $p): ?>
                        <tr>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis"><?= htmlspecialchars($p['codigo_expediente']) ?></span></td>
                            <td><?= htmlspecialchars($p['nombres']) ?></td>
                            <td><?= htmlspecialchars($p['apellidos']) ?></td>
                            <td><?= htmlspecialchars($p['documento_identidad'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($p['telefono']) ?></td>
                            <td><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                            <td class="text-end">
                                <a href="/pacientes/<?= $p['id_paciente'] ?>"
                                    class="btn btn-sm btn-outline-info" title="Ver expediente">Ver</a>
                                <a href="/pacientes/<?= $p['id_paciente'] ?>/edit"
                                    class="btn btn-sm btn-outline-warning" title="Editar">Editar</a>
                                <form method="POST"
                                    action="/pacientes/<?= $p['id_paciente'] ?>/delete"
                                    onsubmit="return confirm('¿Desactivar?')"
                                    class="d-inline">
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>