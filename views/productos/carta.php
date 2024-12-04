<h1 class="text-center my-5 display-4 display-md-3 display-sm-5">Lista de Productos</h1>
<div class="page-product-list">
    <div class="container">
        <div class="row">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <img class="card-img-top mt-3" src="<?= htmlspecialchars($producto->getImg()) ?>" alt="<?= htmlspecialchars($producto->getNombre()) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($producto->getNombre()) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($producto->getDescripcion()) ?></p>
                                <p class="card-text card-price fw-bold"><?= number_format($producto->getPrecio_base(), 2) ?>€</p>
                            </div>
                            <div class="card-footer text-center">
                                <!-- Botón que redirige a la página de detalles -->
                                <a  id="btn-carta"
                                    href="?controller=Producto&action=show&id=<?= $producto->getId_producto() ?>" 
                                    class="btn-hover w-100 my-3">
                                    Ver Producto
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No hay productos disponibles.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
