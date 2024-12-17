<!-- views/productos/carta.php -->

<?php include_once "views/TopNav.php"; ?>

<link rel="stylesheet" href="css/Carta.css">

<h1 class="text-center my-5 display-4 display-md-3 display-sm-5">Lista de Productos</h1>

<?php 
$tipoActual = isset($_GET['tipo']) ? $_GET['tipo'] : 'Todos'; 
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPaginas = isset($totalPaginas) ? (int)$totalPaginas : 1;

function buildUrl($page, $tipo, $search){
    $url = '?controller=producto&action=carta&page=' . $page;
    if ($tipo && $tipo !== 'Todos') $url .= '&tipo=' . urlencode($tipo);
    if ($search && !empty($search)) $url .= '&search=' . urlencode($search);
    return $url;
}

function paginationLinks($currentPage, $totalPages, $tipo, $search){
    $links = '';
    $range = 2;
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $links .= '<li class="page-item"><a class="page-link" href="'. buildUrl($prevPage, $tipo, $search) .'">Anterior</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    }
    for ($i = max(1, $currentPage - $range); $i <= min($currentPage + $range, $totalPages); $i++) {
        if ($i == $currentPage) {
            $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="'. buildUrl($i, $tipo, $search) .'">' . $i . '</a></li>';
        }
    }
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $links .= '<li class="page-item"><a class="page-link" href="'. buildUrl($nextPage, $tipo, $search) .'">Siguiente</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    }
    return $links;
}
?>

<div class="container mb-4">
    <div class="row justify-content-between align-items-center">
        <div class="col-12 col-md-4 mb-3 carta-filters-container">
            <div class="btn-group" role="group" aria-label="Filtrar Productos">
                <a href="?controller=producto&action=carta&tipo=Todos" class="btn btn-filter <?= ($tipoActual === 'Todos') ? 'btn-primary' : 'btn-secondary' ?>">Todos</a>
                <a href="?controller=producto&action=carta&tipo=Bowl" class="btn btn-filter <?= ($tipoActual === 'Bowl') ? 'btn-primary' : 'btn-secondary' ?>">Bowls</a>
                <a href="?controller=producto&action=carta&tipo=Bebida" class="btn btn-filter <?= ($tipoActual === 'Bebida') ? 'btn-primary' : 'btn-secondary' ?>">Bebidas</a>
                <a href="?controller=producto&action=carta&tipo=Postre" class="btn btn-filter <?= ($tipoActual === 'Postre') ? 'btn-primary' : 'btn-secondary' ?>">Postres</a>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3 carta-search-container d-flex justify-content-end">
            <form method="GET" action="index.php" id="search-form" style="position: relative; width:100%; max-width:250px;">
                <input type="hidden" name="controller" value="producto">
                <input type="hidden" name="action" value="carta">
                <input type="hidden" name="tipo" value="Todos">
                <input type="text" name="search" class="form-control search-input" placeholder="Buscar productos..." value="<?= htmlspecialchars($searchTerm) ?>" aria-label="Buscar productos">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" aria-label="Buscar" role="button" tabindex="0" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 
                     6.5 0 1 0-1.397 1.398h-.001l3.85 
                     3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.442 
                     1.398a5 5 0 1 1 0-10 5 5 
                     0 0 1 0 10z"/>
                </svg>
            </form>
        </div>
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
                                <a href="?controller=producto&action=show&id=<?= $producto->getId_producto() ?>" class="btn-hover w-100 my-3">
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

        <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Page navigation" class="carta-pagination">
                <ul class="pagination justify-content-center">
                    <?= paginationLinks($currentPage, $totalPaginas, $tipoActual, $searchTerm) ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include_once "views/Footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchIcon = document.querySelector('.search-icon');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.querySelector('.search-input');

    function submitSearch() {
        searchForm.submit();
    }

    if(searchIcon){
        searchIcon.addEventListener('click', submitSearch);
    }

    if(searchInput){
        searchInput.addEventListener('keydown', function(event){
            if(event.key === 'Enter'){
                event.preventDefault();
                submitSearch();
            }
        });
    }

    if(searchIcon){
        searchIcon.addEventListener('keydown', function(event){
            if(event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                submitSearch();
            }
        });
    }
});
</script>
