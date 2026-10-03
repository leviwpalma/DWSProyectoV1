<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio - UnionDental</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: white;
            border-right: 1px solid #e5e7eb;
            padding: 30px 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .logo span:first-child {
            color: #3b82f6;
        }

        .menu a {
            display: block;
            text-decoration: none;
            color: #4b5563;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .menu a.active {
            background: #dbeafe;
            color: #2563eb;
            font-weight: 600;
        }

        .main {
            flex: 1;
            padding: 36px;
        }

        .header {
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            color: #6b7280;
        }

        .card {
            max-width: 760px;
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        }

        .errores {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 11px 18px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }
    </style>
</head>

<body>

<div class="app">

    <aside class="sidebar">
        <div class="logo">
            <span>Union</span>Dental
        </div>

        <nav class="menu">
            <a href="?url=home/index">Inicio</a>
            <a href="?url=servicio/index" class="active">Servicios</a>
            <a href="#">Calendario</a>
            <a href="#">Pacientes</a>
            <a href="#">Configuración</a>
        </nav>
    </aside>

    <main class="main">

        <div class="header">
            <h1>Editar Servicio</h1>
            <p>Actualiza los parámetros del procedimiento dental.</p>
        </div>

        <div class="card">

            <?php if (!empty($errores)): ?>
                <div class="errores">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="?url=servicio/actualizar/<?= (int) $servicio['id_servicio'] ?>"
            >

                <div class="form-group">
                    <label for="nombre">Nombre del servicio</label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?= htmlspecialchars($servicio['nombre']) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="id_categoria">Categoría</label>

                    <select id="id_categoria" name="id_categoria" required>

                        <?php foreach ($categorias as $categoria): ?>

                            <option
                                value="<?= (int) $categoria['id_categoria'] ?>"
                                <?= (
                                    (int) $servicio['id_categoria']
                                    ===
                                    (int) $categoria['id_categoria']
                                ) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        rows="4"
                    ><?= htmlspecialchars($servicio['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="row">

                    <div class="form-group">
                        <label for="duracion_minutos">Duración (minutos)</label>

                        <input
                            type="number"
                            id="duracion_minutos"
                            name="duracion_minutos"
                            min="1"
                            value="<?= (int) $servicio['duracion_minutos'] ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="precio_ref">Precio de referencia</label>

                        <input
                            type="number"
                            id="precio_ref"
                            name="precio_ref"
                            min="0"
                            step="0.01"
                            value="<?= htmlspecialchars($servicio['precio_ref']) ?>"
                            required
                        >
                    </div>

                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>

                    <select id="estado" name="estado" required>

                        <option
                            value="activo"
                            <?= $servicio['estado'] === 'activo' ? 'selected' : '' ?>
                        >
                            Activo
                        </option>

                        <option
                            value="inactivo"
                            <?= $servicio['estado'] === 'inactivo' ? 'selected' : '' ?>
                        >
                            Inactivo
                        </option>

                    </select>
                </div>

                <div class="actions">

                    <a
                        href="?url=servicio/index"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>