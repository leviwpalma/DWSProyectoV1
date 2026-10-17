<?php
/**
 * @var array|null $paciente
 */

$editando = isset($paciente)
    && $paciente !== null
    && !isset($eliminando);

$eliminando = isset($eliminando)
    && $eliminando === true;

$title = (
    $eliminando
        ? 'Eliminar'
        : ($editando ? 'Editar' : 'Nuevo')
) . ' paciente | UnionDental';

$action = $editando
    ? '/?url=pacientes/edit/' . (int) $paciente['id_paciente']
    : '/?url=pacientes/create';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/pacientes.css" rel="stylesheet">
</head>
<body>

<div class="app">

    <!-- Sidebar Navegación Lateral -->
    <aside class="sidebar">

        <div class="logo">
            <span class="union">Union</span><span class="dental">Dental</span>
        </div>

        <nav class="menu">
            <a href="/?url=home/index">Inicio</a>
            <a href="/?url=medicos/index">Médicos</a>
            <a href="/?url=medicos/horarios">Horarios</a>
            <a href="#">Calendario</a>
            <a href="/?url=pacientes/index" class="active">Pacientes</a>
            <a href="/?url=servicio/index">Servicios</a>
            <a href="#">Configuración</a>
        </nav>

        <?php if (!empty($_SESSION['usuario'])): ?>
            <div class="user-box">
                <div class="user-name">
                    <?= htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']) ?>
                </div>
                <div class="user-role">
                    <?= htmlspecialchars($_SESSION['usuario']['rol']) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="logout">
            <a href="/?url=auth/logout">Cerrar sesión</a>
        </div>

    </aside>

    <!-- Contenido Principal -->
    <main class="main">

        <div class="header">
            <div>
                <h1>
                    <?= $eliminando
                        ? 'Eliminar paciente'
                        : ($editando ? 'Editar paciente' : 'Nuevo paciente') ?>
                </h1>
                <p>
                    <?= $eliminando
                        ? 'Confirme la eliminación del registro del paciente'
                        : ($editando ? 'Modifique los datos del paciente' : 'Complete el formulario para registrar un paciente') ?>
                </p>
            </div>
            <a href="/?url=pacientes/index" class="btn btn-secondary">
                ← Volver a lista
            </a>
        </div>

        <div class="panel">

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <strong>Corrige los siguientes errores:</strong>
                    <ul class="error-list">
                        <?php foreach ($errores as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="avatar-header">
                <div class="avatar-circle">
                    <i class="bi bi-person"></i>
                </div>
            </div>

            <form method="POST" action="<?= htmlspecialchars($action) ?>">

                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Nombres</label>
                        <input
                            type="text"
                            name="nombres"
                            class="form-control"
                            required
                            maxlength="100"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                            placeholder="Ingrese los nombres del paciente"
                            value="<?= htmlspecialchars($paciente['nombres'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellidos</label>
                        <input
                            type="text"
                            name="apellidos"
                            class="form-control"
                            required
                            maxlength="100"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                            placeholder="Ingrese los apellidos del paciente"
                            value="<?= htmlspecialchars($paciente['apellidos'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Documento de identidad</label>
                        <input
                            type="text"
                            name="documento_identidad"
                            class="form-control"
                            required
                            maxlength="12"
                            pattern="(\d{9}|[A-Z]{1,3}\d{6,9})"
                            placeholder="Ingrese el documento de identidad"
                            value="<?= htmlspecialchars($paciente['documento_identidad'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Fecha de nacimiento</label>
                        <input
                            type="date"
                            name="fecha_nacimiento"
                            class="form-control"
                            required
                            max="<?= date('Y-m-d') ?>"
                            value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Género</label>
                        <select name="genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="M" <?= ($paciente['genero'] ?? '') === 'M' ? 'selected' : '' ?>>
                                Masculino
                            </option>
                            <option value="F" <?= ($paciente['genero'] ?? '') === 'F' ? 'selected' : '' ?>>
                                Femenino
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input
                            type="text"
                            name="telefono"
                            class="form-control"
                            required
                            maxlength="9"
                            pattern="\d{4}-\d{4}"
                            placeholder="Ingrese el teléfono del paciente"
                            value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required
                            maxlength="150"
                            placeholder="Ingrese el correo del paciente"
                            value="<?= htmlspecialchars($paciente['email'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <input
                            type="text"
                            name="direccion"
                            class="form-control"
                            maxlength="255"
                            placeholder="Ingrese la dirección del paciente"
                            value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Antecedentes médicos</label>
                        <textarea
                            name="antecedentes_medicos"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            placeholder="Ingrese los antecedentes médicos del paciente"
                        ><?= htmlspecialchars($paciente['antecedentes_medicos'] ?? '') ?></textarea>
                    </div>

                </div>

                <div class="form-actions">
                    <?php if ($eliminando): ?>
                        <button
                            type="button"
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modalConfirmarEliminar"
                        >
                            Eliminar paciente
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary">
                            <?= $editando ? 'Guardar cambios' : 'Agregar paciente' ?>
                        </button>
                    <?php endif; ?>
                </div>

            </form>

        </div>

    </main>

</div>

<?php if ($eliminando): ?>
    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de eliminar a este paciente?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">
                        Regresar
                    </button>
                    <form
                        method="POST"
                        action="/?url=pacientes/desactivar/<?= (int) $paciente['id_paciente'] ?>"
                        style="display: inline;"
                    >
                        <button type="submit" class="btn btn-danger">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalConfirmarEliminar');
            if (modal && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(modal).show();
            }
        });
    </script>
<?php endif; ?>

</body>
</html>