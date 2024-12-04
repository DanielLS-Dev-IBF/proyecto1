<?php
// Asegúrate de iniciar la sesión al comienzo del archivo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener los productos del carrito desde la sesión
$productos = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : array();

// Calcular el subtotal y total en el servidor
$subtotal = 0;
foreach ($productos as $producto) {
    $subtotal += $producto['precio_base'] * $producto['cantidad'];
}
$gastos_envio = 0.00; // Puedes ajustar esto según tus reglas

// Aplicar descuento si hay un código promocional
$descuento = isset($_SESSION['descuento']) ? $_SESSION['descuento'] : 0;
$total = $subtotal + $gastos_envio - $descuento;

// Incluir encabezado o navegación si es necesario
?>

<!-- Incluir tus archivos CSS -->
<link rel="stylesheet" href="css/Carrito.css">

<div class="container my-5 page-cart">
    <h1 class="mb-4 text-start">Resumen</h1>
    <div class="row align-items-start">
        <!-- Lista de productos en el carrito -->
        <div class="col-lg-8">
            <div id="cart-items">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="cart-item d-flex align-items-center justify-content-between p-3 mb-3">
                            <img src="<?= htmlspecialchars($producto['img']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="ms-3 flex-grow-1">
                                <h5 class="m-0"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                <p class="m-0"><?= number_format($producto['precio_base'], 2) ?>€</p>
                                <!-- Mostrar propiedades específicas si las tienes -->
                                <?php if ($producto['tipo'] == 'Bebidas' && !empty($producto['volumen'])): ?>
                                    <p>Volumen: <?= htmlspecialchars($producto['volumen']) ?> ml</p>
                                <?php elseif ($producto['tipo'] == 'Postres' && !empty($producto['calorias'])): ?>
                                    <p>Calorías: <?= htmlspecialchars($producto['calorias']) ?> kcal</p>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- Botón para disminuir cantidad -->
                                <form action="index.php?controller=carrito&action=actualizar" method="post" style="margin: 0;">
                                    <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">
                                    <input type="hidden" name="cantidad" value="<?= $producto['cantidad'] - 1 ?>">
                                    <button type="submit" class="btn btn-sm btn-cantidad">
                                        <img src="/DAW2/Proyecto1/img/Iconos/minus.svg" alt="Disminuir" class="icono-cantidad">
                                    </button>
                                </form>

                                <span class="mx-2"><?= htmlspecialchars($producto['cantidad']) ?></span>

                                <!-- Botón para aumentar cantidad -->
                                <form action="index.php?controller=carrito&action=actualizar" method="post" style="margin: 0;">
                                    <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">
                                    <input type="hidden" name="cantidad" value="<?= $producto['cantidad'] + 1 ?>">
                                    <button type="submit" class="btn btn-sm btn-cantidad">
                                        <img src="/DAW2/Proyecto1/img/Iconos/plus.svg" alt="Aumentar" class="icono-cantidad">
                                    </button>
                                </form>
                            </div>
                            <!-- Botón para eliminar producto -->
                            <form action="index.php?controller=carrito&action=eliminar" method="post" style="margin: 0;">
                                <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm ms-2">🗑</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">El carrito está vacío.</p>
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
                <!-- Título que funciona como toggle para el código promocional -->
                <h5 class="my-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#codigo-promocional" role="button" aria-expanded="false" aria-controls="codigo-promocional" style="cursor: pointer;">
                    <span>Código promocional</span>
                    <!-- Contenedor de los iconos de flecha -->
                    <span class="arrow-icon">
                        <img src="/DAW2/Proyecto1/img/Iconos/down-arrow.svg" alt="Flecha hacia abajo" class="icono-flecha abajo">
                        <img src="/DAW2/Proyecto1/img/Iconos/arrow-up.svg" alt="Flecha hacia arriba" class="icono-flecha arriba">
                    </span>
                </h5>
                <!-- Campo de código promocional colapsable -->
                <div class="collapse" id="codigo-promocional">
                    <form action="index.php?controller=carrito&action=aplicarCodigo" method="post">
                        <input type="text" name="codigo" class="form-control my-2" placeholder="Introduce el código">
                        <button type="submit" class="btn-hover">Aplicar</button>
                    </form>
                </div>
                <!-- Resto del resumen del carrito -->
                <h5 class="my-4 d-flex justify-content-between align-items-center">
                    <span>Gastos de envío:</span>
                    <span><?= number_format($gastos_envio, 2) ?>€</span>
                </h5>
                <h5 class="mt-4 d-flex justify-content-between align-items-center">
                    <span class="text-success fw-bold">TOTAL:</span>
                    <span class="text-success fw-bold" id="total"><?= number_format($total, 2) ?>€</span>
                </h5>
                <button class="btn-hover w-100 mt-5">Pagar Ahora</button>
            </div>
        </div>
    </div>
</div>

