<?php
// controllers/carritoController.php

include_once("models/CodigoDescuentoDAO.php");

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

                // Verificar el monto mínimo de compra
                if ($subtotal < $descuento_info['minimo_compra']) {
                    $_SESSION['error_codigo'] = 'El subtotal debe ser al menos ' . number_format($descuento_info['minimo_compra'], 2) . '€ para aplicar este descuento.';
                    header('Location: index.php?controller=carrito&action=index');
                    exit();
                }

                // Verificar el uso máximo total
                if ($descuento_info['usado'] >= $descuento_info['uso_maximo']) {
                    $_SESSION['error_codigo'] = 'Este código de descuento ha alcanzado su límite de usos.';
                    header('Location: index.php?controller=carrito&action=index');
                    exit();
                }

                // Verificar el uso máximo por usuario
                if (isset($_SESSION['id_usuario'])) {
                    $id_usuario = $_SESSION['id_usuario'];
                    $uso_por_usuario = CodigoDescuentoDAO::obtenerUsoPorUsuario($id_usuario, $descuento_info['id_codigo_descuento']);

                    if ($uso_por_usuario >= $descuento_info['uso_por_usuario_maximo']) {
                        $_SESSION['error_codigo'] = 'Has alcanzado el límite de usos de este código de descuento.';
                        header('Location: index.php?controller=carrito&action=index');
                        exit();
                    }
                } else {
                    $_SESSION['error_codigo'] = 'Debes iniciar sesión para aplicar un código de descuento.';
                    header('Location: index.php?controller=carrito&action=index');
                    exit();
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

                // **No incrementar los contadores de uso aquí**
            } else {
                $_SESSION['error_codigo'] = 'El código promocional no es válido o ha expirado.';
            }
        } else {
            $_SESSION['error_codigo'] = 'No se proporcionó un código promocional.';
        }

        header('Location: index.php?controller=carrito&action=index');
        exit();
    }


    // Eliminar descuento aplicado
    public function eliminarDescuento() {
        unset($_SESSION['descuento']);
        unset($_SESSION['codigo_aplicado']);
        $_SESSION['mensaje'] = 'Descuento eliminado correctamente.';
        header('Location: index.php?controller=carrito&action=index');
        exit();
    }

    // Pagar ahora
    public function pagar() {
        // Aquí iría la lógica para procesar el pago
        // Por ejemplo, integración con pasarelas de pago como PayPal, Stripe, etc.

        // Supongamos que tienes una función llamada procesarPago que retorna true si el pago fue exitoso
        $pagoExitoso = $this->procesarPago();

        if ($pagoExitoso) {
            // Verificar si hay un descuento aplicado
            if (isset($_SESSION['codigo_aplicado'])) {
                $codigo = $_SESSION['codigo_aplicado'];
                $descuento_info = CodigoDescuentoDAO::obtenerDescuentoPorCodigo($codigo);

                if ($descuento_info) {
                    // Incrementar el uso total del código
                    CodigoDescuentoDAO::incrementarUsosTotales($descuento_info['id_codigo_descuento']);

                    // Incrementar el uso por usuario
                    if (isset($_SESSION['id_usuario'])) {
                        $id_usuario = $_SESSION['id_usuario'];
                        CodigoDescuentoDAO::incrementarUsosPorUsuario($id_usuario, $descuento_info['id_codigo_descuento']);
                    }
                }
            }

            // Una vez realizado el pago exitosamente, vaciar el carrito y eliminar descuentos
            unset($_SESSION['carrito']);
            unset($_SESSION['descuento']);
            unset($_SESSION['codigo_aplicado']);
            $_SESSION['mensaje'] = 'Compra realizada exitosamente.';
            header('Location: index.php?controller=carrito&action=index');
            exit();
        } else {
            // Manejar el fallo en el pago
            $_SESSION['error'] = 'Hubo un problema al procesar tu pago. Por favor, inténtalo de nuevo.';
            header('Location: index.php?controller=carrito&action=index');
            exit();
        }
    }

    public function mostrarPago() {
        // Calcular el subtotal si no está en la sesión
        if (!isset($_SESSION['subtotal'])) {
            $subtotal = 0;
            foreach ($_SESSION['carrito'] as $producto) {
                $subtotal += $producto['precio_base'] * $producto['cantidad'];
            }
            $_SESSION['subtotal'] = $subtotal;
        } else {
            $subtotal = $_SESSION['subtotal'];
        }

        // Definir los gastos de envío
        if ($subtotal >= 50.00) {
            $gastos_envio = 0.00; // Envío gratuito para compras >= 50€
        } else {
            $gastos_envio = 5.00; // Costo fijo de envío
        }

        // Obtener descuento si aplica
        $descuento = isset($_SESSION['descuento']) ? $_SESSION['descuento'] : 0;

        // Calcular el total
        $total = $subtotal - $descuento + $gastos_envio;

        // Generar un token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Pasar variables a la vista
        $view = 'views/productos/pago.php';
        include_once 'views/main.php';
    }

    /**
     * Procesar la confirmación del pedido
     */
    public function procesarPago() {
        // Verificar que el formulario haya sido enviado vía POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // **Protección CSRF**
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Token de seguridad inválido. Por favor, intenta de nuevo.';
                header('Location: index.php?controller=carrito&action=mostrarPago');
                exit();
            }

            // **Obtener y limpiar los datos del formulario**
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $correo = trim($_POST['correo'] ?? '');

            // **Validaciones básicas**
            $errors = [];

            if (empty($nombre_completo)) {
                $errors[] = 'El nombre completo es obligatorio.';
            }

            if (empty($direccion)) {
                $errors[] = 'La dirección es obligatoria.';
            }

            if (!preg_match('/^\d{10}$/', $telefono)) {
                $errors[] = 'El teléfono debe tener 10 dígitos.';
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo electrónico no es válido.';
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                header('Location: index.php?controller=carrito&action=mostrarPago');
                exit();
            }

            // **Procesar la confirmación del pedido**
            // Aquí podrías guardar el pedido en la base de datos si lo deseas

            // **Incrementar contadores de códigos de descuento si aplica**
            if (isset($_SESSION['codigo_aplicado'])) {
                $codigo = $_SESSION['codigo_aplicado'];
                $descuento_info = CodigoDescuentoDAO::obtenerDescuentoPorCodigo($codigo);

                if ($descuento_info) {
                    // Incrementar el uso total del código
                    CodigoDescuentoDAO::incrementarUsosTotales($descuento_info['id_codigo_descuento']);

                    // Incrementar el uso por usuario
                    if (isset($_SESSION['id_usuario'])) {
                        $id_usuario = $_SESSION['id_usuario'];
                        CodigoDescuentoDAO::incrementarUsosPorUsuario($id_usuario, $descuento_info['id_codigo_descuento']);
                    }
                }
            }

            // **Limpiar el carrito y las sesiones relacionadas**
            unset($_SESSION['carrito']);
            unset($_SESSION['subtotal']);
            unset($_SESSION['descuento']);
            unset($_SESSION['codigo_aplicado']);
            unset($_SESSION['csrf_token']); // Eliminar el token CSRF después de usarlo

            // **Mensaje de éxito**
            $_SESSION['mensaje'] = 'Pedido confirmado exitosamente. ¡Gracias por tu compra!';
            header('Location: index.php?controller=carrito&action=index');
            exit();
        } else {
            // Si el acceso no es vía POST
            $_SESSION['error'] = 'Método de solicitud inválido.';
            header('Location: index.php?controller=carrito&action=mostrarPago');
            exit();
        }
    }
}
?>
