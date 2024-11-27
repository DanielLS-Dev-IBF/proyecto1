<?php

include_once("models/BowlDAO.php");
include_once("models/Producto.php");

class productoController{
    public function index(){
        
        $view = 'views/Home.php';
        include_once 'views/main.php';
    }
    public function carta(){
        
        // Usar BowlDAO para obtener todos los productos
        $productos = BowlDAO::getAll();
        $view = 'views/productos/carta.php';
        include_once 'views/main.php';
    }

    public function create(){
        include_once 'views/productos/create.php';
    }

    public function store(){
        //include_once 'views/productos/store.php';
       $nombre = $_POST['nombre'];
       $talla = $_POST['talla'];
       $precio = $_POST['precio'];

       $producto = new Bowl();
       $producto->setNombre($nombre);
       $producto->setTalla($talla);
       $producto->setprecio($precio);

       CamisetaDAO::store($producto);
       header('Location:?controller=producto');


    }

    public function destroy(){
        CamisetaDAO::destroy($_GET['id']);
        header('Location:?controller=producto');
    }
    public function show() {
        // Verifica si se proporcionó un ID válido
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) $_GET['id'];
    
            // Usar BowlDAO para obtener el producto
            $producto = BowlDAO::getProducto($id);
    
            // Verifica si el producto existe
            if ($producto) {
                // Cambiar la vista a la nueva ruta
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
    
}