<div class="container my-5" style="margin-bottom: 100px;">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card producto-card shadow-sm">
                <div class="row g-0">
                    <!-- Imagen del producto -->
                    <div class="col-md-4 producto-imagen">
                        <img 
                            src="<?= htmlspecialchars($producto->getImg()) ?>" 
                            class="img-fluid rounded-start" 
                            alt="<?= htmlspecialchars($producto->getNombre()) ?>">
                    </div>
                    <!-- Detalles del producto -->
                    <div class="col-md-8">
                        <div class="card-body producto-detalles">
                            <h1 class="card-title"><?= htmlspecialchars($producto->getNombre()) ?></h1>
                            <p class="card-text text-muted">
                                <?= htmlspecialchars($producto->getDescripcion()) ?>
                            </p>
                            <h3 class="text-success fw-bold">
                                <?= number_format($producto->getPrecio_base(), 2) ?>€
                            </h3>
                            <form action="index.php?controller=Carrito&action=agregar" method="POST">
                                <input type="hidden" name="id_producto" value="<?= $producto->getIdProducto() ?>">
                                <button type="submit" class="btn btn-primary">
                                    Agregar al carrito
                                </button>
                            </form>
                            <a href="index.php?controller=Producto&action=index" class="btn btn-secondary">
                                Volver a la lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
