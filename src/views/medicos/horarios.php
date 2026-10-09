<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilidad de Consulta - UnionDental</title>
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
        .header { margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { color: #6b7280; margin-top: 5px; margin-bottom: 0; }
        
        /* Barra de Filtros */
        .filtros-card { background: #ffffff; padding: 20px 24px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 24px; display: flex; gap: 20px; align-items: flex-end; }
        .form-filtro { flex: 1; }
        .form-filtro label { display: block; font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 6px; }
        .form-filtro select, .form-filtro input { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: #ffffff; }
        .form-filtro select:focus, .form-filtro input:focus { border-color: #3b82f6; }
        .btn-filtrar { background: #2563eb; color: white; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 600; cursor: pointer; height: 42px; font-size: 14px; }
        .btn-filtrar:hover { background: #1d4ed8; }

        /* Grilla de Horarios */
        .panel-agenda { background: #ffffff; padding: 26px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .leyenda { display: flex; gap: 20px; margin-bottom: 20px; font-size: 13px; font-weight: 600; }
        .leyenda-item { display: flex; align-items: center; gap: 8px; }
        .cuadro-leyenda { width: 16px; height: 16px; border-radius: 4px; }
        .bg-libre { background: #ecfdf5; border: 1px solid #a7f3d0; }
        .bg-ocupado { background: #eff6ff; border: 1px solid #bfdbfe; }

        .timeline-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }
        
        /* Slot individual */
        .time-slot {
            position: relative;
            padding: 14px 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .time-slot.libre {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .time-slot.libre:hover {
            background: #d1fae5;
            transform: translateY(-2px);
        }

        .time-slot.ocupado {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            border-left: 5px solid #2563eb;
            color: #1e40af;
        }
        .time-slot.ocupado:hover {
            background: #dbeafe;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }

        .hora-texto { font-weight: 700; font-size: 14px; }
        .estado-texto { font-size: 12px; font-weight: 600; }

        /* Tooltip estilo Teams */
        .tooltip-teams {
            display: none;
            position: absolute;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            width: 250px;
            background: #1e293b;
            color: #ffffff;
            padding: 14px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            font-size: 12px;
            line-height: 1.5;
            z-index: 100;
            pointer-events: none;
        }
        .tooltip-teams::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
        }
        .time-slot.ocupado:hover .tooltip-teams {
            display: block;
        }
        .tooltip-title { font-weight: 700; color: #93c5fd; margin-bottom: 4px; font-size: 13px; border-bottom: 1px solid #334155; padding-bottom: 4px; }

        .user-box { margin-top: auto; padding: 14px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 15px; }
        .user-name { font-weight: 700; color: #1f2937; }
        .user-role { color: #6b7280; margin-top: 4px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        .logout a:hover { background: #fee2e2; }
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
            <a href="?url=medicos/index">Médicos</a>
            <a href="?url=medicos/horarios" class="active">Disponibilidad</a>
            <a href="?url=citas/index">Calendario</a>
            <a href="?url=pacientes/index">Pacientes</a>
            <a href="?url=servicio/index">Servicios</a>
            <a href="?url=home/index">Configuración</a>
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
            <a href="?url=auth/logout">Cerrar sesión</a>
        </div>
    </aside>

    <main class="main">
        <div class="header">
            <h1>Disponibilidad de Consulta</h1>
            <p>Jornada diaria y estado de bloques de atención por médico</p>
        </div>

        <!-- Filtros de Médico y Fecha -->
        <form method="GET" action="" class="filtros-card">
            <input type="hidden" name="url" value="medicos/horarios">

            <div class="form-filtro">
                <label for="id_medico">Médico / Especialista:</label>
                <select name="id_medico" id="id_medico" required>
                    <?php foreach ($medicos as $m): ?>
                        <option value="<?= (int) $m['id_medico'] ?>" <?= ($idMedicoSeleccionado === (int)$m['id_medico']) ? 'selected' : '' ?>>
                            Dr(a). <?= htmlspecialchars($m['nombre_completo']) ?> - <?= htmlspecialchars($m['especialidad']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-filtro">
                <label for="fecha">Día de consulta:</label>
                <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($fechaSeleccionada) ?>" required>
            </div>

            <button type="submit" class="btn-filtrar">Ver Horarios</button>
        </form>

        <div class="panel-agenda">
            <div class="leyenda">
                <div class="leyenda-item">
                    <span class="cuadro-leyenda bg-libre"></span> Disponible
                </div>
                <div class="leyenda-item">
                    <span class="cuadro-leyenda bg-ocupado"></span> Ocupado (Cita Agendada)
                </div>
            </div>

            <div class="timeline-grid">
                <?php foreach ($grillaHorarios as $slot): ?>
                    <?php if ($slot['ocupado']): ?>
                        <div class="time-slot ocupado">
                            <span class="hora-texto"><?= $slot['hora_inicio_str'] ?> - <?= $slot['hora_fin_str'] ?></span>
                            <span class="estado-texto">Ocupado</span>

                            <!-- Tooltip estilo Teams al hacer hover -->
                            <div class="tooltip-teams">
                                <div class="tooltip-title">Cita #<?= (int)$slot['cita']['id_cita'] ?> (<?= htmlspecialchars($slot['cita']['estado']) ?>)</div>
                                <div><strong>Paciente:</strong> <?= htmlspecialchars($slot['cita']['paciente']) ?></div>
                                <div><strong>Procedimiento:</strong> <?= htmlspecialchars($slot['cita']['servicio']) ?></div>
                                <div><strong>Tel:</strong> <?= htmlspecialchars($slot['cita']['telefono']) ?></div>
                                <?php if (!empty($slot['cita']['motivo_consulta'])): ?>
                                    <div style="margin-top: 4px; color: #cbd5e1; font-style: italic;">
                                        "<?= htmlspecialchars($slot['cita']['motivo_consulta']) ?>"
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="time-slot libre">
                            <span class="hora-texto"><?= $slot['hora_inicio_str'] ?> - <?= $slot['hora_fin_str'] ?></span>
                            <span class="estado-texto">Disponible</span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>