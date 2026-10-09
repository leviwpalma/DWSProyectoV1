<?php
$rolSesion = $_SESSION['usuario']['rol'] ?? '';
$urlActual = $_GET['url'] ?? 'home/index';
?>
<aside class="sidebar">
    <div class="logo">
        <span class="union">Union</span><span class="dental">Dental</span>
    </div>

    <nav class="menu">
        <a href="?url=home/index" class="<?= str_starts_with($urlActual, 'home') ? 'active' : '' ?>">
            Inicio
        </a>

        <?php if ($rolSesion === 'Administrador'): ?>
            <a href="?url=medicos/index" class="<?= (str_starts_with($urlActual, 'medicos') && !str_contains($urlActual, 'horarios')) ? 'active' : '' ?>">
                Médicos
            </a>
        <?php endif; ?>

        <a href="?url=medicos/horarios" class="<?= str_contains($urlActual, 'horarios') ? 'active' : '' ?>">
            Disponibilidad
        </a>

        <a href="?url=citas/index" class="<?= (str_starts_with($urlActual, 'citas') || str_starts_with($urlActual, 'consultas')) ? 'active' : '' ?>">
            Calendario
        </a>

        <a href="?url=pacientes/index" class="<?= str_starts_with($urlActual, 'pacientes') ? 'active' : '' ?>">
            Pacientes
        </a>

        <?php if ($rolSesion === 'Administrador'): ?>
            <a href="?url=servicio/index" class="<?= str_starts_with($urlActual, 'servicio') ? 'active' : '' ?>">
                Servicios
            </a>
        <?php endif; ?>

        <a href="?url=configuracion/index" class="<?= str_starts_with($urlActual, 'configuracion') ? 'active' : '' ?>">
            Configuración
        </a>
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