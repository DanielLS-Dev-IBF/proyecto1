<?php

class productoController {
    public function index(){
        $view = 'views/Home.php';
        include_once 'views/main.php';
    }

    public function carta(){
        // Definir el número de productos por página
        $limit = 6;

        // Obtener los parámetros 'tipo' y 'search' si existen
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'Todos';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Validar el tipo para evitar inyecciones o errores
        $tiposValidos = ['Todos', 'Bowl', 'Bebida', 'Postre'];
        if (!in_array($tipo, $tiposValidos)) {
            // Puedes manejar esto como prefieras, aquí mostramos todos los productos
            $tipo = 'Todos';
        }

        // Obtener el parámetro 'page' si existe, sino asignar 1
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Inicializar variables
        $offset = ($page - 1) * $limit;
        $totalProductos = 0;
        $totalPaginas = 1;
        $productos = [];

        if (!empty($search)) {
            // Búsqueda con término
            $totalProductos = ProductoDAO::countProductosFiltrados($tipo, $search);
            $totalPaginas = ceil($totalProductos / $limit);
            $offset = ($page - 1) * $limit;

            // Asegurar que la página actual no exceda el total de páginas
            if ($page > $totalPaginas && $totalPaginas > 0) {
                $page = $totalPaginas;
                $offset = ($page - 1) * $limit;
            }

            // Obtener los productos filtrados y paginados
            $productos = ProductoDAO::getProductosFiltrados($tipo, $search, $limit, $offset);
        } else {
            // Filtrado por tipo sin búsqueda
            if ($tipo !== 'Todos') {
                $totalProductos = ProductoDAO::countProductosPorTipo($tipo);
                $totalPaginas = ceil($totalProductos / $limit);
                $offset = ($page - 1) * $limit;

                // Asegurar que la página actual no exceda el total de páginas
                if ($page > $totalPaginas && $totalPaginas > 0) {
                    $page = $totalPaginas;
                    $offset = ($page - 1) * $limit;
                }

                // Obtener los productos filtrados y paginados
                $productos = ProductoDAO::getProductosPorTipoPaginados($tipo, $limit, $offset);
            } else {
                // Obtener todos los productos paginados
                $totalProductos = ProductoDAO::countProductos();
                $totalPaginas = ceil($totalProductos / $limit);
                $offset = ($page - 1) * $limit;

                // Asegurar que la página actual no exceda el total de páginas
                if ($page > $totalPaginas && $totalPaginas > 0) {
                    $page = $totalPaginas;
                    $offset = ($page - 1) * $limit;
                }

                $productos = ProductoDAO::getAllPaginados($limit, $offset);
            }
        }

        // Pasar variables a la vista
        $view = 'views/productos/carta.php';
        include_once 'views/main.php';
    }

    public function create(){
        $view = 'views/productos/create.php';
        include_once 'views/main.php';
    }

    public function store(){
        // Manejar el almacenamiento de un nuevo producto
        require_once 'models/ProductoDAO.php';
        require_once 'models/Producto.php';

        // Obtener datos del formulario
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio_base = $_POST['precio_base'];
        $img = $_POST['img']; // Asegúrate de manejar la subida de archivos correctamente
        $tipo = $_POST['tipo'];
        $volumen = isset($_POST['volumen']) ? $_POST['volumen'] : null;
        $calorias = isset($_POST['calorias']) ? $_POST['calorias'] : null;

        // Crear un nuevo objeto Producto
        $producto = new Producto(null, $nombre, $descripcion, $precio_base, $img, $tipo, $volumen, $calorias);

        // Almacenar el producto en la base de datos
        ProductoDAO::store($producto);

        // Redirigir a la carta de productos (a la primera página)
        header('Location: index.php?controller=producto&action=carta');
        exit();
    }

    public function show() {
        // Verifica si se proporcionó un ID válido
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) $_GET['id'];

            // Usar ProductoDAO para obtener el producto
            $producto = ProductoDAO::getProducto($id);

            // Verifica si el producto existe
            if ($producto) {
                $view = 'views/productos/show.php';
                include_once 'views/main.php';
            } else {
                // Producto no encontrado
                http_response_code(404);
                echo "Producto no encontrado.";
            }
        } else {
            // ID inválido o no proporcionado
            http_response_code(400);
            echo "Solicitud no válida. Falta el parámetro 'id'.";
        }
    }

    public function destroy(){
        // Verifica si se proporcionó un ID válido
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $id = (int) $_POST['id'];

            // Eliminar el producto de la base de datos
            ProductoDAO::destroy($id);

            // Redirigir a la carta de productos
            header('Location: index.php?controller=producto&action=carta');
            exit();
        } else {
            // ID inválido o no proporcionado
            http_response_code(400);
            echo "Solicitud no válida. Falta el parámetro 'id'.";
        }
    }
}
?>
