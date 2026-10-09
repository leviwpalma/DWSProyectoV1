<?php
/**
 * @var array $paciente
 * @var array $historial
 */
$titulo = 'Expediente ' . htmlspecialchars($paciente['codigo_expediente']) . ' | UnionDental';
require __DIR__ . '/../layout.php';
?>

<div class="page-wrapper">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="?url=pacientes/index" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Pacientes
        </a>
        <div class="d-flex gap-2">
            <a href="?url=pacientes/edit/<?= (int)$paciente['id_paciente'] ?>" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="?url=pacientes/eliminar/<?= (int)$paciente['id_paciente'] ?>" class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i> Eliminar
            </a>
        </div>
    </div>

    <h2 class="mb-3">Expediente <?= htmlspecialchars($paciente['codigo_expediente']) ?></h2>

    <div class="card card-soft mb-4">
        <div class="card-body row g-3">
            <div class="col-md-6"><strong>Nombres:</strong> <?= htmlspecialchars($paciente['nombres']) ?></div>
            <div class="col-md-6"><strong>Apellidos:</strong> <?= htmlspecialchars($paciente['apellidos']) ?></div>
            <div class="col-md-6"><strong>Documento:</strong> <?= htmlspecialchars($paciente['documento_identidad'] ?? '—') ?></div>
            <div class="col-md-6"><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '—') ?></div>
            <div class="col-md-6"><strong>Género:</strong>
                <?php
                $generos = ['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'];
                echo htmlspecialchars($generos[$paciente['genero'] ?? ''] ?? '—');
                ?>
            </div>
            <div class="col-md-6"><strong>Teléfono:</strong> <?= htmlspecialchars($paciente['telefono']) ?></div>
            <div class="col-md-6"><strong>Correo:</strong> <?= htmlspecialchars($paciente['email'] ?? '—') ?></div>
            <div class="col-md-6"><strong>Dirección:</strong> <?= htmlspecialchars($paciente['direccion'] ?? '—') ?></div>
            <div class="col-12">
                <strong>Antecedentes médicos:</strong><br>
                <?= nl2br(htmlspecialchars($paciente['antecedentes_medicos'] ?? 'Sin observaciones')) ?>
            </div>
        </div>
    </div>

    <!-- Historial Clínico de Consultas -->
        <div class="panel" style="margin-top: 25px;">
            <h2 style="font-size: 18px; margin-bottom: 16px;">Historial Clínico y Consultas Atendidas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha Consulta</th>
                        <th>Procedimiento</th>
                        <th>Médico Tratante</th>
                        <th>Diagnóstico / Resultado</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Expediente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citas)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">
                                El paciente aún no registra consultas en su historial.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($citas as $c): ?>
                            <tr>
                                <td><strong><?= date('d/m/Y H:i', strtotime($c['fecha_hora_inicio'])) ?></strong></td>
                                <td><?= htmlspecialchars($c['servicio']) ?></td>
                                <td>Dr(a). <?= htmlspecialchars($c['medico']) ?></td>
                                <td>
                                    <?php if (!empty($c['diagnostico'])): ?>
                                        <span title="<?= htmlspecialchars($c['diagnostico']) ?>">
                                            <?= htmlspecialchars(mb_strimwidth($c['diagnostico'], 0, 45, '...')) ?>
                                        </span>
                                    <?php else: ?>
                                        <em style="color: #94a3b8;">Sin diagnóstico registrado</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower($c['estado']) ?>">
                                        <?= htmlspecialchars($c['estado']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($c['estado'] === 'Atendida'): ?>
                                        <a href="?url=consultas/imprimir/<?= (int)$c['id_cita'] ?>" target="_blank" 
                                           style="padding: 6px 12px; background: #059669; color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;">
                                            📄 Descargar PDF
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 13px;">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

<?php require __DIR__ . '/../layout-footer.php'; ?>