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
                <a href="?controller=usuario&action=show" class="list-group-item list-group-item-action <?php echo (!isset($_GET['action']) || $_GET['action'] === 'show') ? 'active' : ''; ?>" aria-current="true">
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

            if ($action === 'pedidos') {
                // Mostrar la lista de pedidos
                if (isset($pedidos) && !empty($pedidos)) {
                    foreach ($pedidos as $pedido) {
                        ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                Pedido #<?= htmlspecialchars($pedido['id_pedido']) ?> - <?= htmlspecialchars($pedido['fecha_pedido']) ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Total: <?= number_format($pedido['total'], 2) ?>€</h5>
                                <p class="card-text"><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion']) ?></p>
                                <p class="card-text"><strong>Método de Pago:</strong> <?= htmlspecialchars($pedido['metodo_pago']) ?></p>
                                <p class="card-text"><strong>Detalles de Pago:</strong> <?= htmlspecialchars($pedido['detalles_pago']) ?></p>
                                <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#detallePedido<?= $pedido['id_pedido'] ?>" aria-expanded="false" aria-controls="detallePedido<?= $pedido['id_pedido'] ?>">
                                    Ver Detalles
                                    <!-- Usar íconos de Bootstrap para las flechas -->
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
                    <div class="card-header">
                        <h2 class="mb-0 text-start header-title">Direcciones</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($direcciones)): ?>
                            <p class="text-start">No tienes direcciones registradas.</p>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php foreach ($direcciones as $index => $dir): ?>
                                        <div class="mb-4">
                                            <div class="row mb-2">
                                                <div class="col-sm-6 text-start"><strong>Dirección:</strong></div>
                                                <div class="col-sm-6 text-start"><?= htmlspecialchars($dir->getDireccion()); ?></div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-6 text-start"><strong>Código Postal:</strong></div>
                                                <div class="col-sm-6 text-start"><?= htmlspecialchars($dir->getCodigo_postal()); ?></div>
                                            </div>
                                            <?php if ($index < count($direcciones) - 1): ?>
                                                <hr>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const editButton = document.getElementById("editButton");
        const cancelButton = document.getElementById("cancelButton");
        const saveCancelButtons = document.getElementById("saveCancelButtons");
        const textFields = document.querySelectorAll("span[id$='_text']");
        const inputFields = document.querySelectorAll("input[id$='_input']");
        const fechaRegistro = document.getElementById("fecha_registro");

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

        cancelButton.addEventListener("click", () => {
            textFields.forEach(text => text.classList.remove("d-none"));
            inputFields.forEach(input => input.classList.add("d-none"));
            saveCancelButtons.classList.add("d-none");
            editButton.classList.remove("d-none");
            if (fechaRegistro) fechaRegistro.classList.remove("d-none"); // Mostrar fecha de registro
        });

        // Opcional: Manejar la alternancia de los íconos de flecha
        const buttons = document.querySelectorAll('button[data-bs-toggle="collapse"]');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const iconoAbajo = button.querySelector('.icono-flecha.abajo');
                const iconoArriba = button.querySelector('.icono-flecha.arriba');

                iconoAbajo.classList.toggle('d-none');
                iconoArriba.classList.toggle('d-none');
            });
        });
    });
</script>
