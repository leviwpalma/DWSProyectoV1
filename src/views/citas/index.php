<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario y Citas - UnionDental</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f4f7fb; color: #1f2937; }
        .app { display: flex; min-height: 100vh; }
        .sidebar { width: 230px; background: #ffffff; border-right: 1px solid #e5e7eb; padding: 30px 20px; display: flex; flex-direction: column; flex-shrink: 0; }
        .logo { font-size: 24px; font-weight: 700; margin-bottom: 40px; }
        .logo .union { color: #3b82f6; }
        .logo .dental { color: #1f2937; }
        .menu { display: flex; flex-direction: column; gap: 8px; }
        .menu a { text-decoration: none; color: #4b5563; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        .menu a:hover { background: #eff6ff; color: #2563eb; }
        .menu a.active { background: #dbeafe; color: #2563eb; font-weight: 600; }
        .main { flex: 1; padding: 35px; }
        .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .btn { display: inline-block; padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-success { background: #059669; color: white; }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .panel { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        
        /* Barra de filtros */
        .filtros-bar { background: #ffffff; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .filtros-tabs { display: flex; gap: 8px; }
        .filtros-tabs a { text-decoration: none; padding: 7px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #64748b; background: #f8fafc; }
        .filtros-tabs a.active { background: #2563eb; color: #ffffff; }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f9fafb; color: #4b5563; font-weight: 600; font-size: 14px; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        tr:hover td { background: #fbfcfe; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-programada { background: #dbeafe; color: #1d4ed8; }
        .badge-confirmada { background: #dcfce7; color: #166534; }
        .badge-atendida { background: #e2e8f0; color: #334155; }
        .badge-cancelada { background: #fee2e2; color: #991b1b; }
        
        .user-box { margin-top: auto; padding: 14px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 15px; }
        .user-name { font-weight: 700; color: #1f2937; }
        .user-role { color: #6b7280; margin-top: 4px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        
        /* Modal */
        .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.45); z-index: 1000; align-items: center; justify-content: center; }
        .modal-card { background: white; border-radius: 12px; width: 100%; max-width: 520px; padding: 26px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .form-group select, .form-group input, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
    </style>
</head>
<body>
<div class="app">
    
    <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <main class="main">
        <div class="header">
            <div>
                <h1>Agenda y Citas</h1>
                <p style="color: #64748b; margin-top: 4px;">Monitoreo del calendario clínico y citas programadas</p>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">+ Agendar Cita</button>
        </div>

        <!-- Filtros Rápidos -->
        <div class="filtros-bar">
            <div class="filtros-tabs">
                <a href="?url=citas/index&estado=todos<?= !empty($filtroFecha) ? '&fecha='.$filtroFecha : '' ?>" class="<?= $filtroEstado === 'todos' ? 'active' : '' ?>">Todas</a>
                <a href="?url=citas/index&estado=pendientes<?= !empty($filtroFecha) ? '&fecha='.$filtroFecha : '' ?>" class="<?= $filtroEstado === 'pendientes' ? 'active' : '' ?>">Pendientes</a>
                <a href="?url=citas/index&estado=atendidas<?= !empty($filtroFecha) ? '&fecha='.$filtroFecha : '' ?>" class="<?= $filtroEstado === 'atendidas' ? 'active' : '' ?>">Atendidas</a>
            </div>

            <form method="GET" action="" style="display: flex; gap: 8px; align-items: center;">
                <input type="hidden" name="url" value="citas/index">
                <input type="hidden" name="estado" value="<?= htmlspecialchars($filtroEstado) ?>">
                <label style="font-size: 13px; font-weight: 600; color: #64748b;">Día:</label>
                <input type="date" name="fecha" value="<?= htmlspecialchars($filtroFecha) ?>" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;" onchange="this.form.submit()">
                <?php if (!empty($filtroFecha)): ?>
                    <a href="?url=citas/index&estado=<?= $filtroEstado ?>" style="color: #ef4444; font-size: 12px; text-decoration: none; font-weight: 600;">Limpiar fecha</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Paciente</th>
                        <th>Teléfono</th>
                        <th>Doctor</th>
                        <th>Procedimiento</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citas)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #6b7280; padding: 35px;">
                                No se encontraron citas con los filtros seleccionados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $rolSesion = $_SESSION['usuario']['rol'] ?? '';
                        $idMedSesion = $idMedicoSesion ?? 0;
                        ?>
                        <?php foreach ($citas as $c): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars(date('d/m/Y', strtotime($c['fecha_hora_inicio']))) ?></strong><br>
                                    <small style="color: #6b7280;">
                                        <?= htmlspecialchars(date('H:i', strtotime($c['fecha_hora_inicio']))) ?> - <?= htmlspecialchars(date('H:i', strtotime($c['fecha_hora_fin']))) ?>
                                    </small>
                                </td>
                                <td><?= htmlspecialchars($c['paciente']) ?></td>
                                <td><?= htmlspecialchars($c['paciente_telefono']) ?></td>
                                <td><?= htmlspecialchars($c['medico']) ?></td>
                                <td><?= htmlspecialchars($c['servicio']) ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($c['estado']) ?>">
                                        <?= htmlspecialchars($c['estado']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($c['estado'] === 'Atendida'): ?>
                                        <!-- Ya fue atendida: Ver o Descargar Expediente -->
                                        <a href="?url=consultas/atender/<?= (int)$c['id_cita'] ?>" class="btn btn-secondary" title="Ver evolución clínica">
                                            👁️ Ver Consulta
                                        </a>
                                        <a href="?url=consultas/imprimir/<?= (int)$c['id_cita'] ?>" target="_blank" class="btn btn-success" title="Imprimir / Descargar PDF">
                                            📄 PDF
                                        </a>
                                    <?php else: ?>
                                        <!-- Pendiente: Atender -->
                                        <?php if ($rolSesion === 'Doctor' && (int)$c['id_medico'] === $idMedSesion): ?>
                                            <a href="?url=consultas/atender/<?= (int)$c['id_cita'] ?>" class="btn btn-primary">
                                                🩺 Atender Cita
                                            </a>
                                        <?php else: ?>
                                            <span style="font-size: 12px; color: #94a3b8; font-style: italic;">
                                                <?= $rolSesion === 'Doctor' ? 'No asignado' : 'Pendiente' ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal para agendar cita -->
<div class="modal-backdrop" id="modalCita">
    <div class="modal-card">
        <h3 style="margin-top: 0;">Agendar Nueva Cita</h3>
        <form method="POST" action="?url=citas/agendar">
            <div class="form-group">
                <label for="id_paciente">Paciente:</label>
                <select name="id_paciente" id="id_paciente" required>
                    <option value="">Seleccione el paciente</option>
                    <?php foreach ($pacientes as $pac): ?>
                        <option value="<?= (int)$pac['id_paciente'] ?>">
                            <?= htmlspecialchars($pac['nombre_completo']) ?> (<?= htmlspecialchars($pac['codigo_expediente']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_medico">Médico Tratante:</label>
                <select name="id_medico" id="id_medico" required>
                    <option value="">Seleccione el médico</option>
                    <?php foreach ($medicos as $med): ?>
                        <option value="<?= (int)$med['id_medico'] ?>">
                            Dr(a). <?= htmlspecialchars($med['nombre_completo']) ?> - <?= htmlspecialchars($med['especialidad']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_servicio">Servicio / Procedimiento:</label>
                <select name="id_servicio" id="id_servicio" required>
                    <option value="">Seleccione el servicio</option>
                    <?php foreach ($servicios as $serv): ?>
                        <option value="<?= (int)$serv['id_servicio'] ?>">
                            <?= htmlspecialchars($serv['nombre']) ?> (<?= (int)$serv['duracion_minutos'] ?> min - $<?= number_format((float)$serv['precio_ref'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_hora">Fecha y Hora de Inicio:</label>
                <input type="datetime-local" name="fecha_hora" id="fecha_hora" required>
            </div>

            <div class="form-group">
                <label for="motivo_consulta">Motivo u observaciones:</label>
                <textarea name="motivo_consulta" id="motivo_consulta" rows="2" placeholder="Opcional..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cita</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal() { document.getElementById('modalCita').style.display = 'flex'; }
function cerrarModal() { document.getElementById('modalCita').style.display = 'none'; }
</script>
</body>
</html>