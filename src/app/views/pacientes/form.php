<?php

/**
 * @var array $paciente
 */
$editando = isset($paciente) && $paciente !== null && !isset($eliminando);
$eliminando = isset($eliminando) && $eliminando === true;
$title = ($eliminando ? 'Eliminar' : ($editando ? 'Editar' : 'Nuevo')) . ' paciente | UnionDental';
$action = $editando ? "/pacientes/{$paciente['id_paciente']}/edit" : "/pacientes/create";
require __DIR__ . '/../layout.php';
?>

<div class="page-wrapper">

    <h2 class="mb-4" style="font-weight: 600;">
        <?= $eliminando ? 'Eliminar paciente' : ($editando ? 'Editar paciente' : 'Nuevo paciente') ?>
    </h2>

    <div class="card-soft p-4 p-md-5">

        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center"
                style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 18px;">
                <i class="bi bi-person" style="font-size: 2.2rem; color: #334155;"></i>
            </div>
        </div>

        <form method="POST" action="<?= $action ?>" class="row g-4">
            <div class="col-md-6">
                <label class="label-soft">Nombres</label>
                <input type="text" name="nombres" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['nombres'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="label-soft">Apellidos</label>
                <input type="text" name="apellidos" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['apellidos'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="label-soft">Documento de identidad</label>
                <input type="text" name="documento_identidad" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['documento_identidad'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="label-soft">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="label-soft">Género</label>
                <select name="genero" class="form-select input-soft" required>
                    <option value="">Seleccionar</option>
                    <option value="M" <?= ($paciente['genero'] ?? '') === 'M'    ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= ($paciente['genero'] ?? '') === 'F'    ? 'selected' : '' ?>>Femenino</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="label-soft">Teléfono</label>
                <input type="text" name="telefono" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="label-soft">Correo</label>
                <input type="email" name="email" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="label-soft">Dirección</label>
                <input type="text" name="direccion" class="form-control input-soft" required
                    value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>">
            </div>

            <div class="col-12">
                <label class="label-soft">Antecedentes médicos</label>
                <textarea name="antecedentes_medicos" class="form-control input-soft" rows="3" required><?= htmlspecialchars($paciente['antecedentes_medicos'] ?? '') ?></textarea>
            </div>

            <div class="col-12 text-center pt-4">
                <?php if ($eliminando): ?>
                    <button type="button"
                        class="btn btn-danger px-5"
                        data-bs-toggle="modal"
                        data-bs-target="#modalConfirmarEliminar">
                        Eliminar paciente
                    </button>
                    <a href="/pacientes" class="btn btn-link text-muted ms-2">Cancelar</a>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary px-5">
                        <?= $editando ? 'Guardar cambios' : 'Agregar paciente' ?>
                    </button>
                    <a href="/pacientes" class="btn btn-link text-muted ms-2">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

</div>

<?php if ($eliminando): ?>
    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Eliminar paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0">Estás seguro de eliminar a este paciente?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Regresar</button>
                    <form method="POST" action="/pacientes/<?= $paciente['id_paciente'] ?>/delete" class="d-inline">
                        <button type="submit" class="btn btn-danger px-4">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('modalConfirmarEliminar')).show();
        });
    </script>
<?php endif; ?>

<?php require __DIR__ . '/../layout-footer.php'; ?>