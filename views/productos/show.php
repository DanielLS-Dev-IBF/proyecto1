<!-- views/productos/show.php -->
<?php include_once "views/TopNav.php"; ?>
<div class="container my-5 product-detail-container position-relative page-show">
    <!-- Botón de volver con flecha -->
    <a href="?controller=producto&action=carta" type="button" class="slick-prev">
        <img src="/DAW2/Proyecto1/img/Iconos/prev-arrow.svg" alt="Flecha izquierda">
    </a>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card producto-card shadow-sm position-relative">
                <div class="row g-0">
                    <!-- Imagen del producto -->
                    <div class="col-md-5 producto-imagen">
                        <img 
                            src="<?= htmlspecialchars($producto->getImg()) ?>" 
                            class="img-fluid rounded-start" 
                            alt="<?= htmlspecialchars($producto->getNombre()) ?>">
                    </div>
                    <!-- Detalles del producto -->
                    <div class="col-md-7 card-body producto-detalles">
                        <h1 class="product-title"><?= htmlspecialchars($producto->getNombre()) ?></h1>
                        <p class="product-description text-muted"><?= htmlspecialchars($producto->getDescripcion()) ?></p>
                        <h3 class="product-price fw-bold"><?= number_format($producto->getPrecio_base(), 2) ?>€</h3>
                        
                        <!-- Mostrar propiedades específicas según el tipo -->
                        <?php if ($producto->getTipo() == 'Bebidas' && !empty($producto->getVolumen())): ?>
                            <p>Volumen: <?= htmlspecialchars($producto->getVolumen()) ?> ml</p>
                        <?php elseif ($producto->getTipo() == 'Postres' && !empty($producto->getCalorias())): ?>
                            <p>Calorías: <?= htmlspecialchars($producto->getCalorias()) ?> kcal</p>
                        <?php endif; ?>
                        
                        <!-- Formulario para agregar al carrito -->
                        <form action="?controller=carrito&action=agregar" method="POST" class="agregar-carrito-form mt-4">
                            <input type="hidden" name="producto_id" value="<?= $producto->getId_producto() ?>">
                            <input type="hidden" name="cantidad" value="1"> <!-- Cantidad fija en 1 -->
                            <button type="submit" class="btn-hover">
                                Agregar al carrito
                            </button>
                        </form>

                        <!-- Mensajes de error o éxito -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger mt-3">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['mensaje'])): ?>
                            <div class="alert alert-success mt-3">
                                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                            </div>
                            <?php unset($_SESSION['mensaje']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once "views/Footer.php"; ?>