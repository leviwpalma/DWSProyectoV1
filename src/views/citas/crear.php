<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita</title>
</head>
<body>
    <h1>Agendar Cita</h1>

    <?php if (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="/citas/store" method="POST">
        <div>
            <label>ID Paciente:</label>
            <input type="number" name="paciente_id" required>
        </div>
        <br>
        <div>
            <label>ID Médico:</label>
            <input type="number" name="medico_id" required>
        </div>
        <br>
        <div>
            <label>ID Servicio:</label>
            <input type="number" name="servicio_id" required>
        </div>
        <br>
        <div>
            <label>Fecha y Hora Inicio:</label>
            <input type="datetime-local" name="fecha_hora_inicio" required>
        </div>
        <br>
        <button type="submit">Agendar Cita</button>
        <a href="/citas">Cancelar</a>
    </form>
</body>
</html>