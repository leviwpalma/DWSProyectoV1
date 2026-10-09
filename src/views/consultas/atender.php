<?php
$rolSesion = $_SESSION['usuario']['rol'] ?? '';
$idUsuarioSesion = (int)($_SESSION['usuario']['id_usuario'] ?? 0);

// Verificar si el usuario en sesión es exactamente el médico de esta cita
$esMedicoDeEstaCita = ($rolSesion === 'Doctor' && isset($cita['id_usuario']) && (int)$cita['id_usuario'] === $idUsuarioSesion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Odontológica - UnionDental</title>
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
        .main { flex: 1; padding: 35px; max-width: 1100px; }
        .header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 26px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        
        .ficha-paciente { background: #ffffff; padding: 20px 24px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 24px; border-left: 5px solid #2563eb; }
        .ficha-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 10px; }
        .ficha-item small { display: block; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .ficha-item strong { font-size: 15px; color: #0f172a; }

        .form-card { background: #ffffff; padding: 26px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        input, textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: inherit; }
        input:focus, textarea:focus { outline: none; border-color: #2563eb; }
        input[readonly], textarea[readonly] { background: #f8fafc; color: #475569; border-color: #e2e8f0; cursor: not-allowed; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-info { background: #fef3c7; color: #92400e; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        
        .user-box { margin-top: auto; padding: 14px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 15px; }
        .user-name { font-weight: 700; color: #1f2937; }
        .user-role { color: #6b7280; margin-top: 4px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="logo">
            <span class="union">Union</span><span class="dental">Dental</span>
        </div>
        <nav class="menu">
            <a href="?url=home/index">Inicio</a>
            <?php if ($rolSesion === 'Administrador'): ?>
                <a href="?url=medicos/index">Médicos</a>
            <?php endif; ?>
            <a href="?url=medicos/horarios">Disponibilidad</a>
            <a href="?url=citas/index" class="active">Calendario</a>
            <a href="?url=pacientes/index">Pacientes</a>
            <?php if ($rolSesion === 'Administrador'): ?>
                <a href="?url=servicio/index">Servicios</a>
            <?php endif; ?>
            <a href="?url=configuracion/index">Configuración</a>
        </nav>
        <?php if (!empty($_SESSION['usuario'])): ?>
            <div class="user-box">
                <div class="user-name"><?= htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']) ?></div>
                <div class="user-role"><?= htmlspecialchars($_SESSION['usuario']['rol']) ?></div>
            </div>
        <?php endif; ?>
        <div class="logout"><a href="?url=auth/logout">Cerrar sesión</a></div>
    </aside>

    <main class="main">
        <div class="header">
            <div>
                <h1>Evolución y Consulta Dental</h1>
                <p style="color: #64748b; margin-top: 4px;">Cita #<?= (int)$cita['id_cita'] ?> &bull; <?= htmlspecialchars($cita['servicio_nombre']) ?></p>
            </div>
            <div style="display: flex; gap: 10px;">
                <?php if ($consultaExistente): ?>
                    <a href="?url=consultas/imprimir/<?= (int)$cita['id_cita'] ?>" target="_blank" class="btn btn-secondary">🖨️ Imprimir / PDF</a>
                <?php endif; ?>
                <a href="?url=citas/index" class="btn btn-secondary">← Volver a Citas</a>
            </div>
        </div>

        <?php if (isset($_GET['exito'])): ?>
            <div class="alert-success">✓ Consulta guardada y cita actualizada a "Atendida".</div>
        <?php endif; ?>

        <?php if (!$esMedicoDeEstaCita): ?>
            <div class="alert-info">
                ℹ️ <strong>Modo Lectura:</strong> Solo el Doctor asignado a esta cita (Dr(a). <?= htmlspecialchars($cita['medico_nombre']) ?>) tiene permisos clínicos para redactar o actualizar la evolución médica.
            </div>
        <?php endif; ?>

        <!-- Ficha de Datos Precargados -->
        <div class="ficha-paciente">
            <div style="font-weight: 700; color: #1e40af; margin-bottom: 8px; font-size: 13px;">INFORMACIÓN DEL PACIENTE (AUTOMÁTICA)</div>
            <div class="ficha-grid">
                <div class="ficha-item">
                    <small>Paciente</small>
                    <strong><?= htmlspecialchars($cita['nombres'] . ' ' . $cita['apellidos']) ?></strong>
                </div>
                <div class="ficha-item">
                    <small>N° Expediente</small>
                    <strong><?= htmlspecialchars($cita['codigo_expediente']) ?></strong>
                </div>
                <div class="ficha-item">
                    <small>Edad al momento de consulta</small>
                    <strong><?= $edad ?></strong>
                </div>
                <div class="ficha-item">
                    <small>DUI / Documento</small>
                    <strong><?= htmlspecialchars($cita['documento_identidad'] ?? '—') ?></strong>
                </div>
            </div>
            <?php if (!empty($cita['antecedentes_medicos'])): ?>
                <div style="margin-top: 12px; font-size: 13px; color: #b91c1c; background: #fee2e2; padding: 8px 12px; border-radius: 6px;">
                    <strong>⚠️ Alergias / Antecedentes Médicos:</strong> <?= htmlspecialchars($cita['antecedentes_medicos']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Formulario Clínico -->
        <div class="form-card">
            <form method="POST" action="?url=consultas/guardar">
                <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>">
                <input type="hidden" name="id_paciente" value="<?= (int)$cita['id_paciente'] ?>">
                <input type="hidden" name="id_medico" value="<?= (int)$cita['id_medico'] ?>">

                <div class="form-group">
                    <label for="motivo_consulta">Motivo de la Consulta *</label>
                    <input type="text" id="motivo_consulta" name="motivo_consulta" 
                           value="<?= htmlspecialchars($consultaExistente['motivo_consulta'] ?? $cita['motivo_consulta'] ?? '') ?>"
                           <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?> required>
                </div>

                <div class="form-group">
                    <label for="sintomas">Sintomatología y Anamnesis</label>
                    <textarea id="sintomas" name="sintomas" rows="2" 
                              placeholder="Dolor, inflamación, sensibilidad al frío/calor..."
                              <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?>><?= htmlspecialchars($consultaExistente['sintomas'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="diagnostico">Diagnóstico Dental *</label>
                    <textarea id="diagnostico" name="diagnostico" rows="2" 
                              placeholder="Piezas dentales afectadas, patología detectada..."
                              <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?> required><?= htmlspecialchars($consultaExistente['diagnostico'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="tratamiento_realizado">Procedimiento / Tratamiento Realizado *</label>
                    <textarea id="tratamiento_realizado" name="tratamiento_realizado" rows="3" 
                              placeholder="Procedimiento odontológico ejecutado en sesión..."
                              <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?> required><?= htmlspecialchars($consultaExistente['tratamiento_realizado'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="receta_medica">Prescripción / Medicación Recetada</label>
                        <textarea id="receta_medica" name="receta_medica" rows="3" 
                                  placeholder="Fármaco, posología y duración..."
                                  <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?>><?= htmlspecialchars($consultaExistente['receta_medica'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="notas_observaciones">Notas Clínicas y Observaciones Libres</label>
                        <textarea id="notas_observaciones" name="notas_observaciones" rows="3" 
                                  placeholder="Indicaciones post-operatorias o notas de seguimiento..."
                                  <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?>><?= htmlspecialchars($consultaExistente['notas_observaciones'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-group" style="max-width: 300px;">
                    <label for="proxima_cita_sugerida">Próxima Cita Sugerida</label>
                    <input type="date" id="proxima_cita_sugerida" name="proxima_cita_sugerida" 
                           value="<?= htmlspecialchars($consultaExistente['proxima_cita_sugerida'] ?? '') ?>"
                           <?= !$esMedicoDeEstaCita ? 'readonly' : '' ?>>
                </div>

                <?php if ($esMedicoDeEstaCita): ?>
                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">
                            <?= $consultaExistente ? 'Actualizar Consulta' : 'Guardar y Finalizar Consulta' ?>
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </main>
</div>
</body>
</html>