<?php $title = 'Pacientes | UnionDental'; require __DIR__ . '/../layout.php'; ?>

<div>
    <h2>Lista de pacientes</h2>
    <a href="/pacientes/create">Nuevo paciente</a>
</div>

<div>
    <input type="text" id="search" placeholder="Buscar" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
</div>

<div>
    <table>
        <thead>
            <tr>
                <th>Expediente</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Telefono</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="pacientes-table">
            <?php if (empty($pacientes)):  ?>
                <tr><td>
                    No se encuentran pacientes.
                </td></tr>
            <?php else: ?>
                <?php foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['codigo_expediente']) ?></td>
                        <td><?= htmlspecialchars($p['nombres']) ?></td>
                        <td><?= htmlspecialchars($p['apellidos']) ?></td>
                        <td><?= htmlspecialchars($p['telefono']) ?></td>
                        <td><?= htmlspecialchars($p['correo']) ?></td>
                        <td>
                            <a href="/pacientes/<?= $p['id_paciente'] ?>">Ver</a>
                            <a href="/pacientes/<?= $p['id_paciente'] ?>/edit">Editar</a>
                            <form method="POST" action="/pacientes/<?= $p['id_paciente'] ?>/delete" onsubmit="return confirm('¿Desactivar?')">
                                <button>Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>