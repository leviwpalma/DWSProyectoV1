<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - UnionDental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
        .btn-primary { background: #2563eb; color: white; text-decoration: none; padding: 11px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; border: none; }
        .btn-primary:hover { background: #1d4ed8; }
        .card-busqueda { background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 24px; }
        .card-busqueda label { font-size: 15px; font-weight: 600; color: #374151; display: block; margin-bottom: 12px; }
        .input-busqueda { width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: #f9fafb; }
        .input-busqueda:focus { border-color: #3b82f6; background: #ffffff; }
        .panel-tabla { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f9fafb; color: #4b5563; font-weight: 600; font-size: 14px; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        tr:hover td { background: #fbfcfe; }
        .acciones a { text-decoration: none; margin-left: 8px; font-size: 16px; }
        .acciones .ver { color: #0284c7; }
        .acciones .editar { color: #d97706; }
        .acciones .eliminar { color: #dc2626; }
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
            <div>
                <h1>Lista de Pacientes</h1>
                <p>Gestión de expedientes y datos personales</p>
            </div>
            <a href="?url=pacientes/create" class="btn-primary">+ Nuevo paciente</a>
        </div>

        <div class="card-busqueda">
            <label for="search">Busca pacientes por nombre, teléfono o documento</label>
            <input
                type="text"
                id="search"
                class="input-busqueda"
                placeholder="Ingresa nombre, teléfono o N° de expediente..."
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            >
        </div>

        <div class="panel-tabla">
            <table>
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>N° Expediente</th>
                        <th>Documento</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="pacientes-table">
                    <?php if (empty($pacientes)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #6b7280; padding: 30px;">
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
                                <td>
                                    <?php
                                    if (!empty($p['fecha_nacimiento'])) {
                                        echo htmlspecialchars(date('d/m/Y', strtotime($p['fecha_nacimiento'])));
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($p['telefono']) ?></td>
                                <td><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                                <td class="acciones" style="text-align: right;">
                                    <a href="?url=pacientes/show/<?= (int) $p['id_paciente'] ?>" class="ver" title="Ver Expediente"><i class="bi bi-eye"></i></a>
                                    <a href="?url=pacientes/edit/<?= (int) $p['id_paciente'] ?>" class="editar" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <a href="?url=pacientes/eliminar/<?= (int) $p['id_paciente'] ?>" class="eliminar" title="Eliminar"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<script src="js/pacientes.js?v=3"></script>
</body>
</html>