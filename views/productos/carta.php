<!-- views/productos/carta.php -->

<?php include_once "views/TopNav.php"; ?>


<!-- Enlace al CSS específico de carta.php -->
<link rel="stylesheet" href="css/Carta.css">

<h1 class="text-center my-5 display-4 display-md-3 display-sm-5">Lista de Productos</h1>

<?php 
// Obtener el tipo actual para resaltar el botón activo
$tipoActual = isset($_GET['tipo']) ? $_GET['tipo'] : 'Todos';

// Variables de paginación
$productosPorPagina = 6; // Número máximo de productos por página
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPaginas = isset($totalPaginas) ? $totalPaginas : 1;

// Función para generar enlaces de paginación
function paginationLinks($currentPage, $totalPages, $tipo){
    $links = '';

    // Definir el rango de páginas a mostrar
    $range = 2; // Mostrar 2 páginas antes y después de la actual

    // Página anterior
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $links .= '<li class="page-item"><a class="page-link" href="'. buildUrl($prevPage, $tipo) .'">Anterior</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    }

    // Páginas individuales
    for ($i = max(1, $currentPage - $range); $i <= min($currentPage + $range, $totalPages); $i++) {
        if ($i == $currentPage) {
            $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="'. buildUrl($i, $tipo) .'">' . $i . '</a></li>';
        }
    }

    // Página siguiente
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $links .= '<li class="page-item"><a class="page-link" href="'. buildUrl($nextPage, $tipo) .'">Siguiente</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    }

    return $links;
}

// Función para construir la URL con los parámetros adecuados
function buildUrl($page, $tipo){
    $url = '?controller=producto&action=carta&page=' . $page;
    if ($tipo && $tipo !== 'Todos') {
        $url .= '&tipo=' . urlencode($tipo);
    }
    return $url;
}
?>

<!-- Botones de Filtrado -->
<div class="container mb-4 carta-filters-container">
    <div class="btn-group" role="group" aria-label="Filtrar Productos">
        <a href="?controller=producto&action=carta" class="btn btn-filter <?= ($tipoActual === 'Todos' || !$tipoActual) ? 'btn-primary' : 'btn-secondary' ?>">Todos</a>
        <a href="?controller=producto&action=carta&tipo=Bowl" class="btn btn-filter <?= ($tipoActual === 'Bowl') ? 'btn-primary' : 'btn-secondary' ?>">Bowls</a>
        <a href="?controller=producto&action=carta&tipo=Bebida" class="btn btn-filter <?= ($tipoActual === 'Bebida') ? 'btn-primary' : 'btn-secondary' ?>">Bebidas</a>
        <a href="?controller=producto&action=carta&tipo=Postre" class="btn btn-filter <?= ($tipoActual === 'Postre') ? 'btn-primary' : 'btn-secondary' ?>">Postres</a>
    </div>
</div>

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
                                <a  
                                    href="?controller=producto&action=show&id=<?= $producto->getId_producto() ?>" 
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

        <!-- Enlaces de Paginación -->
        <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Page navigation" class="carta-pagination">
                <ul class="pagination justify-content-center">
                    <?= paginationLinks($currentPage, $totalPaginas, $tipoActual) ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include_once "views/Footer.php"; ?>
