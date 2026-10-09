<?php

$title = 'Pacientes | UnionDental';

require __DIR__ . '/../layout.php';

?>

<div class="page-wrapper">

    <div class="breadcrumb-custom">
        <span>Pacientes</span>
        &nbsp;›&nbsp;
        Buscar pacientes
    </div>

    <h2 class="mb-4" style="font-weight: 600;">
        Lista de pacientes
    </h2>

    <div class="card-soft p-4 mb-4">

        <div class="d-flex justify-content-between align-items-start mb-3">

            <label class="label-soft-lg mb-0">
                Busca pacientes por nombre, teléfono o documento
            </label>

            <a
                href="/?url=pacientes/create"
                class="btn btn-primary btn-sm"
            >
                Nuevo paciente +
            </a>

        </div>

        <div class="position-relative">

            <i
                class="bi bi-search position-absolute"
                style="
                    left: 16px;
                    top: 50%;
                    transform: translateY(-50%);
                    color: #94a3b8;
                "
            ></i>

            <input
                type="text"
                id="search"
                class="form-control input-soft ps-5"
                placeholder="Ingresa nombre, teléfono o N° de expediente"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            >

        </div>

    </div>


    <div class="card-soft">

        <div class="table-responsive">

            <table class="table table-clean mb-0">

                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>N° Expediente</th>
                        <th>Documento</th>
                        <th>Fecha de nacimiento</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody id="pacientes-table">

                    <?php if (empty($pacientes)): ?>

                        <tr>

                            <td
                                colspan="8"
                                class="text-center text-muted py-5"
                            >

                                <i
                                    class="bi bi-inbox d-block mb-2"
                                    style="font-size: 1.5rem;"
                                ></i>

                                No se encuentran pacientes registrados.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($pacientes as $p): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($p['nombres']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['apellidos']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['codigo_expediente']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $p['documento_identidad'] ?? '—'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $p['fecha_nacimiento'] ?? '—'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['telefono']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $p['email'] ?? '—'
                                    ) ?>
                                </td>

                                <td class="text-end">

                                    <!-- Ver expediente -->
                                    <a
                                        href="#"
                                        class="text-info me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#expediente-<?= (int) $p['id_paciente'] ?>"
                                        title="Ver expediente"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Editar -->
                                    <a
                                        href="/?url=pacientes/edit/<?= (int) $p['id_paciente'] ?>"
                                        class="text-warning me-2"
                                        title="Editar"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- Eliminar -->
                                    <a
                                        href="/?url=pacientes/eliminar/<?= (int) $p['id_paciente'] ?>"
                                        class="text-danger"
                                        title="Eliminar"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </a>

                                </td>

                            </tr>


                            <!-- Modal expediente -->

                            <div
                                class="modal fade expediente-modal"
                                id="expediente-<?= (int) $p['id_paciente'] ?>"
                                tabindex="-1"
                            >

                                <div class="modal-dialog modal-dialog-centered modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-body">

                                            <div class="expediente-card">

                                                <div class="expediente-side">

                                                    <div>

                                                        <div class="exp-label">

                                                            Expediente No.<br>

                                                            <?= htmlspecialchars(
                                                                $p['codigo_expediente']
                                                            ) ?>

                                                        </div>

                                                        <div class="exp-avatar">

                                                            <i class="bi bi-person"></i>

                                                        </div>

                                                    </div>

                                                    <div class="exp-dots">
                                                        • • •
                                                    </div>

                                                </div>


                                                <div class="expediente-main">

                                                    <h4>
                                                        <?= htmlspecialchars(
                                                            $p['nombres']
                                                            . ' '
                                                            . $p['apellidos']
                                                        ) ?>
                                                    </h4>

                                                    <div class="expediente-grid">

                                                        <div class="expediente-item">

                                                            <i class="bi bi-gender-ambiguous"></i>

                                                            <span>
                                                                <?php

                                                                $generos = [
                                                                    'M' => 'Masculino',
                                                                    'F' => 'Femenino',
                                                                    'Otro' => 'Otro'
                                                                ];

                                                                echo htmlspecialchars(
                                                                    $generos[
                                                                        $p['genero'] ?? ''
                                                                    ] ?? 'n/d'
                                                                );

                                                                ?>
                                                            </span>

                                                        </div>


                                                        <div class="expediente-item">

                                                            <i class="bi bi-geo-alt"></i>

                                                            <span>
                                                                <?= htmlspecialchars(
                                                                    $p['direccion']
                                                                    ?? 'Sin dirección'
                                                                ) ?>
                                                            </span>

                                                        </div>


                                                        <div class="expediente-item">

                                                            <i class="bi bi-calendar"></i>

                                                            <span>

                                                                <?php

                                                                if (
                                                                    !empty(
                                                                        $p['fecha_nacimiento']
                                                                    )
                                                                ) {

                                                                    $meses = [
                                                                        'ene',
                                                                        'feb',
                                                                        'mar',
                                                                        'abr',
                                                                        'may',
                                                                        'jun',
                                                                        'jul',
                                                                        'ago',
                                                                        'sep',
                                                                        'oct',
                                                                        'nov',
                                                                        'dic'
                                                                    ];

                                                                    [
                                                                        $y,
                                                                        $m,
                                                                        $d
                                                                    ] = explode(
                                                                        '-',
                                                                        $p['fecha_nacimiento']
                                                                    );

                                                                    echo htmlspecialchars(
                                                                        $d
                                                                        . '-'
                                                                        . $meses[
                                                                            (int) $m - 1
                                                                        ]
                                                                        . '-'
                                                                        . $y
                                                                    );

                                                                } else {

                                                                    echo 'n/d';

                                                                }

                                                                ?>

                                                            </span>

                                                        </div>


                                                        <div class="expediente-item">

                                                            <i class="bi bi-telephone"></i>

                                                            <span>
                                                                <?= htmlspecialchars(
                                                                    $p['telefono']
                                                                ) ?>
                                                            </span>

                                                        </div>


                                                        <div class="expediente-item">

                                                            <i class="bi bi-card-text"></i>

                                                            <span>
                                                                <?= htmlspecialchars(
                                                                    $p['documento_identidad']
                                                                    ?? 'n/d'
                                                                ) ?>
                                                            </span>

                                                        </div>


                                                        <div class="expediente-item">

                                                            <i class="bi bi-envelope"></i>

                                                            <span>
                                                                <?= htmlspecialchars(
                                                                    $p['email']
                                                                    ?? 'n/d'
                                                                ) ?>
                                                            </span>

                                                        </div>


                                                        <div
                                                            class="expediente-item"
                                                            style="grid-column: 1 / -1;"
                                                        >

                                                            <i class="bi bi-clipboard-pulse"></i>

                                                            <span>
                                                                <?= htmlspecialchars(
                                                                    $p['antecedentes_medicos']
                                                                    ?? 'Sin antecedentes registrados'
                                                                ) ?>
                                                            </span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>