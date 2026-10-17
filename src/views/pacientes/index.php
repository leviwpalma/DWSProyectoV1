<?php
$title = 'Pacientes | UnionDental';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/pacientes.css">
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
                <h1>Gestión de Pacientes</h1>
                <p>Búsqueda y expediente clínico de pacientes registrados</p>
            </div>
            <a href="/?url=pacientes/create" class="btn btn-primary">
                Nuevo paciente +
            </a>
        </div>

        <!-- Panel de Filtro / Búsqueda -->
        <div class="panel mb-4">
            <label class="search-label">
                Busca pacientes por nombre, teléfono o documento
            </label>
            <div class="search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input
                    type="text"
                    id="search"
                    class="form-control-search"
                    placeholder="Ingresa nombre, teléfono o N° de expediente"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                >
            </div>
        </div>

        <!-- Tabla de Pacientes -->
        <div class="panel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>N° Expediente</th>
                            <th>Documento</th>
                            <th>Fecha Nac.</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pacientes-table">
                        <?php if (empty($pacientes)): ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="bi bi-inbox" style="font-size: 1.8rem; display: block; margin-bottom: 8px;"></i>
                                    No se encuentran pacientes registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pacientes as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['nombres']) ?></td>
                                    <td><?= htmlspecialchars($p['apellidos']) ?></td>
                                    <td><strong><?= htmlspecialchars($p['codigo_expediente']) ?></strong></td>
                                    <td><?= htmlspecialchars($p['documento_identidad'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($p['fecha_nacimiento'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($p['telefono']) ?></td>
                                    <td><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                                    <td class="actions-cell">
                                        <!-- Ver expediente -->
                                        <a
                                            href="#"
                                            class="action-icon icon-view"
                                            data-bs-toggle="modal"
                                            data-bs-target="#expediente-<?= (int) $p['id_paciente'] ?>"
                                            title="Ver expediente"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Editar -->
                                        <a
                                            href="/?url=pacientes/edit/<?= (int) $p['id_paciente'] ?>"
                                            class="action-icon icon-edit"
                                            title="Editar"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <a
                                            href="/?url=pacientes/eliminar/<?= (int) $p['id_paciente'] ?>"
                                            class="action-icon icon-delete"
                                            title="Eliminar"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal de Expediente -->
                                <div
                                    class="modal fade expediente-modal"
                                    id="expediente-<?= (int) $p['id_paciente'] ?>"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0">
                                            <div class="modal-header border-0 pb-0">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body pt-0">
                                                <div class="expediente-card">
                                                    <div class="expediente-side">
                                                        <div>
                                                            <div class="exp-label">
                                                                Expediente No.<br>
                                                                <strong><?= htmlspecialchars($p['codigo_expediente']) ?></strong>
                                                            </div>
                                                            <div class="exp-avatar">
                                                                <i class="bi bi-person"></i>
                                                            </div>
                                                        </div>
                                                        <div class="exp-dots">• • •</div>
                                                    </div>

                                                    <div class="expediente-main">
                                                        <h4>
                                                            <?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']) ?>
                                                        </h4>

                                                        <div class="expediente-grid">
                                                            <div class="expediente-item">
                                                                <i class="bi bi-gender-ambiguous"></i>
                                                                <span>
                                                                    <?php
                                                                    $generos = [
                                                                        'M' => 'Masculino',
                                                                        'F' => 'Femenino',
                                                                        'Otro' => 'Otro'
                                                                    ];
                                                                    echo htmlspecialchars($generos[$p['genero'] ?? ''] ?? 'n/d');
                                                                    ?>
                                                                </span>
                                                            </div>

                                                            <div class="expediente-item">
                                                                <i class="bi bi-geo-alt"></i>
                                                                <span><?= htmlspecialchars($p['direccion'] ?? 'Sin dirección') ?></span>
                                                            </div>

                                                            <div class="expediente-item">
                                                                <i class="bi bi-calendar"></i>
                                                                <span>
                                                                    <?php
                                                                    if (!empty($p['fecha_nacimiento'])) {
                                                                        $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                                                                        [$y, $m, $d] = explode('-', $p['fecha_nacimiento']);
                                                                        $mesIndex = ((int)$m > 0) ? (int)$m - 1 : 0;
                                                                        echo htmlspecialchars($d . '-' . ($meses[$mesIndex] ?? 'n/d') . '-' . $y);
                                                                    } else {
                                                                        echo 'n/d';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>

                                                            <div class="expediente-item">
                                                                <i class="bi bi-telephone"></i>
                                                                <span><?= htmlspecialchars($p['telefono']) ?></span>
                                                            </div>

                                                            <div class="expediente-item">
                                                                <i class="bi bi-card-text"></i>
                                                                <span><?= htmlspecialchars($p['documento_identidad'] ?? 'n/d') ?></span>
                                                            </div>

                                                            <div class="expediente-item">
                                                                <i class="bi bi-envelope"></i>
                                                                <span><?= htmlspecialchars($p['email'] ?? 'n/d') ?></span>
                                                            </div>

                                                            <div class="expediente-item" style="grid-column: 1 / -1;">
                                                                <i class="bi bi-clipboard-pulse"></i>
                                                                <span><?= htmlspecialchars($p['antecedentes_medicos'] ?? 'Sin antecedentes registrados') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/pacientes.js"></script>

</body>
</html>