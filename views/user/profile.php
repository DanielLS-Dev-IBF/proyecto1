<?php
include_once "views/TopNav.php";
?>
<link rel="stylesheet" href="css/Profile.css">
<div class="container my-5">
    <div class="row">
        <!-- Menú lateral -->
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="?controller=usuario&action=show" class="list-group-item list-group-item-action active mb-2" aria-current="true">
                    Perfil
                </a>
                <a href="?controller=pedido&action=index" class="list-group-item list-group-item-action">
                    Pedidos
                </a>
            </div>
        </div>

        <!-- Contenido principal: información del usuario y direcciones -->
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
                                <span id="nombre_completo_text"><?php echo htmlspecialchars($usuario->getNombre_completo()); ?></span>
                                <input type="text" name="nombre_completo" id="nombre_completo_input" class="form-control d-none text-start" value="<?php echo htmlspecialchars($usuario->getNombre_completo()); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-3 text-start"><strong>Email:</strong></div>
                            <div class="col-sm-9 text-start">
                                <span id="email_text"><?php echo htmlspecialchars($usuario->getEmail()); ?></span>
                                <input type="email" name="email" id="email_input" class="form-control d-none text-start" value="<?php echo htmlspecialchars($usuario->getEmail()); ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-3 text-start"><strong>Teléfono:</strong></div>
                            <div class="col-sm-9 text-start">
                                <span id="telefono_text"><?php echo htmlspecialchars($usuario->getTelefono()); ?></span>
                                <input type="text" name="telefono" id="telefono_input" class="form-control d-none text-start" value="<?php echo htmlspecialchars($usuario->getTelefono()); ?>">
                            </div>
                        </div>
                        <div id="fecha_registro" class="row mb-2">
                            <div class="col-sm-3 text-start"><strong>Fecha de registro:</strong></div>
                            <div class="col-sm-9 text-start"><?php echo htmlspecialchars($usuario->getFecha_registro()); ?></div>
                        </div>
                        <div id="saveCancelButtons" class="mt-3 d-none">
                            <button type="submit" class="btn-guardar">Guardar</button>
                            <button type="button" id="cancelButton" class="btn-cancelar">Cancelar</button>
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
                                            <div class="col-sm-6 text-start"><?php echo htmlspecialchars($dir->getDireccion()); ?></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-6 text-start"><strong>Código Postal:</strong></div>
                                            <div class="col-sm-6 text-start"><?php echo htmlspecialchars($dir->getCodigo_postal()); ?></div>
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
    });
</script>
