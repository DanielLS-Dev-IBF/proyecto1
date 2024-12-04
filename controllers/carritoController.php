<?php
class carritoController {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mostrar el carrito
    public function index() {
        $productos = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : array();

        // Calcular el subtotal y total
        $subtotal = 0;
        foreach ($productos as $producto) {
            $subtotal += $producto['precio_base'] * $producto['cantidad'];
        }
        $gastos_envio = 0.00; // Calcula según tus reglas
        $total = $subtotal + $gastos_envio;

        // Incluir la vista del carrito
        $view = 'views/productos/carrito.php';
        include_once 'views/main.php';
    }

    // Agregar producto al carrito
    public function agregar() {
        $producto_id = $_POST['producto_id'];
        $cantidad = $_POST['cantidad'];

        require_once 'models/ProductoDAO.php';
        $producto = ProductoDAO::getProducto($producto_id);

        if ($producto) {
            // Agregar o actualizar el producto en la sesión
            if (isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
            } else {
                $_SESSION['carrito'][$producto_id] = array(
                    'id_producto' => $producto->getId_producto(),
                    'nombre' => $producto->getNombre(),
                    'precio_base' => $producto->getPrecio_base(),
                    'img' => $producto->getImg(),
                    'cantidad' => $cantidad,
                    'tipo' => $producto->getTipo()
                );
            }
        } else {
            // Manejar el caso en que el producto no existe
            $_SESSION['error'] = 'El producto no existe.';
        }

        // Redirigir al carrito
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Actualizar cantidad de producto
    public function actualizar() {
        $producto_id = $_POST['producto_id'];
        $cantidad = $_POST['cantidad'];

        if (isset($_SESSION['carrito'][$producto_id])) {
            $nueva_cantidad = max(0, (int)$cantidad);
            if ($nueva_cantidad > 0) {
                $_SESSION['carrito'][$producto_id]['cantidad'] = $nueva_cantidad;
            } else {
                // Elimina el producto si la cantidad es 0
                unset($_SESSION['carrito'][$producto_id]);
            }
        }

        // Redirigir al carrito
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Eliminar producto del carrito
    public function eliminar() {
        $producto_id = $_POST['producto_id'];

        if (isset($_SESSION['carrito'][$producto_id])) {
            unset($_SESSION['carrito'][$producto_id]);
        }

        // Redirigir al carrito
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Aplicar código promocional (si lo implementas)
    public function aplicarCodigo() {
        $codigo = $_POST['codigo'];

        // Lógica para validar y aplicar el código
        // Por ejemplo:
        if ($codigo == 'DESCUENTO10') {
            $_SESSION['descuento'] = 10; // 10 euros de descuento
            $_SESSION['codigo_aplicado'] = $codigo;
        } else {
            $_SESSION['error_codigo'] = 'El código promocional no es válido.';
        }

        header('Location: index.php?controller=carrito&action=index');
        exit();
    }
}
?>
