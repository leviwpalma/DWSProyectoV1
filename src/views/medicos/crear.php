<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Médico - UnionDental</title>
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
        .header p { color: #6b7280; margin-top: 5px; }
        .card { max-width: 760px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 18px rgba(0,0,0,0.05); }
        .errores { background: #fee2e2; color: #991b1b; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .errores ul { margin: 0; padding-left: 20px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; color: #374151; }
        input, select { width: 100%; padding: 11px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: #ffffff; }
        input:focus, select:focus { border-color: #3b82f6; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
        .btn { padding: 11px 18px; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; font-weight: 600; font-size: 14px; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .seguridad-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 16px; border-radius: 8px; margin-top: 10px; }
        .seguridad-box h4 { margin: 0 0 6px; color: #1e40af; font-size: 14px; }
        .seguridad-box p { margin: 0 0 10px; color: #3b82f6; font-size: 13px; }
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
            <a href="?url=medicos/index" class="active">Médicos</a>
            <a href="?url=medicos/horarios">Horarios</a>
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
            <h1>Registrar Nuevo Médico</h1>
            <p>Se creará el perfil clínico y la cuenta de acceso al sistema</p>
        </div>

        <div class="card">
            <?php if (!empty($errores)): ?>
                <div class="errores">
                    <ul>
                        <?php foreach ($errores as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="?url=medicos/guardar">
                <div class="row">
                    <div class="form-group">
                        <label for="nombre">Nombres</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellidos</label>
                        <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="especialidad">Especialidad Odontológica</label>
                        <select id="especialidad" name="especialidad" required>
                            <option value="">Seleccione una especialidad</option>
                            <option value="Odontología General" <?= (($_POST['especialidad'] ?? '') === 'Odontología General') ? 'selected' : '' ?>>Odontología General</option>
                            <option value="Ortodoncia" <?= (($_POST['especialidad'] ?? '') === 'Ortodoncia') ? 'selected' : '' ?>>Ortodoncia</option>
                            <option value="Endodoncia" <?= (($_POST['especialidad'] ?? '') === 'Endodoncia') ? 'selected' : '' ?>>Endodoncia</option>
                            <option value="Periodoncia" <?= (($_POST['especialidad'] ?? '') === 'Periodoncia') ? 'selected' : '' ?>>Periodoncia</option>
                            <option value="Cirugía Maxilofacial" <?= (($_POST['especialidad'] ?? '') === 'Cirugía Maxilofacial') ? 'selected' : '' ?>>Cirugía Maxilofacial</option>
                            <option value="Odontopediatría" <?= (($_POST['especialidad'] ?? '') === 'Odontopediatría') ? 'selected' : '' ?>>Odontopediatría</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="numero_junta">N° de Junta de Vigilancia</label>
                        <input type="text" id="numero_junta" name="numero_junta" placeholder="Ej. JVPO-1234" value="<?= htmlspecialchars($_POST['numero_junta'] ?? '') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="email">Correo Institucional (Acceso)</label>
                        <input type="email" id="email" name="email" placeholder="doctor@clinicadental.local" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" placeholder="0000-0000" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña inicial para el Médico</label>
                    <input type="password" id="password" name="password" minlength="6" placeholder="Mínimo 6 caracteres" required>
                </div>

                <!-- Capa de Seguridad -->
                <div class="seguridad-box">
                    <h4>Autorización Requerida</h4>
                    <p>Por seguridad, confirme su contraseña de Administrador para autorizar el alta:</p>
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="password" name="admin_password" placeholder="Tu contraseña de Administrador" required autocomplete="current-password">
                    </div>
                </div>

                <div class="actions">
                    <a href="?url=medicos/index" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Registrar Médico</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>