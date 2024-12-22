<?php include_once "views/TopNav.php"; ?>

<?php
// Asegurarse de que la sesión está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mostrar mensajes de éxito o error si existen
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje']) . '</div>';
    unset($_SESSION['mensaje']);
}

if (isset($_SESSION['error'])) {
     // Mostrar errores generales
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Mostrar errores específicos del código de descuento
if (isset($_SESSION['error_codigo'])) {
    echo '<div class="alert alert-warning">' . htmlspecialchars($_SESSION['error_codigo']) . '</div>';
    unset($_SESSION['error_codigo']);
}
?>
<link rel="stylesheet" href="css/Carrito.css">

<div class="container my-5 page-cart">
    <h1 class="mb-4 text-start">Resumen del Carrito</h1>
    <div class="row align-items-start">
        <!-- Lista de productos en el carrito -->
        <div class="col-lg-8">
            <div id="cart-items">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="cart-item p-3 mb-3">
                            <div class="product-container d-flex justify-content-between align-items-start">
                                <!-- Imagen y detalles del producto -->
                                <div class="product-details d-flex">
                                    <!-- Imagen del producto -->
                                    <div class="product-image-container">
                                        <img src="<?= htmlspecialchars($producto['img']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="img-fluid rounded product-image">
                                    </div>

                                    <!-- Nombre y Precio del producto -->
                                    <div class="ms-3 product-info">
                                        <h5 class="product-name text-start mb-2"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                        <p class="product-price text-start mb-2">
                                            <?= number_format($producto['precio_base'], 2) ?>€
                                        </p>
                                    </div>
                                </div>

                                <!-- Contenedor para Eliminar y Controles de Cantidad -->
                                <div class="action-button-container d-flex flex-column align-items-end">
                                    <!-- Botón para eliminar producto -->
                                    <form action="index.php?controller=carrito&action=eliminar" method="post" class="mb-4">
                                        <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar producto" aria-label="Eliminar producto">
                                            <img src="/DAW2/Proyecto1/img/Iconos/bin.svg" alt="Eliminar" class="icono-basura">
                                        </button>
                                    </form>

                                    <!-- Controles de cantidad alineados con la imagen -->
                                    <div class="quantity-controls-container d-flex align-items-center justify-content-center">
                                        <!-- Botón para disminuir cantidad -->
                                        <form action="index.php?controller=carrito&action=actualizar" method="post" class="me-2">
                                            <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">
                                            <input type="hidden" name="cantidad" value="<?= max(0, $producto['cantidad'] - 1) ?>">
                                            <button type="submit" class="btn btn-sm btn-cantidad" title="Disminuir cantidad" aria-label="Disminuir cantidad">
                                                <img src="/DAW2/Proyecto1/img/Iconos/minus.svg" alt="Disminuir" class="icono-cantidad">
                                            </button>
                                        </form>

                                        <span class="quantity-number"><?= htmlspecialchars($producto['cantidad']) ?></span>

                                        <!-- Botón para aumentar cantidad -->
                                        <form action="index.php?controller=carrito&action=actualizar" method="post" class="ms-2">
                                            <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">
                                            <input type="hidden" name="cantidad" value="<?= $producto['cantidad'] + 1 ?>">
                                            <button type="submit" class="btn btn-sm btn-cantidad" title="Aumentar cantidad" aria-label="Aumentar cantidad">
                                                <img src="/DAW2/Proyecto1/img/Iconos/plus.svg" alt="Aumentar" class="icono-cantidad">
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center mt-3">El carrito está vacío.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resumen del carrito -->
        <div class="col-lg-4">
            <div class="cart-summary p-4">
                <h5 class="mb-4 d-flex justify-content-between align-items-center">
                    <span>Subtotal:</span>
                    <span id="subtotal"><?= number_format($subtotal, 2) ?>€</span>
                </h5>

                <!-- Mostrar descuento si aplica -->
                <?php if ($descuento > 0): ?>
                    <h5 class="mb-4 d-flex justify-content-between align-items-center">
                        <span>Descuento:</span>
                        <span>-<?= number_format($descuento, 2) ?>€</span>
                    </h5>
                <?php endif; ?>

                <!-- Mostrar el código aplicado y permitir eliminarlo con una "X" -->
                <?php if (isset($_SESSION['codigo_aplicado'])): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        Has aplicado el código promocional: <strong><?= htmlspecialchars($_SESSION['codigo_aplicado']) ?></strong>.
                        <button type="button" class="btn-close" aria-label="Close" onclick="window.location.href='index.php?controller=carrito&action=eliminarDescuento'"></button>
                    </div>
                <?php endif; ?>

                <!-- Título que funciona como toggle para el código promocional -->
                <?php if (!isset($_SESSION['codigo_aplicado'])): ?>
                    <h5 class="my-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#codigo-promocional" role="button" aria-expanded="false" aria-controls="codigo-promocional" style="cursor: pointer;">
                        <span>Código promocional</span>
                        <!-- Contenedor de los iconos de flecha -->
                        <span class="arrow-icon">
                            <img src="/DAW2/Proyecto1/img/Iconos/down-arrow.svg" alt="Flecha hacia abajo" class="icono-flecha abajo">
                            <img src="/DAW2/Proyecto1/img/Iconos/arrow-up.svg" alt="Flecha hacia arriba" class="icono-flecha arriba">
                        </span>
                    </h5>
                <?php endif; ?>

                <!-- Campo de código promocional colapsable -->
                <?php if (!isset($_SESSION['codigo_aplicado'])): ?>
                    <div class="collapse" id="codigo-promocional">
                        <form action="index.php?controller=carrito&action=aplicarCodigo" method="post">
                            <input type="text" name="codigo" class="form-control my-2" placeholder="Introduce el código" required pattern="[A-Za-z0-9]{5,255}" title="El código debe tener entre 5 y 255 caracteres alfanuméricos.">
                            <button type="submit" class="btn-hover w-100 mt-2">Aplicar</button>
                        </form>
                        <?php if (isset($_SESSION['error_codigo'])): ?>
                            <div class="alert alert-danger mt-2">
                                <?= htmlspecialchars($_SESSION['error_codigo']) ?>
                            </div>
                            <?php unset($_SESSION['error_codigo']); ?>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['mensaje'])): ?>
                            <div class="alert alert-success mt-2">
                                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                            </div>
                            <?php unset($_SESSION['mensaje']); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Resto del resumen del carrito -->
                <h5 class="my-4 d-flex justify-content-between align-items-center">
                    <span>Gastos de envío:</span>
                    <span><?= number_format($gastos_envio, 2) ?>€</span>
                </h5>
                <h5 class="mt-4 d-flex justify-content-between align-items-center">
                    <span class="text-success fw-bold">TOTAL:</span>
                    <span class="text-success fw-bold" id="total"><?= number_format($total, 2) ?>€</span>
                </h5>
                <!-- Botón de Pagar Ahora en el resumen del carrito -->
                <button class="btn-hover w-100 mt-5" onclick="window.location.href='index.php?controller=carrito&action=mostrarPago'">Pagar Ahora</button>
            </div>
        </div>
    </div>
</div>
<?php include_once "views/Footer.php"; ?>

<!-- Script para manejar el toggle de las flechas en el formulario de código promocional -->
<script>
    $(document).ready(function() {
        // Toggle arrows on collapse (utilizando Bootstrap)
        const codigoPromocional = $('#codigo-promocional');
        if (codigoPromocional.length) {
            const arrowDown = $('.icono-flecha.abajo');
            const arrowUp = $('.icono-flecha.arriba');

            codigoPromocional.on('show.bs.collapse', function () {
                arrowDown.hide();
                arrowUp.show();
            });

            codigoPromocional.on('hide.bs.collapse', function () {
                arrowDown.show();
                arrowUp.hide();
            });
        }
    });

    // Menú hamburguesa
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdown-menu");
        dropdownMenu.classList.toggle("show");
    }
</script>
