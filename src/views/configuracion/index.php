<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - UnionDental</title>
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
        
        /* Tabs */
        .tabs { display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
        .tab-btn { background: none; border: none; padding: 8px 16px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; border-radius: 6px; text-decoration: none; }
        .tab-btn.active { background: #eff6ff; color: #2563eb; }

        .card { background: white; padding: 28px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
        input { width: 100%; padding: 11px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: #3b82f6; }
        .btn { padding: 11px 18px; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; font-weight: 600; font-size: 14px; display: inline-block; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-danger { background: #fee2e2; color: #dc2626; padding: 6px 12px; font-size: 12px; }
        .btn-success { background: #dcfce7; color: #166534; padding: 6px 12px; font-size: 12px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 14px; }
        th { background: #f9fafb; font-weight: 600; color: #4b5563; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .user-box { margin-top: auto; padding: 14px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 15px; }
        .user-name { font-weight: 700; color: #1f2937; }
        .user-role { color: #6b7280; margin-top: 4px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        .logout a:hover { background: #fee2e2; }
    </style>
</head>
<body>
<div class="app">
    
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <main class="main">
        <div class="header">
            <h1>Configuración del Sistema</h1>
            <p>Ajustes de cuenta, personal administrativo y clínica</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">Error al procesar la solicitud: verifique los datos ingresados.</div>
        <?php elseif ($exito): ?>
            <div class="alert alert-success">Los cambios fueron guardados exitosamente.</div>
        <?php endif; ?>

        <div class="tabs">
            <a href="?url=configuracion/index&tab=perfil" class="tab-btn <?= $tab === 'perfil' ? 'active' : '' ?>">Mi Perfil y Seguridad</a>
            <?php if ($esAdmin): ?>
                <a href="?url=configuracion/index&tab=personal" class="tab-btn <?= $tab === 'personal' ? 'active' : '' ?>">Personal de Recepción</a>
                <a href="?url=configuracion/index&tab=clinica" class="tab-btn <?= $tab === 'clinica' ? 'active' : '' ?>">Datos de la Clínica</a>
            <?php endif; ?>
        </div>

        <!-- Pestaña 1: Mi Perfil -->
        <?php if ($tab === 'perfil'): ?>
            <div class="card">
                <h3 style="margin-top: 0;">Actualizar Contraseña</h3>
                <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">
                    Usuario actual: <strong><?= htmlspecialchars($_SESSION['usuario']['email']) ?></strong> (<?= htmlspecialchars($_SESSION['usuario']['rol']) ?>)
                </p>
                <form method="POST" action="?url=configuracion/cambiarPassword">
                    <div class="form-group">
                        <label for="password_actual">Contraseña Actual:</label>
                        <input type="password" name="password_actual" id="password_actual" required>
                    </div>
                    <div class="form-group">
                        <label for="password_nueva">Nueva Contraseña (mínimo 6 caracteres):</label>
                        <input type="password" name="password_nueva" id="password_nueva" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Confirmar Nueva Contraseña:</label>
                        <input type="password" name="password_confirm" id="password_confirm" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Pestaña 2: Personal de Recepción (Solo Admin) -->
        <?php if ($tab === 'personal' && $esAdmin): ?>
            <div class="card" style="margin-bottom: 24px;">
                <h3 style="margin-top: 0;">Registrar Nueva Ejecutiva / Recepcionista</h3>
                <p style="color: #64748b; font-size: 13px;">Tendrá acceso al registro de pacientes, citas y consulta de horarios sin permisos de administrador.</p>
                <form method="POST" action="?url=configuracion/guardarRecepcionista">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido:</label>
                            <input type="text" name="apellido" id="apellido" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="email">Correo Institucional:</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña Inicial:</label>
                            <input type="password" name="password" id="password" minlength="6" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">+ Crear Cuenta de Recepción</button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-top: 0;">Personal Registrado</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recepcionistas)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">No hay personal de recepción registrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recepcionistas as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?></td>
                                    <td><?= htmlspecialchars($r['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $r['estado'] === 'activo' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= htmlspecialchars(ucfirst($r['estado'])) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <form method="POST" action="?url=configuracion/cambiarEstadoUsuario/<?= (int)$r['id_usuario'] ?>" style="display: inline;">
                                            <input type="hidden" name="estado" value="<?= $r['estado'] === 'activo' ? 'inactivo' : 'activo' ?>">
                                            <button type="submit" class="btn <?= $r['estado'] === 'activo' ? 'btn-danger' : 'btn-success' ?>">
                                                <?= $r['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Pestaña 3: Datos de la Clínica (Solo Admin) -->
        <?php if ($tab === 'clinica' && $esAdmin): ?>
            <div class="card">
                <h3 style="margin-top: 0;">Información de la Clínica</h3>
                <p style="color: #64748b; font-size: 13px;">Estos datos se mostrarán en los recibos, presupuestos y encabezados de expedientes médicos en PDF.</p>
                <form method="POST" action="?url=configuracion/guardarClinica">
                    <div class="form-group">
                        <label for="nombre_clinica">Nombre de la Clínica:</label>
                        <input type="text" name="nombre_clinica" id="nombre_clinica" value="<?= htmlspecialchars($clinica['nombre_clinica']) ?>" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="tel_clinica">Teléfono de Contacto:</label>
                            <input type="text" name="telefono" id="tel_clinica" value="<?= htmlspecialchars($clinica['telefono'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email_clinica">Correo de Contacto:</label>
                            <input type="email" name="email" id="email_clinica" value="<?= htmlspecialchars($clinica['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dir_clinica">Dirección Física:</label>
                        <input type="text" name="direccion" id="dir_clinica" value="<?= htmlspecialchars($clinica['direccion'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Datos de la Clínica</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>