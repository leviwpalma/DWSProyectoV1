</main>
</div>

<!-- Scripts esenciales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($cargarJsPacientes) && $cargarJsPacientes === true): ?>
    <script src="js/pacientes.js?v=2"></script>
<?php endif; ?>
</body>
</html>