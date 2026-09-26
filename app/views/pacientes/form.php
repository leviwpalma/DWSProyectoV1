<?php
/**
 * @var array $paciente
 */
$editando = isset($paciente) && $paciente !== null;
$title = ($editando ? 'Editar' : 'Nuevo') . ' paciente | UnionDental';
$action = $editando ? "/pacientes/{$paciente['id_paciente']}/edit" : "/pacientes/create";
require __DIR__ . '/../layout.php';
?>

<h2 class="mb-4"><?= $editando ? 'Editar paciente' : 'Nuevo paciente' ?></h2>

<form method="POST" action="<?= $action ?>" class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Nombres *</label>
        <input type="text" name="nombres" class="form-control" required
               value="<?= htmlspecialchars($paciente['nombres'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos *</label>
        <input type="text" name="apellidos" class="form-control" required
               value="<?= htmlspecialchars($paciente['apellidos'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Teléfono *</label>
        <input type="text" name="telefono" class="form-control" required
               value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Correo</label>
        <input type="email" name="correo" class="form-control"
               value="<?= htmlspecialchars($paciente['correo'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Fecha de nacimiento *</label>
        <input type="date" name="fecha_nacimiento" class="form-control" required
               value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>">
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">
            <?= $editando ? 'Guardar cambios' : 'Crear paciente' ?>
        </button>
        <a href="/pacientes" class="btn btn-secondary">Cancelar</a>
    </div>

</form>

<?php require __DIR__ . '/../layout-footer.php'; ?>