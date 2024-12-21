<?php
// views/productos/confirmacion.php
?>
<link rel="stylesheet" href="css/Carrito.css"> <!-- Asegúrate de que este CSS esté correctamente enlazado -->

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="pago-summary p-4">
                <div class="card-body">
                    <!-- Título de Confirmación: Cambiado a h3 y color negro -->
                    <h2 class="card-title text-dark pb-4 text-success">¡Pedido Realizado con Éxito!</h2>
                    
                    <!-- Mensajes de Agradecimiento -->
                    <p class="card-text">Gracias por tu compra, <?= htmlspecialchars($_SESSION['nombre_completo'] ?? 'cliente'); ?>.</p>
                    <p class="card-text">Tu pedido ha sido procesado correctamente y estamos preparando todo para enviártelo.</p>
                    
                    <hr>
                    
                    <!-- Detalles del Pedido: Alineados a la Izquierda -->
                    <h4 class="mb-3 px-3 text-start">Detalles del Pedido:</h4>
                    <ul class="list-group list-group-flush text-start">
                        <li class="list-group-item d-flex text-success fw-bold justify-content-between">
                            <span>TOTAL:</span>
                            <span><?= number_format($_SESSION['total'], 2) ?>€</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Método de Pago:</span>
                            <span><?= htmlspecialchars($_SESSION['metodo_pago'] ?? ''); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Fecha del Pedido:</span>
                            <span><?= htmlspecialchars($_SESSION['fecha_pedido'] ?? date('Y-m-d H:i:s')); ?></span>
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <!-- Productos del Pedido: Alineados a la Izquierda -->
                    <h4 class="mb-3 px-3 text-start">Productos:</h4>
                    <ul class="list-group list-group-flush text-start">
                        <?php
                        // Suponiendo que has almacenado los detalles del pedido en la sesión
                        if (isset($_SESSION['detalles_pedido']) && is_array($_SESSION['detalles_pedido'])):
                            foreach ($_SESSION['detalles_pedido'] as $detalle):
                        ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= htmlspecialchars($detalle['nombre_producto']); ?></span>
                                <span><?= $detalle['cantidad']; ?> x <?= number_format($detalle['precio_unitario'], 2) ?>€ = <?= number_format($detalle['total_producto'], 2) ?>€</span>
                            </li>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <li class="list-group-item">No hay detalles de productos disponibles.</li>
                        <?php
                        endif;
                        ?>
                    </ul>
                    
                    <hr>
                    
                    <!-- Botones de Navegación -->
                    <a href="index.php?controller=carrito&action=index" id="boton_carrito" class="mt-3">Volver al Carrito</a>
                    <a href="index.php?controller=usuario&action=historialPedidos" id="boton_cuenta" class="mt-3">Ver Historial de Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Al final de confirmacion.php

// Limpiar los datos de la sesión relacionados con el pedido
unset($_SESSION['detalles_pedido']);
unset($_SESSION['nombre_completo']);
unset($_SESSION['metodo_pago']);
unset($_SESSION['total']);
unset($_SESSION['fecha_pedido']);
?>
