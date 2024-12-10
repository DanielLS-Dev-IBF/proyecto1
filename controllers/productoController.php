<?php
include_once("models/ProductoDAO.php");
include_once("models/Producto.php");

class productoController{
    public function index(){
        $view = 'views/Home.php';
        include_once 'views/main.php';
    }

    public function carta(){
        // Usar ProductoDAO para obtener todos los productos
        $productos = ProductoDAO::getAll();
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

        // Redirigir a la carta de productos
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
