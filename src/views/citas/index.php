<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas</title>
</head>
<body>
    <h1>Motor Transaccional de Citas</h1>

    <?php if (isset($_GET['mensaje'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_GET['mensaje']) ?></p>
    <?php endif; ?>

    <a href="/citas/crear">Agendar Nueva Cita</a>

    <table border="1" cellpadding="8" style="margin-top: 15px; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Servicio</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?= $cita['id'] ?></td>
                    <td><?= htmlspecialchars($cita['paciente_nombre'] ?? $cita['paciente_id']) ?></td>
                    <td><?= htmlspecialchars($cita['medico_nombre'] ?? $cita['medico_id']) ?></td>
                    <td><?= htmlspecialchars($cita['servicio_nombre'] ?? $cita['servicio_id']) ?></td>
                    <td><?= $cita['fecha_hora_inicio'] ?></td>
                    <td><?= $cita['fecha_hora_fin'] ?></td>
                    <td><strong><?= $cita['estado'] ?></strong></td>
                    <td>
                        <a href="/citas/editar?id=<?= $cita['id'] ?>">Reprogramar / Cambiar Estado</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>