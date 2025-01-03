<?php
// controllers/carritoController.php

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
    
        // Validar y desaplicar el descuento si es necesario
        if (isset($_SESSION['codigo_aplicado']) && isset($_SESSION['descuento'])) {
            $codigo = $_SESSION['codigo_aplicado'];
            $descuento_info = CodigoDescuentoDAO::obtenerDescuentoPorCodigo($codigo);
    
            if ($descuento_info) {
                $minimo_compra = $descuento_info['minimo_compra'];
                if ($subtotal < $minimo_compra) {
                    // Desaplicar el descuento
                    unset($_SESSION['descuento']);
                    unset($_SESSION['codigo_aplicado']);
                    $_SESSION['error_codigo'] = 'El subtotal ha caído por debajo del mínimo requerido para aplicar el código de descuento. El descuento ha sido eliminado.';
                }
            } else {
                // Desaplicar el descuento si el código ya no es válido
                unset($_SESSION['descuento']);
                unset($_SESSION['codigo_aplicado']);
                $_SESSION['error_codigo'] = 'El código de descuento aplicado ya no es válido y ha sido eliminado.';
            }
        }
    
        // Aplicar descuento si existe
        $descuento = isset($_SESSION['descuento']) ? $_SESSION['descuento'] : 0;
        $subtotal_con_descuento = $subtotal - $descuento;
    
        // Asegurarse de que el subtotal_con_descuento no sea negativo
        if ($subtotal_con_descuento < 0) {
            $subtotal_con_descuento = 0;
        }
    
        // Definir los gastos de envío
        if ($subtotal >= 50.00) {
            $gastos_envio = 0.00; // Envío gratuito para compras >= 50€
        } else {
            $gastos_envio = 5.00; // Costo fijo de envío
        }
    
        $total = $subtotal_con_descuento + $gastos_envio;
    
        // Guardar en sesión para uso posterior
        $_SESSION['subtotal'] = $subtotal;
        $_SESSION['gastos_envio'] = $gastos_envio;
        $_SESSION['total'] = $total;
    
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

            include_once 'models/ProductoDAO.php'; // Asegúrate de tener este archivo
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
        if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
            // Manejar actualizaciones individuales
            $producto_id = (int)$_POST['producto_id'];
            $cantidad = (int)$_POST['cantidad'];

            if (isset($_SESSION['carrito'][$producto_id])) {
                if ($cantidad > 0) {
                    $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
                    $_SESSION['mensaje'] = 'Cantidad actualizada correctamente.';
                } else {
                    // Eliminar el producto si la cantidad es 0
                    unset($_SESSION['carrito'][$producto_id]);
                    $_SESSION['mensaje'] = 'Producto eliminado del carrito.';
                }
            } else {
                $_SESSION['error'] = 'El producto no está en el carrito.';
            }
        } elseif (isset($_POST['cantidad']) && is_array($_POST['cantidad'])) {
            // Manejar actualizaciones masivas
            foreach ($_POST['cantidad'] as $producto_id => $cantidad) {
                $producto_id = (int)$producto_id;
                $cantidad = (int)$cantidad;

                if (isset($_SESSION['carrito'][$producto_id])) {
                    if ($cantidad > 0) {
                        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
                        $_SESSION['mensaje'] = 'Cantidad actualizada correctamente.';
                    } else {
                        // Eliminar el producto si la cantidad es 0
                        unset($_SESSION['carrito'][$producto_id]);
                        $_SESSION['mensaje'] = 'Producto eliminado del carrito.';
                    }
                }
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
                $subtotal = $_SESSION['subtotal'];

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

    // Mostrar la página de pago
    public function mostrarPago() {
        // Verificar que el carrito no esté vacío
        if (empty($_SESSION['carrito'])) {
            $_SESSION['error'] = 'Tu carrito está vacío. Agrega productos antes de proceder al pago.';
            header('Location: index.php?controller=carrito&action=index');
            exit();
        }

        // Verificar que el usuario está autenticado
        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para proceder al pago.';
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }

        // Obtener la información del usuario
        $id_usuario = $_SESSION['id_usuario'];
        $usuario = UsuarioDAO::obtenerUsuarioPorId($id_usuario);

        if (!$usuario) {
            $_SESSION['error'] = 'No se pudo obtener la información del usuario.';
            header('Location: index.php?controller=carrito&action=index');
            exit();
        }

        // Obtener las direcciones del usuario
        $direcciones = DireccionDAO::getDireccionesByUsuario($id_usuario);

        if (empty($direcciones)) {
            $_SESSION['error'] = 'No tienes direcciones registradas. Por favor, añade una dirección antes de proceder al pago.';
            header('Location: index.php?controller=usuario&action=addDireccion'); // Asegúrate de tener esta acción
            exit();
        }

        // Calcular los valores necesarios
        $subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;
        $descuento = isset($_SESSION['descuento']) ? $_SESSION['descuento'] : 0;

        // Definir los gastos de envío
        if ($subtotal >= 50.00) {
            $gastos_envio = 0.00; // Envío gratuito para compras >= 50€
        } else {
            $gastos_envio = 5.00; // Costo fijo de envío
        }

        $total = $subtotal - $descuento + $gastos_envio;

        // Guardar en sesión para uso posterior
        $_SESSION['subtotal'] = $subtotal;
        $_SESSION['gastos_envio'] = $gastos_envio;
        $_SESSION['total'] = $total; // Asegúrate de que esto esté presente

        // Pasar variables a la vista
        $view = 'views/productos/pago.php';
        include_once 'views/main.php';
    }

    // Procesar el pago y crear el pedido
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
            $metodo_pago = trim($_POST['metodo_pago'] ?? '');

            // **Validaciones básicas**
            $errors = [];

            if (empty($nombre_completo)) {
                $errors[] = 'El nombre completo es obligatorio.';
            }

            if (empty($direccion)) {
                $errors[] = 'La dirección es obligatoria.';
            }

            // **Validación del Teléfono a 9 Dígitos**
            if (empty($telefono)) {
                $errors[] = 'El teléfono es obligatorio.';
            } elseif (!preg_match('/^\d{9}$/', $telefono)) { // Cambiado de 10 a 9 dígitos
                $errors[] = 'El teléfono debe tener exactamente 9 dígitos.';
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo electrónico no es válido.';
            }

            if (empty($metodo_pago)) {
                $errors[] = 'El método de pago es obligatorio.';
            }

            // **Validar detalles de pago según el método seleccionado**
            $detalles_pago = '';
            if ($metodo_pago === 'Tarjeta de Crédito') {
                $numero_tarjeta = trim($_POST['numero_tarjeta'] ?? '');
                $mes_expiracion = trim($_POST['mes_expiracion'] ?? '');
                $ano_expiracion = trim($_POST['ano_expiracion'] ?? '');
                $cvv = trim($_POST['cvv'] ?? '');

                // **Validación del Número de Tarjeta a 16 Dígitos**
                if (empty($numero_tarjeta)) {
                    $errors[] = 'El número de tarjeta es obligatorio.';
                } elseif (!preg_match('/^\d{16}$/', $numero_tarjeta)) {
                    $errors[] = 'El número de tarjeta debe tener exactamente 16 dígitos.';
                }

                // **Validación del Mes de Expiración**
                if (empty($mes_expiracion)) {
                    $errors[] = 'El mes de expiración es obligatorio.';
                } elseif (!preg_match('/^\d{1,2}$/', $mes_expiracion) || (int)$mes_expiracion < 1 || (int)$mes_expiracion > 12) {
                    $errors[] = 'El mes de expiración es inválido.';
                }

                // **Validación del Año de Expiración**
                if (empty($ano_expiracion)) {
                    $errors[] = 'El año de expiración es obligatorio.';
                } elseif (!preg_match('/^\d{4}$/', $ano_expiracion)) {
                    $errors[] = 'El año de expiración es inválido.';
                }

                // **Validación del CVV a 3 Dígitos**
                if (empty($cvv)) {
                    $errors[] = 'El CVV es obligatorio.';
                } elseif (!preg_match('/^\d{3}$/', $cvv)) {
                    $errors[] = 'El CVV debe tener exactamente 3 dígitos.';
                }

                // **Detalles de Pago (Enmascarados)**
                $detalles_pago = "Número de Tarjeta: **** **** **** " . substr($numero_tarjeta, -4) . ", Exp: {$mes_expiracion}/{$ano_expiracion}, CVV: ***";
            } elseif ($metodo_pago === 'PayPal') {
                $paypal_email = trim($_POST['paypal_email'] ?? '');
                if (empty($paypal_email)) {
                    $errors[] = 'La cuenta de PayPal es obligatoria.';
                } elseif (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'El correo electrónico de PayPal no es válido.';
                }
                $detalles_pago = "Cuenta de PayPal: {$paypal_email}";
            } elseif ($metodo_pago === 'Transferencia Bancaria') {
                $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
                if (empty($numero_cuenta)) {
                    $errors[] = 'El número de cuenta es obligatorio.';
                } elseif (!preg_match('/^[A-Z]{2}\d{22}$/', $numero_cuenta)) {
                    $errors[] = 'El número de cuenta debe seguir el formato IBAN (ej. ES0000000000000000000000).';
                }
                $detalles_pago = "Número de Cuenta: {$numero_cuenta}";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = $errors;
                header('Location: index.php?controller=carrito&action=mostrarPago');
                exit();
            }

            // **Obtener 'subtotal', 'descuento' y 'gastos_envio' de la sesión**
            $subtotal = $_SESSION['subtotal'] ?? 0;
            $descuento = $_SESSION['descuento'] ?? 0;
            $gastos_envio = $_SESSION['gastos_envio'] ?? 0;
            $total = $_SESSION['total'] ?? 0; // Ahora $total está definido

            // **Procesar la confirmación del pedido**
            // Iniciar una transacción para asegurar la integridad de los datos
            $conn = DataBase::connect();
            $conn->begin_transaction();

            try {
                // Crear el pedido
                $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;

                $pedido = new Pedido(
                    $id_usuario,
                    $nombre_completo,
                    $direccion,
                    $telefono,
                    $correo,
                    $metodo_pago,
                    $detalles_pago,
                    $subtotal,
                    $descuento,
                    $gastos_envio,
                    $total
                );

                $pedido_creado = PedidoDAO::crearPedido($pedido);
                if (!$pedido_creado) {
                    throw new Exception('Error al crear el pedido.');
                }

                // Crear los detalles del pedido
                $detalles_pedido = []; // Array para almacenar los detalles

                foreach ($_SESSION['carrito'] as $producto) {
                    $detalle = new DetallePedido(
                        $pedido_creado->getIdPedido(),
                        $producto['id_producto'],
                        $producto['nombre'],
                        $producto['precio_base'],
                        $producto['cantidad'],
                        $producto['precio_base'] * $producto['cantidad']
                    );

                    $resultado_detalle = DetallePedidoDAO::agregarDetallePedido($detalle);
                    if (!$resultado_detalle) {
                        throw new Exception('Error al crear el detalle del pedido.');
                    }

                    // Almacenar los detalles en el array
                    $detalles_pedido[] = [
                        'nombre_producto' => $producto['nombre'],
                        'precio_unitario' => $producto['precio_base'],
                        'cantidad' => $producto['cantidad'],
                        'total_producto' => $producto['precio_base'] * $producto['cantidad']
                    ];
                }

                // Confirmar la transacción
                $conn->commit();
            } catch (Exception $e) {
                // Revertir la transacción en caso de error
                $conn->rollback();
                error_log("Error al procesar el pedido: " . $e->getMessage());
                $_SESSION['error'] = 'Hubo un problema al procesar tu pedido. Por favor, intenta nuevamente.';
                header('Location: index.php?controller=carrito&action=mostrarPago');
                exit();
            }

            // **Incrementar los contadores de códigos de descuento si aplica**
            if (isset($_SESSION['codigo_aplicado'])) {
                $codigo = strtoupper($_SESSION['codigo_aplicado']);
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

            // **Almacenar los detalles del pedido en la sesión para la confirmación**
            $_SESSION['detalles_pedido'] = $detalles_pedido;

            // **Almacenar otros detalles en la sesión para la confirmación**
            $_SESSION['nombre_completo'] = $nombre_completo;
            $_SESSION['metodo_pago'] = $metodo_pago;
            $_SESSION['total'] = $total;
            $_SESSION['fecha_pedido'] = $pedido_creado->getFechaPedido();

            // **Limpiar el carrito y las sesiones relacionadas**
            unset($_SESSION['carrito']);
            unset($_SESSION['subtotal']);
            unset($_SESSION['descuento']);
            unset($_SESSION['codigo_aplicado']);
            unset($_SESSION['csrf_token']); // Eliminar el token CSRF después de usarlo

            // **Mensaje de éxito y redirección a la confirmación**
            $_SESSION['success'] = 'Pedido confirmado exitosamente. ¡Gracias por tu compra!';
            header('Location: index.php?controller=carrito&action=confirmacion');
            exit();
        }
    }

    // Mostrar la página de confirmación
    public function confirmacion() {
        // Verificar que el pedido fue creado exitosamente
        if (!isset($_SESSION['success'])) {
            $_SESSION['error'] = 'No se ha realizado ningún pedido.';
            header('Location: index.php?controller=carrito&action=index');
            exit();
        }

        // Incluir la vista de confirmación
        $view = 'views/productos/confirmacion.php';
        include_once 'views/main.php';
    }

    /**
     * Carga los productos del último pedido en el carrito.
     */
    public function cargarUltimoPedido() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            // Verificar si el usuario está autenticado
            if (!isset($_SESSION['id_usuario'])) {
                $_SESSION['error'] = 'Debes iniciar sesión para cargar el último pedido.';
                header('Location: index.php?controller=usuario&action=login');
                exit();
            }
    
            $id_usuario = $_SESSION['id_usuario'];
            $ultimo_pedido = PedidoDAO::obtenerUltimoPedidoPorUsuario($id_usuario);
    
            if ($ultimo_pedido) {
                $id_pedido = $ultimo_pedido['id_pedido'];
                $detalles_pedido = DetallePedidoDAO::obtenerDetallesPorPedido($id_pedido);
    
                if (!empty($detalles_pedido)) {
                    // Limpiar el carrito actual
                    $_SESSION['carrito'] = [];
    
                    foreach ($detalles_pedido as $detalle) {
                        // Acceder a las propiedades usando métodos getter
                        $producto_id = $detalle->getIdProducto();
                        $cantidad = $detalle->getCantidad();
    
                        // Obtener información actual del producto
                        $producto = ProductoDAO::getProducto($producto_id);
    
                        if ($producto) {
                            // Agregar o actualizar el producto en el carrito
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
                            // Manejar el caso en que el producto ya no existe
                            $_SESSION['error'] = 'Algunos productos del último pedido ya no están disponibles.';
                        }
                    }
    
                    // Calcular nuevamente los totales del carrito
                    $this->index();
    
                    $_SESSION['mensaje'] = 'Los productos de tu último pedido han sido cargados en el carrito.';
                } else {
                    $_SESSION['error'] = 'El último pedido no contiene productos.';
                }
            } else {
                $_SESSION['error'] = 'No tienes pedidos anteriores para cargar.';
            }
    
            // Redirigir al carrito
            header('Location: index.php?controller=carrito&action=index');
            exit();
        } else {
            // Si no es una solicitud válida, redirigir al carrito
            header('Location: index.php?controller=carrito&action=index');
            exit();
        }
    }    
}
?>
