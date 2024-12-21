<?php
// views/user/profile.php

include_once "views/TopNav.php";
?>
<link rel="stylesheet" href="css/Profile.css">
<div class="container my-5">
    <div class="row">
        <!-- Menú lateral -->
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="?controller=usuario&action=show" class="mb-2 list-group-item list-group-item-action <?php echo (!isset($_GET['action']) || $_GET['action'] === 'show') ? 'active' : ''; ?>" aria-current="true">
                    Perfil
                </a>
                <a href="?controller=usuario&action=pedidos" class="list-group-item list-group-item-action <?php echo (isset($_GET['action']) && $_GET['action'] === 'pedidos') ? 'active' : ''; ?>">
                    Pedidos
                </a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="col-md-9">
            <?php
            // Mostrar mensajes de éxito o error
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }

            // Mostrar errores específicos si están disponibles
            if (isset($errores) && !empty($errores)) {
                echo '<div class="alert alert-danger">' . implode('<br>', array_map('htmlspecialchars', $errores)) . '</div>';
            }

            // Verificar qué sección mostrar: Perfil o Pedidos
            $action = $_GET['action'] ?? 'show';

            // Funciones de Paginación
            function buildUrl($page, $action) {
                $params = array(
                    'controller' => 'usuario',
                    'action' => $action,
                    'page' => $page
                );
                return '?' . http_build_query($params);
            }

            function paginationLinks($currentPage, $totalPages, $action) {
                $links = '';
                $range = 2;

                // Enlace para "Anterior"
                if ($currentPage > 1) {
                    $prevPage = $currentPage - 1;
                    $links .= '<li class="page-item"><a class="page-link" href="' . buildUrl($prevPage, $action) . '">Anterior</a></li>';
                } else {
                    $links .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
                }

                // Rango de páginas alrededor de la página actual
                $start = max(1, $currentPage - $range);
                $end = min($currentPage + $range, $totalPages);

                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $currentPage) {
                        $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                    } else {
                        $links .= '<li class="page-item"><a class="page-link" href="' . buildUrl($i, $action) . '">' . $i . '</a></li>';
                    }
                }

                // Enlace para "Siguiente"
                if ($currentPage < $totalPages) {
                    $nextPage = $currentPage + 1;
                    $links .= '<li class="page-item"><a class="page-link" href="' . buildUrl($nextPage, $action) . '">Siguiente</a></li>';
                } else {
                    $links .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
                }

                return $links;
            }

            if ($action === 'pedidos') {
                // Mostrar la lista de pedidos con paginación
                if (isset($pedidos) && !empty($pedidos)) {
                    foreach ($pedidos as $pedido) {
                        ?>
                        <div class="card mb-4">
                            <div class="card-header fw-semibold">
                                Pedido #<?= htmlspecialchars($pedido['id_pedido']) ?> - <?= htmlspecialchars($pedido['fecha_pedido']) ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-success fw-bold">TOTAL: <?= number_format($pedido['total'], 2) ?>€</h5>
                                <p class="card-text"><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion']) ?></p>
                                <p class="card-text"><strong>Método de Pago:</strong> <?= htmlspecialchars($pedido['metodo_pago']) ?></p>
                                <p class="card-text"><strong>Detalles de Pago:</strong> <?= htmlspecialchars($pedido['detalles_pago']) ?></p>
                                <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#detallePedido<?= $pedido['id_pedido'] ?>" aria-expanded="false" aria-controls="detallePedido<?= $pedido['id_pedido'] ?>">
                                    Ver Detalles
                                    <!-- Íconos de flecha -->
                                    <svg class="icono-flecha abajo" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                    <svg class="icono-flecha arriba d-none" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M1.646 11.354a.5.5 0 0 1 .708 0L8 6.707l5.646 5.647a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1-.708 0l-6 6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                                <div class="collapse mt-3" id="detallePedido<?= $pedido['id_pedido'] ?>">
                                    <?php if (!empty($pedido['detalles'])): ?>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Cantidad</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pedido['detalles'] as $detalle): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($detalle['nombre_producto']) ?></td>
                                                        <td><?= number_format($detalle['precio_unitario'], 2) ?>€</td>
                                                        <td><?= htmlspecialchars($detalle['cantidad']) ?></td>
                                                        <td><?= number_format($detalle['total_producto'], 2) ?>€</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p>No hay detalles para este pedido.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    // Implementar la paginación
                    if (isset($totalPaginas) && $totalPaginas > 1):
                        ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?= paginationLinks($currentPage, $totalPaginas, 'pedidos') ?>
                            </ul>
                        </nav>
                        <?php
                    endif;
                } else {
                    echo '<p>No tienes pedidos realizados.</p>';
                }
            } else {
                // Mostrar la información del usuario
                ?>
                <!-- Card de información del usuario -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 text-start header-title">Información del Usuario</h2>
                        <button id="editButton" class="btn btn-sm edit-icon" aria-label="Editar perfil">
                            <!-- SVG minimalista de un lápiz (icono de edición) -->
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M12 20h9"/>
                              <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body">
                        <form id="userForm" action="?controller=usuario&action=update" method="POST">
                            <div class="row mb-2">
                                <div class="col-sm-3 text-start"><strong>Nombre completo:</strong></div>
                                <div class="col-sm-9 text-start">
                                    <span id="nombre_completo_text"><?= htmlspecialchars($usuario->getNombre_completo()); ?></span>
                                    <input type="text" name="nombre_completo" id="nombre_completo_input" class="form-control d-none text-start" value="<?= htmlspecialchars($usuario->getNombre_completo()); ?>">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-start"><strong>Email:</strong></div>
                                <div class="col-sm-9 text-start">
                                    <span id="email_text"><?= htmlspecialchars($usuario->getEmail()); ?></span>
                                    <input type="email" name="email" id="email_input" class="form-control d-none text-start" value="<?= htmlspecialchars($usuario->getEmail()); ?>">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-start"><strong>Teléfono:</strong></div>
                                <div class="col-sm-9 text-start">
                                    <span id="telefono_text"><?= htmlspecialchars($usuario->getTelefono()); ?></span>
                                    <input type="text" name="telefono" id="telefono_input" class="form-control d-none text-start" value="<?= htmlspecialchars($usuario->getTelefono()); ?>">
                                </div>
                            </div>
                            <div id="fecha_registro" class="row mb-2">
                                <div class="col-sm-3 text-start"><strong>Fecha de registro:</strong></div>
                                <div class="col-sm-9 text-start"><?= htmlspecialchars($usuario->getFecha_registro()); ?></div>
                            </div>
                            <div id="saveCancelButtons" class="mt-3 d-none">
                                <button type="submit" class="btn-guardar btn btn-success me-2">Guardar</button>
                                <button type="button" id="cancelButton" class="btn-cancelar btn btn-secondary">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card de direcciones del usuario -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 text-start header-title">Direcciones</h2>
                        <!-- Botón para añadir una nueva dirección -->
                        <button class="btn btn-sm edit-icon" type="button" data-bs-toggle="collapse" data-bs-target="#addDireccionForm" aria-expanded="false" aria-controls="addDireccionForm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Formulario para añadir una nueva dirección (colapsable) -->
                        <div class="collapse mb-4" id="addDireccionForm">
                            <div class="card card-body">
                                <form action="?controller=usuario&action=addDireccion" method="POST">
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="codigo_postal" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" pattern="\d{5}" title="Debe tener 5 dígitos" required>
                                    </div>
                                    <button type="submit" id="boton_guardar" class="btn btn-success me-2">Guardar</button>
                                    <button type="button" id="boton_cancelar" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#addDireccionForm">Cancelar</button>
                                </form>
                            </div>
                        </div>

                        <?php if (empty($direcciones)): ?>
                            <p class="text-start">No tienes direcciones registradas.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-start">Dirección</th>
                                            <th class="text-start">Código Postal</th>
                                            <th class="text-start"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($direcciones as $dir): ?>
                                            <tr>
                                                <td class="text-start"><?= htmlspecialchars($dir->getDireccion()); ?></td>
                                                <td class="text-start"><?= htmlspecialchars($dir->getCodigoPostal()); ?></td>
                                                <td class="text-start">
                                                    <!-- Formulario para eliminar dirección -->
                                                    <form action="?controller=usuario&action=deleteDireccion" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta dirección?');">
                                                        <input type="hidden" name="id_direccion" value="<?= $dir->getIdDireccion(); ?>">
                                                        <button type="submit" class="btn-close btn-sm" aria-label="Eliminar"></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php include_once "views/Footer.php"; ?>

<!-- Script para manejar el toggle de edición de perfil y las flechas en pedidos -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Manejar la edición del perfil
        const editButton = document.getElementById("editButton");
        const cancelButton = document.getElementById("cancelButton");
        const saveCancelButtons = document.getElementById("saveCancelButtons");
        const textFields = document.querySelectorAll("span[id$='_text']");
        const inputFields = document.querySelectorAll("input[id$='_input']");
        const fechaRegistro = document.getElementById("fecha_registro");

        if (editButton) {
            editButton.addEventListener("click", () => {
                textFields.forEach(text => text.classList.add("d-none"));
                inputFields.forEach(input => {
                    input.classList.remove("d-none");
                    input.style.width = "100%"; // Asegurar que el input ocupe todo el espacio disponible
                });
                saveCancelButtons.classList.remove("d-none");
                editButton.classList.add("d-none");
                if (fechaRegistro) fechaRegistro.classList.add("d-none"); // Ocultar fecha de registro
            });
        }

        if (cancelButton) {
            cancelButton.addEventListener("click", () => {
                textFields.forEach(text => text.classList.remove("d-none"));
                inputFields.forEach(input => input.classList.add("d-none"));
                saveCancelButtons.classList.add("d-none");
                editButton.classList.remove("d-none");
                if (fechaRegistro) fechaRegistro.classList.remove("d-none"); // Mostrar fecha de registro
            });
        }

        // Manejar la alternancia de los íconos de flecha en pedidos
        const buttons = document.querySelectorAll('button[data-bs-toggle="collapse"]');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const iconoAbajo = button.querySelector('.icono-flecha.abajo');
                const iconoArriba = button.querySelector('.icono-flecha.arriba');

                if (iconoAbajo && iconoArriba) {
                    iconoAbajo.classList.toggle('d-none');
                    iconoArriba.classList.toggle('d-none');
                }
            });
        });
    });
</script>
