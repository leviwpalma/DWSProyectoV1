<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos - UnionDental</title>
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
        .header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { color: #6b7280; margin-top: 5px; margin-bottom: 0; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; border: none; cursor: pointer; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-danger { background: #fee2e2; color: #dc2626; padding: 6px 12px; font-size: 12px; }
        .btn-success { background: #dcfce7; color: #166534; padding: 6px 12px; font-size: 12px; }
        .panel { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f9fafb; color: #4b5563; font-weight: 600; font-size: 14px; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        tr:hover td { background: #fbfcfe; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .user-box { margin-top: auto; padding: 14px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 15px; }
        .user-name { font-weight: 700; color: #1f2937; }
        .user-role { color: #6b7280; margin-top: 4px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        .logout a:hover { background: #fee2e2; }

        /* Modal minimalista */
        .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.45); z-index: 1000; align-items: center; justify-content: center; }
        .modal-card { background: white; border-radius: 12px; width: 100%; max-width: 420px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .modal-card h3 { margin: 0 0 10px; font-size: 18px; color: #1e293b; }
        .modal-card p { font-size: 13px; color: #64748b; margin-bottom: 16px; }
        .modal-card input { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; }
        .btn-cancel { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
<div class="app">
    
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <main class="main">
        <div class="header">
            <div>
                <h1>Gestión de Médicos</h1>
                <p>Personal médico y credenciales de la clínica</p>
            </div>
            <?php if ($esAdmin): ?>
                <a href="?url=medicos/crear" class="btn btn-primary">+ Nuevo Médico</a>
            <?php endif; ?>
        </div>

        <?php if ($error === 'password_incorrecta'): ?>
            <div class="alert alert-error">Contraseña incorrecta. No se modificó el estado del médico.</div>
        <?php elseif ($exito === 'creado'): ?>
            <div class="alert alert-success">Médico registrado y credenciales creadas con éxito.</div>
        <?php elseif ($exito === 'actualizado'): ?>
            <div class="alert alert-success">Estado del médico actualizado correctamente.</div>
        <?php endif; ?>

        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>N° Junta</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <?php if ($esAdmin): ?>
                            <th style="text-align: right;">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicos)): ?>
                        <tr>
                            <td colspan="<?= $esAdmin ? '8' : '7' ?>" style="text-align: center; color: #6b7280; padding: 30px;">
                                No hay médicos registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($medicos as$m): ?>
                            <tr>
                                <td><?= (int) $m['id_medico'] ?></td>
                                <td><strong><?= htmlspecialchars($m['nombre_completo']) ?></strong></td>
                                <td><?= htmlspecialchars($m['especialidad']) ?></td>
                                <td><?= htmlspecialchars($m['numero_junta'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($m['telefono'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($m['email'] ?? '—') ?></td>
                                <td>
                                    <?php if ($m['estado'] === 'activo'): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($esAdmin): ?>
                                    <td style="text-align: right;">
                                        <?php if ($m['estado'] === 'activo'): ?>
                                            <button 
                                                class="btn btn-danger" 
                                                onclick="abrirModalConfirmacion(<?= (int)$m['id_medico'] ?>, 'inactivo', '<?= htmlspecialchars($m['nombre_completo'], ENT_QUOTES) ?>')">
                                                Desactivar
                                            </button>
                                        <?php else: ?>
                                            <button 
                                                class="btn btn-success" 
                                                onclick="abrirModalConfirmacion(<?= (int)$m['id_medico'] ?>, 'activo', '<?= htmlspecialchars($m['nombre_completo'], ENT_QUOTES) ?>')">
                                                Activar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal de Confirmación de Credencial de Administrador -->
<div class="modal-backdrop" id="modalConfirm">
    <div class="modal-card">
        <h3>Confirmar Autorización</h3>
        <p id="modalDesc">Ingrese su contraseña de Administrador para continuar.</p>
        <form method="POST" id="formCambiarEstado" action="">
            <input type="hidden" name="estado" id="inputEstado" value="">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Contraseña de Administrador:</label>
            <input type="password" name="admin_password" placeholder="Tu contraseña actual" required autocomplete="current-password">
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalConfirmacion(id, nuevoEstado, nombre) {
    const modal = document.getElementById('modalConfirm');
    const form = document.getElementById('formCambiarEstado');
    const inputEstado = document.getElementById('inputEstado');
    const desc = document.getElementById('modalDesc');

    form.action = '?url=medicos/cambiarEstado/' + id;
    inputEstado.value = nuevoEstado;
    desc.innerText = `¿Desea cambiar a ${nuevoEstado} al Dr(a). ${nombre}? Ingrese su contraseña para confirmar:`;

    modal.style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalConfirm').style.display = 'none';
}
</script>
</body>
</html>