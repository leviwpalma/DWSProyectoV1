<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
</head>
<body>
    <h1>Reprogramar o Cambiar Estado de Cita #<?= $cita['id'] ?></h1>

    <?php if (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="/citas/update" method="POST">
        <input type="hidden" name="id" value="<?= $cita['id'] ?>">

        <div>
            <label>ID Médico:</label>
            <input type="number" name="medico_id" value="<?= $cita['medico_id'] ?>" required>
        </div>
        <br>
        <div>
            <label>ID Servicio:</label>
            <input type="number" name="servicio_id" value="<?= $cita['servicio_id'] ?>" required>
        </div>
        <br>
        <div>
            <label>Fecha y Hora Inicio:</label>
            <input type="datetime-local" name="fecha_hora_inicio" value="<?= date('Y-m-d\TH:i', strtotime($cita['fecha_hora_inicio'])) ?>" required>
        </div>
        <br>
        <div>
            <label>Estado:</label>
            <select name="estado" required>
                <option value="Programada" <?= $cita['estado'] === 'Programada' ? 'selected' : '' ?>>Programada</option>
                <option value="Confirmada" <?= $cita['estado'] === 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                <option value="Atendida" <?= $cita['estado'] === 'Atendida' ? 'selected' : '' ?>>Atendida</option>
                <option value="Cancelada" <?= $cita['estado'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada (Libera horario)</option>
            </select>
        </div>
        <br>
        <button type="submit">Guardar Cambios</button>
        <a href="/citas">Regresar</a>
    </form>
</body>
</html>