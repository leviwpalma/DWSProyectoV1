<?php
/**
 * @var array $paciente
 * @var array $historial
 */
$title = 'Expediente ' . $paciente['codigo_expediente'] . ' | UnionDental';
require __DIR__ . '/../layout.php';
?>

<a href="/pacientes" class="btn btn-sm btn-outline-secondary mb-3">← Volver</a>

<h2>Expediente <?= htmlspecialchars($paciente['codigo_expediente']) ?></h2>

<div class="card mb-4">
    <div class="card-body row g-3">
        <div class="col-md-6"><strong>Nombres:</strong> <?= htmlspecialchars($paciente['nombres']) ?></div>
        <div class="col-md-6"><strong>Apellidos:</strong> <?= htmlspecialchars($paciente['apellidos']) ?></div>
        <div class="col-md-6"><strong>Teléfono:</strong> <?= htmlspecialchars($paciente['telefono']) ?></div>
        <div class="col-md-6"><strong>Correo:</strong> <?= htmlspecialchars($paciente['correo'] ?? '—') ?></div>
        <div class="col-md-6"><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($paciente['fecha_nacimiento']) ?></div>
    </div>
</div>

<h4>Historial de citas</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Servicio</th>
            <th>Médico</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($historial)): ?>
            <tr><td colspan="5" class="text-center text-muted">Sin citas registradas.</td></tr>
        <?php else: ?>
            <?php foreach ($historial as $c): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($c['fecha_hora_inicio'], 0, 10)) ?></td>
                    <td><?= htmlspecialchars(substr($c['fecha_hora_inicio'], 11, 5)) ?></td>
                    <td><?= htmlspecialchars($c['servicio'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['medico'] ?? '—') ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($c['estado']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../layout-footer.php'; ?>