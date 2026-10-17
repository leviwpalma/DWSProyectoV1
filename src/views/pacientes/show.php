<?php
/**
 * @var array $paciente
 * @var array $historial
 */
$title = 'Expediente ' . $paciente['codigo_expediente'] . ' | UnionDental';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos de pacientes -->
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

        <div class="breadcrumb-custom">
            <span>Pacientes</span> &nbsp;›&nbsp;
            Expediente <?= htmlspecialchars($paciente['codigo_expediente']) ?>
        </div>

        <div class="header">
            <div>
                <h1>Expediente N° <?= htmlspecialchars($paciente['codigo_expediente']) ?></h1>
                <p>Detalle clínico e historial de citas del paciente</p>
            </div>
            <div>
                <a href="/?url=pacientes/edit/<?= (int) $paciente['id_paciente'] ?>" class="btn btn-primary me-2">
                    Editar paciente
                </a>
                <a href="/?url=pacientes/index" class="btn btn-secondary">
                    ← Volver a lista
                </a>
            </div>
        </div>

        <!-- Tarjeta Informativa del Paciente -->
        <div class="panel mb-4">
            <div class="expediente-show-header">
                <div class="avatar-circle">
                    <i class="bi bi-person"></i>
                </div>
                <div class="expediente-show-title">
                    <h3><?= htmlspecialchars($paciente['nombres'] . ' ' . $paciente['apellidos']) ?></h3>
                    <span class="badge badge-info">N° Expediente: <?= htmlspecialchars($paciente['codigo_expediente']) ?></span>
                </div>
            </div>

            <div class="detail-grid">

                <div class="detail-item">
                    <span class="detail-label">Documento de identidad</span>
                    <span class="detail-value"><?= htmlspecialchars($paciente['documento_identidad'] ?? '—') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Fecha de nacimiento</span>
                    <span class="detail-value"><?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '—') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Género</span>
                    <span class="detail-value">
                        <?php
                        $generos = ['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'];
                        echo htmlspecialchars($generos[$paciente['genero'] ?? ''] ?? '—');
                        ?>
                    </span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Teléfono</span>
                    <span class="detail-value"><?= htmlspecialchars($paciente['telefono']) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Correo electrónico</span>
                    <span class="detail-value"><?= htmlspecialchars($paciente['email'] ?? '—') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Dirección</span>
                    <span class="detail-value"><?= htmlspecialchars($paciente['direccion'] ?? '—') ?></span>
                </div>

                <div class="detail-item full-width">
                    <span class="detail-label">Antecedentes médicos</span>
                    <p class="detail-text">
                        <?= nl2br(htmlspecialchars($paciente['antecedentes_medicos'] ?? 'Sin observaciones')) ?>
                    </p>
                </div>

            </div>
        </div>

        <!-- Historial de Citas -->
        <div class="panel">
            <h3 class="panel-subtitle">Historial de citas</h3>

            <div class="table-responsive">
                <table class="table">
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
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="bi bi-calendar-x" style="font-size: 1.8rem; display: block; margin-bottom: 8px;"></i>
                                    Sin citas registradas para este paciente.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historial as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars(substr($c['fecha_hora_inicio'], 0, 10)) ?></td>
                                    <td><?= htmlspecialchars(substr($c['fecha_hora_inicio'], 11, 5)) ?></td>
                                    <td><?= htmlspecialchars($c['servicio'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($c['medico'] ?? '—') ?></td>
                                    <td>
                                        <?php
                                        $estadoClass = 'badge-neutral';
                                        $estado = strtolower($c['estado'] ?? '');
                                        if (in_array($estado, ['completada', 'confirmada', 'atendida'])) {
                                            $estadoClass = 'badge-success';
                                        } elseif (in_array($estado, ['cancelada', 'inactiva'])) {
                                            $estadoClass = 'badge-danger';
                                        } elseif (in_array($estado, ['pendiente', 'programada'])) {
                                            $estadoClass = 'badge-warning';
                                        }
                                        ?>
                                        <span class="badge <?= $estadoClass ?>">
                                            <?= htmlspecialchars($c['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

</body>
</html>