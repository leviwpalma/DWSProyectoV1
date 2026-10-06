<?php $title = 'Pacientes | UnionDental';
require __DIR__ . '/../layout.php'; ?>

<div class="page-wrapper">

    <div class="breadcrumb-custom">
        <span>Pacientes</span> &nbsp;›&nbsp; Buscar pacientes
    </div>

    <h2 class="mb-4" style="font-weight: 600;">Lista de pacientes</h2>

    <div class="card-soft p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <label class="label-soft-lg mb-0">
                Busca pacientes por nombre, teléfono o documento
            </label>
            <a href="/pacientes/create" class="btn btn-primary btn-sm">
                Nuevo paciente +
            </a>
        </div>

        <div class="position-relative">
            <i class="bi bi-search position-absolute"
                style="left:16px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
            <input type="text"
                id="search"
                class="form-control input-soft ps-5"
                placeholder="Ingresa nombre, teléfono o N° de expediente"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>
    </div>

    <div class="card-soft">
        <div class="table-responsive">
            <table class="table table-clean mb-0">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>N° Expediente</th>
                        <th>Documento</th>
                        <th>Fecha de nacimiento</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="pacientes-table">
                    <?php if (empty($pacientes)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 1.5rem;"></i>
                                No se encuentran pacientes registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pacientes as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombres']) ?></td>
                                <td><?= htmlspecialchars($p['apellidos']) ?></td>
                                <td><?= htmlspecialchars($p['codigo_expediente']) ?></td>
                                <td><?= htmlspecialchars($p['documento_identidad'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($p['fecha_nacimiento'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($p['telefono']) ?></td>
                                <td><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                                <td class="text-end">
                                    <a href="/pacientes/<?= $p['id_paciente'] ?>/edit"
                                        class="text-warning me-2"
                                        title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/pacientes/<?= $p['id_paciente'] ?>/delete"
                                        class="text-danger"
                                        title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>