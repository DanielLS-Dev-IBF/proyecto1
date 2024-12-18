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
    
        // Calcular el subtotal
        $subtotal = 0;
        foreach ($productos as $producto) {
            $subtotal += $producto['precio_base'] * $producto['cantidad'];
        }
    
        // Aplicar descuento si existe
        $descuento = isset($_SESSION['descuento']) ? $_SESSION['descuento'] : 0;
        $subtotal_con_descuento = $subtotal - $descuento;
    
        // Asegurarse de que el subtotal_con_descuento no sea negativo
        if ($subtotal_con_descuento < 0) {
            $subtotal_con_descuento = 0;
        }
    
        $gastos_envio = 0.00; // Puedes ajustar esto según tus reglas
        $total = $subtotal_con_descuento + $gastos_envio;
    
        // Incluir la vista del carrito
        $view = 'views/productos/carrito.php';
        include_once 'views/main.php';
    }

    // Agregar producto al carrito
    public function agregar() {

        // Validar y sanitizar entradas
        if (isset($_POST['producto_id']) && is_numeric($_POST['producto_id']) && 
            isset($_POST['cantidad']) && is_numeric($_POST['cantidad'])) {
            
            $producto_id = (int) $_POST['producto_id'];
            $cantidad = (int) $_POST['cantidad'];
            
            // Asegurarse de que la cantidad sea al menos 1
            if ($cantidad < 1) {
                $cantidad = 1;
            }

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
                        'tipo' => $producto->getTipo(),
                        'volumen' => $producto->getVolumen(),
                        'calorias' => $producto->getCalorias()
                    );
                }

                // Establecer mensaje de éxito
                $_SESSION['mensaje'] = 'Producto agregado al carrito correctamente.';
            } else {
                // Manejar el caso en que el producto no existe
                $_SESSION['error'] = 'El producto no existe.';
            }
        } else {
            // Manejar entradas inválidas
            $_SESSION['error'] = 'Datos de producto inválidos.';
        }

        // Redirigir al carrito
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Actualizar cantidad de producto
    public function actualizar() {

        if (isset($_POST['producto_id']) && is_numeric($_POST['producto_id']) && 
            isset($_POST['cantidad']) && is_numeric($_POST['cantidad'])) {
            
            $producto_id = (int) $_POST['producto_id'];
            $cantidad = (int) $_POST['cantidad'];

            if (isset($_SESSION['carrito'][$producto_id])) {
                $nueva_cantidad = max(0, $cantidad);
                if ($nueva_cantidad > 0) {
                    $_SESSION['carrito'][$producto_id]['cantidad'] = $nueva_cantidad;
                    $_SESSION['mensaje'] = 'Cantidad actualizada correctamente.';
                } else {
                    // Eliminar el producto si la cantidad es 0
                    unset($_SESSION['carrito'][$producto_id]);
                    $_SESSION['mensaje'] = 'Producto eliminado del carrito.';
                }
            } else {
                $_SESSION['error'] = 'El producto no está en el carrito.';
            }
        } else {
            $_SESSION['error'] = 'Datos de actualización inválidos.';
        }

        // Redirigir al carrito
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Eliminar producto del carrito
    public function eliminar() {

        if (isset($_POST['producto_id']) && is_numeric($_POST['producto_id'])) {
            $producto_id = (int) $_POST['producto_id'];

            if (isset($_SESSION['carrito'][$producto_id])) {
                unset($_SESSION['carrito'][$producto_id]);
                $_SESSION['mensaje'] = 'Producto eliminado del carrito.';
            } else {
                $_SESSION['error'] = 'El producto no está en el carrito.';
            }
        } else {
            $_SESSION['error'] = 'Datos de eliminación inválidos.';
        }

        // Redirigir al carrito
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Aplicar código promocional
    public function aplicarCodigo() {

        if (isset($_POST['codigo'])) {
            $codigo = trim($_POST['codigo']);

            // Validar que el carrito no esté vacío
            if (empty($_SESSION['carrito'])) {
                $_SESSION['error_codigo'] = 'El carrito está vacío.';
                header('Location: index.php?controller=carrito&action=index');
                exit();
            }

            // Obtener el descuento desde la base de datos
            $descuento_info = CodigoDescuentoDAO::obtenerDescuentoPorCodigo($codigo);

            if ($descuento_info) {
                // Calcular el subtotal
                $subtotal = 0;
                foreach ($_SESSION['carrito'] as $producto) {
                    $subtotal += $producto['precio_base'] * $producto['cantidad'];
                }

                // Calcular el descuento basado en porcentaje
                $descuento = ($descuento_info['porcentaje_descuento'] / 100) * $subtotal;

                // Asegurar que el descuento no exceda el subtotal
                if ($descuento > $subtotal) {
                    $descuento = $subtotal;
                }

                // Aplicar el descuento
                $_SESSION['descuento'] = $descuento;
                $_SESSION['codigo_aplicado'] = strtoupper($codigo);
                $_SESSION['mensaje'] = 'Código promocional aplicado correctamente.';
            } else {
                $_SESSION['error_codigo'] = 'El código promocional no es válido o ha expirado.';
            }
        } else {
            $_SESSION['error_codigo'] = 'No se proporcionó un código promocional.';
        }

        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    public function eliminarDescuento() {
        unset($_SESSION['descuento']);
        unset($_SESSION['codigo_aplicado']);
        $_SESSION['mensaje'] = 'Descuento eliminado correctamente.';
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }
    public function pagar() {
        // Aquí iría la lógica para procesar el pago

        // Una vez realizado el pago exitosamente, vaciar el carrito y eliminar descuentos
        unset($_SESSION['carrito']);
        unset($_SESSION['descuento']);
        unset($_SESSION['codigo_aplicado']);
        $_SESSION['mensaje'] = 'Compra realizada exitosamente.';
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }



}
?>
