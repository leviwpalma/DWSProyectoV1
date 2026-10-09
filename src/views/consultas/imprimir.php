<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente - Consulta #<?= (int)$datos['id_consulta'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #1e293b; line-height: 1.5; font-size: 13px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .logo-title { font-size: 22px; font-weight: bold; color: #2563eb; }
        .clinica-info { text-align: right; font-size: 11px; color: #64748b; }
        .box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; margin-bottom: 14px; }
        .box-title { font-weight: bold; font-size: 12px; text-transform: uppercase; margin-bottom: 6px; color: #1e40af; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .firma { margin-top: 50px; text-align: center; width: 250px; float: right; border-top: 1px solid #000; padding-top: 6px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">🖨️ Imprimir o Guardar como PDF</button>
    </div>

    <div class="header">
        <div>
            <div class="logo-title"><?= htmlspecialchars($clinica['nombre_clinica'] ?? 'UnionDental') ?></div>
            <div style="font-weight: bold; margin-top: 4px;">REGISTRO DE ATENCIÓN ODONTOLÓGICA</div>
            <div>Fecha: <?= date('d/m/Y H:i', strtotime($datos['fecha_consulta'])) ?></div>
        </div>
        <div class="clinica-info">
            <?= htmlspecialchars($clinica['direccion'] ?? '') ?><br>
            Tel: <?= htmlspecialchars($clinica['telefono'] ?? '') ?><br>
            <?= htmlspecialchars($clinica['email'] ?? '') ?>
        </div>
    </div>

    <div class="box">
        <div class="box-title">Datos del Paciente</div>
        <div class="grid-4">
            <div><strong>Paciente:</strong><br><?= htmlspecialchars($datos['nombres'] . ' ' . $datos['apellidos']) ?></div>
            <div><strong>Expediente:</strong><br><?= htmlspecialchars($datos['codigo_expediente']) ?></div>
            <div><strong>Edad en Consulta:</strong><br><?= $edad ?></div>
            <div><strong>Documento:</strong><br><?= htmlspecialchars($datos['documento_identidad'] ?? '—') ?></div>
        </div>
    </div>

    <div class="box">
        <div class="box-title">Evolución Clínica</div>
        <p><strong>Motivo de consulta:</strong> <?= nl2br(htmlspecialchars($datos['motivo_consulta'])) ?></p>
        <?php if (!empty($datos['sintomas'])): ?>
            <p><strong>Sintomatología:</strong> <?= nl2br(htmlspecialchars($datos['sintomas'])) ?></p>
        <?php endif; ?>
        <p><strong>Diagnóstico:</strong> <?= nl2br(htmlspecialchars($datos['diagnostico'])) ?></p>
        <p><strong>Tratamiento realizado:</strong> <?= nl2br(htmlspecialchars($datos['tratamiento_realizado'])) ?></p>
    </div>

    <?php if (!empty($datos['receta_medica'])): ?>
        <div class="box">
            <div class="box-title">Receta e Indicaciones Médicas</div>
            <p><?= nl2br(htmlspecialchars($datos['receta_medica'])) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($datos['notas_observaciones'])): ?>
        <div class="box">
            <div class="box-title">Observaciones Clínicas</div>
            <p><?= nl2br(htmlspecialchars($datos['notas_observaciones'])) ?></p>
        </div>
    <?php endif; ?>

    <div class="firma">
        <strong>Dr(a). <?= htmlspecialchars($datos['medico_nombre']) ?></strong><br>
        <?= htmlspecialchars($datos['especialidad']) ?><br>
        <?= !empty($datos['numero_junta']) ? 'JVPO: ' . htmlspecialchars($datos['numero_junta']) : '' ?>
    </div>
</body>
</html>