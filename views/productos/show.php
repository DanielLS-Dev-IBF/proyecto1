<div class="container my-5 product-detail-container position-relative page-show">
    <!-- Botón de volver con flecha -->
    <a href="?controller=Producto&action=carta" type="button" class="slick-prev">
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
                        <form action="?controller=Carrito&action=agregar" method="POST" class="agregar-carrito-form">
                            <input type="hidden" name="id_producto" value="<?= $producto->getId_producto() ?>">
                            <button type="submit" class="btn-hover">
                                Agregar al carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
