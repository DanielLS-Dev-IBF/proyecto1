<?php
// controllers/adminController.php

include_once 'models/UsuarioDAO.php';
include_once 'models/PedidoDAO.php';
include_once 'models/DetallePedidoDAO.php';
include_once 'models/ProductoDAO.php';
include_once 'models/DireccionDAO.php';

class AdminController
{
    public function __construct()
    {
        // Verifica si es admin
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header('Location: index.php?controller=Admin&action=index');
            exit();
        }
    }

    // Carga la vista principal con el menú y un contenedor vacío
    public function index()
    {
        $view = 'views/admin/index.php';
        include_once 'views/main.php';
    }

    // JSON para Usuarios
    public function getUsuariosJSON()
    {
        $usuarios = UsuarioDAO::getAllUsers();
        $data = [];

        if (empty($usuarios)) {
            echo json_encode([]);
            return;
        }

        foreach ($usuarios as $u) {
            $data[] = [
                'id_usuario'     => $u->getId_usuario(),
                'nombre_completo'=> $u->getNombre_completo(),
                'email'          => $u->getEmail(),
                'rol'            => $u->getRol(),
                'telefono'       => $u->getTelefono()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getUsuarioDetallesJSON()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id_usuario = $_GET['id_usuario'] ?? null;
            if (!$id_usuario) {
                $this->sendJsonResponse(['status'=>'error','message'=>'ID usuario no válido']);
                return;
            }

            // Obtener usuario
            $usuario = UsuarioDAO::obtenerUsuarioPorId($id_usuario);
            if (!$usuario) {
                $this->sendJsonResponse(['status'=>'error','message'=>'Usuario no encontrado']);
                return;
            }

            // Obtener direcciones
            $direcciones = DireccionDAO::getDireccionesByUsuario($id_usuario); 
            // DireccionDAO::getDireccionesByUsuario => retorna un arreglo de direcciones

            $data = [
                'status' => 'ok',
                'usuario' => [
                    'id_usuario' => $usuario->getId_usuario(),
                    'nombre_completo' => $usuario->getNombre_completo(),
                    'telefono' => $usuario->getTelefono(),
                    'correo' => $usuario->getEmail(),
                    'direcciones' => []
                ]
            ];

            foreach ($direcciones as $dir) {
                // Ajusta según tu modelo
                $data['usuario']['direcciones'][] = [
                    'id_direccion' => $dir->getIdDireccion(), 
                    'texto' => $dir->getDireccion() 
                ];
            }

            $this->sendJsonResponse($data);
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    // JSON para Pedidos
    public function getPedidosJSON()
    {
        $pedidos = PedidoDAO::getAllPedidos();
        $data = [];

        if (empty($pedidos)) {
            echo json_encode([]);
            return;
        }

        foreach ($pedidos as $p) {
            $data[] = [
                'id_pedido'   => $p['id_pedido'],
                'id_usuario'  => $p['id_usuario'],
                'fecha_pedido'=> $p['fecha_pedido'],
                'total'       => $p['total'],
                'direccion'   => $p['direccion']
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getPedidoDetallesJSON()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id_pedido = isset($_GET['id_pedido']) ? intval($_GET['id_pedido']) : null;

            if (!$id_pedido) {
                $this->sendJsonResponse(['status' => 'error', 'message' => 'ID del pedido no válido.']);
                return;
            }

            // Obtener el pedido
            $pedido = PedidoDAO::getPedidoById($id_pedido); // Implementar en PedidoDAO
            if (!$pedido) {
                $this->sendJsonResponse(['status' => 'error', 'message' => 'Pedido no encontrado.']);
                return;
            }

            // Obtener los detalles del pedido
            $detalles = DetallePedidoDAO::obtenerDetallesPorPedido($id_pedido); // Implementar en DetallePedidoDAO

            // Obtener lista de productos disponibles
            $productosDisponibles = ProductoDAO::getAll();

            // Construir la respuesta
            $response = [
                'status' => 'ok',
                'pedido' => [
                    'id_pedido' => $pedido->getIdPedido(),
                    'id_usuario' => $pedido->getIdUsuario(),
                    'direccion' => $pedido->getDireccion(),
                    'telefono' => $pedido->getTelefono(),
                    'correo' => $pedido->getCorreo(),
                    'metodo_pago' => $pedido->getMetodoPago(),
                    'subtotal' => $pedido->getSubtotal(),
                    'descuento' => $pedido->getDescuento(),
                    'gastos_envio' => $pedido->getGastosEnvio(),
                    'total' => $pedido->getTotal(),
                    'fecha_pedido' => $pedido->getFechaPedido(),
                    'productos' => []
                ],
                'productosDisponibles' => []
            ];

            foreach ($detalles as $detalle) {
                $response['pedido']['productos'][] = [
                    'id_detalle_pedido' => $detalle->getIdPedido(),
                    'id_producto' => $detalle->getIdProducto(),
                    'nombre_producto' => $detalle->getNombreProducto(),
                    'precio_unitario' => $detalle->getPrecioUnitario(),
                    'cantidad' => $detalle->getCantidad(),
                    'total_producto' => $detalle->getTotalProducto()
                ];
            }

            foreach ($productosDisponibles as $producto) {
                $response['productosDisponibles'][] = [
                    'id_producto' => $producto->getId_producto(),
                    'nombre' => $producto->getNombre(),
                    'precio_base' => $producto->getPrecio_base()
                ];
            }

            $this->sendJsonResponse(['status' => 'ok', 'pedido' => $response['pedido'], 'productosDisponibles' => $response['productosDisponibles']]);
        } else {
            // Método no permitido
            header("HTTP/1.1 405 Method Not Allowed");
            $this->sendJsonResponse(['status' => 'error', 'message' => 'Método no permitido.']);
        }
    }


    // JSON para Productos
    public function getProductosJSON()
    {
        $productos = ProductoDAO::getAll();
        $data = [];

        if (empty($productos)) {
            echo json_encode([]);
            return;
        }

        foreach ($productos as $prod) {
            $data[] = [
                'id_producto' => $prod->getId_producto(),
                'nombre'      => $prod->getNombre(),
                'descripcion' => $prod->getDescripcion(),
                'precio_base' => $prod->getPrecio_base(),
                'tipo'        => $prod->getTipo(),
                'img'         => $prod->getImg()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function createUsuario()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ya no es necesario llamar a session_start() aquí

        // Recolectar datos de POST
        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $telefono = trim($_POST['telefono'] ?? '');
        $rol = trim($_POST['rol'] ?? 'usuario'); // Rol por defecto "usuario"

        $errores = [];

        // Validaciones
        if (empty($nombre_completo)) {
            $errores['nombre_completo'] = 'El nombre completo es obligatorio.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El correo electrónico no es válido.';
        }
        if (empty($password) || strlen($password) < 6) {
            $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($password !== $confirm_password) {
            $errores['confirm_password'] = 'Las contraseñas no coinciden.';
        }
        if (empty($telefono) || !ctype_digit($telefono)) {
            $errores['telefono'] = 'El teléfono no es válido.';
        }

        // Verificar si el email ya existe
        if (empty($errores)) {
            if (UsuarioDAO::obtenerUsuarioPorEmail($email)) {
                $errores['email'] = 'El correo electrónico ya está en uso.';
            }
        }

        // Si no hay errores, guardar el usuario
        if (empty($errores)) {
            $usuario = new Usuario();
            $usuario->setNombre_completo($nombre_completo);
            $usuario->setEmail($email);
            $usuario->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $usuario->setTelefono((int)$telefono);
            $usuario->setRol($rol);

            $resultado = UsuarioDAO::newUser($usuario);

            if ($resultado) {
                $this->logAction("Usuario", "INSERT", id_afectado: $resultado); 
                header('Content-Type: application/json');
                echo json_encode(['status' => 'ok', 'message' => 'Usuario creado exitosamente.']);
                exit();
            } else {
                $errores['general'] = 'Error al guardar el usuario. Inténtalo nuevamente.';
            }
        }

        // Devolver errores si los hay
        if (!empty($errores)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errores]);
            exit();
        }
    } else {
        // Método no permitido
        header("HTTP/1.1 405 Method Not Allowed");
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
        exit();
    }
}



    private function sendJsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }


    public function updateUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? null;
            $nombre = $_POST['nombre_completo'] ?? '';
            $email  = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (!$id_usuario) {
                echo json_encode(['status'=>'error','message'=>'ID de usuario no válido']);
                return;
            }

            // Obtener el usuario
            $u = UsuarioDAO::obtenerUsuarioPorId($id_usuario);
            if (!$u) {
                echo json_encode(['status'=>'error','message'=>'Usuario no encontrado']);
                return;
            }

            // Actualizar campos
            $u->setNombre_completo($nombre);
            $u->setEmail($email);
            $u->setTelefono($telefono);

            // Si se ha ingresado una nueva contraseña, encriptarla y actualizar
            if (!empty($password) || !empty($confirm_password)) {
                if ($password !== $confirm_password) {
                    echo json_encode(['status'=>'error','message'=>'Las contraseñas no coinciden']);
                    return;
                }

                if (strlen($password) < 6) {
                    echo json_encode(['status'=>'error','message'=>'La contraseña debe tener al menos 6 caracteres']);
                    return;
                }

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                if ($hashedPassword === false) {
                    echo json_encode(['status'=>'error','message'=>'Error al encriptar la contraseña']);
                    return;
                }

                $u->setPassword($hashedPassword);
            }

            // Llamar DAO
            $ok = UsuarioDAO::updateUser($u);
            if ($ok) {
                $this->logAction("Usuario", "UPDATE", $id_usuario);
                echo json_encode(['status'=>'ok','message'=>'Usuario actualizado']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Error al actualizar usuario']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    public function deleteUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_usuario'] ?? null;
            if ($id) {
                $ok = UsuarioDAO::deleteUser($id);
                if ($ok) {
                    $this->logAction("Usuario", "DELETE", $id);
                    echo json_encode(['status' => 'ok', 'message' => 'Usuario eliminado con éxito']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID usuario no válido']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    public function createPedido()
    {
        // Leer los datos JSON enviados desde el front-end
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que los datos se hayan decodificado correctamente
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendJsonResponse(['status' => 'error', 'message' => 'Datos JSON inválidos.']);
                return;
            }

            // Recolectar datos del pedido
            $id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : null;
            $direccion = trim($data['direccion'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $correo = trim($data['correo'] ?? '');
            $metodo_pago = trim($data['metodo_pago'] ?? '');
            $productos = $data['productos'] ?? [];

            $errores = [];

            // Validaciones básicas
            if (!$id_usuario) {
                $errores['id_usuario'] = 'El ID de usuario es obligatorio.';
            }
            if (empty($direccion)) {
                $errores['direccion'] = 'La dirección es obligatoria.';
            }
            if (empty($metodo_pago)) {
                $errores['metodo_pago'] = 'El método de pago es obligatorio.';
            }
            if (empty($productos) || !is_array($productos)) {
                $errores['productos'] = 'Debe agregar al menos un producto al pedido.';
            } else {
                foreach ($productos as $index => $producto) {
                    if (empty($producto['id_producto'])) {
                        $errores["productos_$index"] = 'El ID del producto es obligatorio.';
                    }
                    if (empty($producto['cantidad']) || intval($producto['cantidad']) <= 0) {
                        $errores["cantidad_$index"] = 'La cantidad debe ser un número positivo.';
                    }
                    // Puedes agregar más validaciones según sea necesario
                }
            }

            if (!empty($errores)) {
                $this->sendJsonResponse(['status' => 'error', 'errors' => $errores]);
                return;
            }

            // Calcular subtotal, descuento, gastos de envío y total
            $subtotal = 0;
            foreach ($productos as $producto) {
                $subtotal += floatval($producto['precio_unitario']) * intval($producto['cantidad']);
            }

            $descuento = 0; // Implementar lógica de descuentos si es necesario
            $gastos_envio = 0; // Implementar lógica de gastos de envío

            // Lógica de gastos de envío
            $minimo_envio_gratuito = 50.00;
            if ($subtotal - $descuento >= $minimo_envio_gratuito) {
                $gastos_envio = 0.00; // Envío gratuito
            } else {
                $gastos_envio = 5.00; // Costo fijo de envío
            }

            $total = $subtotal - $descuento + $gastos_envio;

            // Crear objeto Pedido
            $pedido = new Pedido(
                $id_usuario,
                '', // nombre_completo si es necesario
                $direccion,
                $telefono,
                $correo,
                $metodo_pago,
                'Panel de administrador',
                $subtotal,
                $descuento,
                $gastos_envio,
                $total
            );

            // Insertar pedido y detalles dentro de una transacción
            $con = DataBase::connect();
            $con->begin_transaction();

            try {
                // Insertar pedido
                $pedidoInsertado = PedidoDAO::crearPedido($pedido, $con);
                if (!$pedidoInsertado) {
                    throw new Exception('Error al crear el pedido.');
                }

                // Insertar detalles del pedido
                foreach ($productos as $producto) {
                    $detalle = new DetallePedido(
                        $pedidoInsertado->getIdPedido(),
                        intval($producto['id_producto']),
                        trim($producto['nombre_producto']),
                        floatval($producto['precio_unitario']),
                        intval($producto['cantidad']),
                        floatval($producto['total_producto'])
                    );

                    $resultado_detalle = DetallePedidoDAO::agregarDetallePedido($detalle, $con);
                    if (!$resultado_detalle) {
                        throw new Exception('Error al crear el detalle del pedido.');
                    }
                }

                // Confirmar la transacción
                $con->commit();
                $this->logAction("Pedido", "INSERT", $pedidoInsertado->getIdPedido());
                $this->sendJsonResponse(['status' => 'ok', 'message' => 'Pedido creado exitosamente.']);
            } catch (Exception $e) {
                // Revertir la transacción en caso de error
                $con->rollback();
                error_log("Error al procesar el pedido: " . $e->getMessage());
                $this->sendJsonResponse(['status' => 'error', 'message' => 'Error al crear el pedido.']);
            }

            $con->close();
        }
    }


    public function updatePedido()
    {
        // Leer los datos JSON enviados desde el front-end
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que los datos se hayan decodificado correctamente
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendJsonResponse(['status' => 'error', 'message' => 'Datos JSON inválidos.']);
                return;
            }

            // Recolectar datos del pedido
            $id_pedido = isset($data['id_pedido']) ? intval($data['id_pedido']) : null;
            $id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : null;
            $direccion = trim($data['direccion'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $correo = trim($data['correo'] ?? '');
            $metodo_pago = trim($data['metodo_pago'] ?? '');
            $productos = $data['productos'] ?? [];

            $errores = [];

            // Validaciones básicas
            if (!$id_pedido) {
                $errores['id_pedido'] = 'El ID del pedido es obligatorio.';
            }
            if (!$id_usuario) {
                $errores['id_usuario'] = 'El ID de usuario es obligatorio.';
            }
            if (empty($direccion)) {
                $errores['direccion'] = 'La dirección es obligatoria.';
            }
            if (empty($metodo_pago)) {
                $errores['metodo_pago'] = 'El método de pago es obligatorio.';
            }
            if (empty($productos) || !is_array($productos)) {
                $errores['productos'] = 'Debe agregar al menos un producto al pedido.';
            } else {
                foreach ($productos as $index => $producto) {
                    if (empty($producto['id_producto'])) {
                        $errores["productos_$index"] = 'El ID del producto es obligatorio.';
                    }
                    if (empty($producto['cantidad']) || intval($producto['cantidad']) <= 0) {
                        $errores["cantidad_$index"] = 'La cantidad debe ser un número positivo.';
                    }
                    // Puedes agregar más validaciones según sea necesario
                }
            }

            if (!empty($errores)) {
                $this->sendJsonResponse(['status' => 'error', 'errors' => $errores]);
                return;
            }

            // Calcular subtotal, descuento, gastos de envío y total
            $subtotal = 0;
            foreach ($productos as $producto) {
                $subtotal += floatval($producto['precio_unitario']) * intval($producto['cantidad']);
            }

            $descuento = 0; // Implementar lógica de descuentos si es necesario

            // Lógica de gastos de envío
            $minimo_envio_gratuito = 50.00;
            if ($subtotal - $descuento >= $minimo_envio_gratuito) {
                $gastos_envio = 0.00; // Envío gratuito
            } else {
                $gastos_envio = 5.00; // Costo fijo de envío
            }

            $total = $subtotal - $descuento + $gastos_envio;

            // Crear objeto Pedido
            $pedido = new Pedido(
                $id_usuario,
                '', // nombre_completo si es necesario
                $direccion,
                $telefono,
                $correo,
                $metodo_pago,
                '', // detalles_pago si es necesario
                $subtotal,
                $descuento,
                $gastos_envio,
                $total,
                date('Y-m-d H:i:s') // fecha_pedido o mantener la original
            );
            $pedido->setIdPedido($id_pedido);

            // Actualizar pedido y detalles dentro de una transacción
            $con = DataBase::connect();
            $con->begin_transaction();

            try {
                // Actualizar pedido
                $pedidoActualizado = PedidoDAO::updatePedido($pedido, $con); // Modificar para aceptar conexión
                if (!$pedidoActualizado) {
                    throw new Exception('Error al actualizar el pedido.');
                }

                // Obtener detalles actuales del pedido
                $detallesActuales = DetallePedidoDAO::obtenerDetallesPorPedido($id_pedido, $con); // Modificar para aceptar conexión
                $detallesActualesIds = array_map(function($detalle) {
                    return $detalle->getIdPedido();
                }, $detallesActuales);

                // Procesar los productos enviados
                $productosEnviados = $productos;

                foreach ($productosEnviados as $producto) {
                    // Verificar si el detalle ya existe (por ejemplo, por id_producto)
                    $detalleExistente = null;
                    foreach ($detallesActuales as $detalle) {
                        if ($detalle->getIdProducto() == $producto['id_producto']) {
                            $detalleExistente = $detalle;
                            break;
                        }
                    }

                    if ($detalleExistente) {
                        // Actualizar detalle existente
                        $detalleExistente->setCantidad(intval($producto['cantidad']));
                        $detalleExistente->setTotalProducto(floatval($producto['total_producto']));
                        $detalleExistente->setNombreProducto(trim($producto['nombre_producto']));
                        $detalleExistente->setPrecioUnitario(floatval($producto['precio_unitario']));

                        $actualizado = DetallePedidoDAO::actualizarDetallePedido($detalleExistente, $con); // Implementar en DetallePedidoDAO
                        if (!$actualizado) {
                            throw new Exception('Error al actualizar un detalle del pedido.');
                        }

                        // Eliminar del array de detalles actuales para identificar los que deben eliminarse
                        $detallesActualesIds = array_diff($detallesActualesIds, [$detalleExistente->getIdPedido()]);
                    } else {
                        // Agregar nuevo detalle
                        $detalleNuevo = new DetallePedido(
                            $id_pedido,
                            intval($producto['id_producto']),
                            trim($producto['nombre_producto']),
                            floatval($producto['precio_unitario']),
                            intval($producto['cantidad']),
                            floatval($producto['total_producto'])
                        );

                        $agregado = DetallePedidoDAO::agregarDetallePedido($detalleNuevo, $con); // Modificar para aceptar conexión
                        if (!$agregado) {
                            throw new Exception('Error al agregar un nuevo detalle al pedido.');
                        }
                    }
                }

                // Eliminar los detalles que no están en la lista enviada
                foreach ($detallesActualesIds as $id_detalle) {
                    $eliminado = DetallePedidoDAO::eliminarDetallePedido($id_detalle, $con); // Implementar en DetallePedidoDAO
                    if (!$eliminado) {
                        throw new Exception('Error al eliminar un detalle del pedido.');
                    }
                }

                // Confirmar transacción
                $con->commit();
                $this->logAction("Pedido", "UPDATE", $id_pedido);
                $this->sendJsonResponse(['status' => 'ok', 'message' => 'Pedido actualizado exitosamente.']);
            } catch (Exception $e) {
                // Revertir transacción
                $con->rollback();
                error_log($e->getMessage());
                $this->sendJsonResponse(['status' => 'error', 'message' => 'Error al actualizar el pedido.']);
            }

            $con->close();
        }
    }

    public function deletePedido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_pedido'] ?? null;
            if ($id) {
                $ok = PedidoDAO::deletePedido($id);
                if ($ok) {
                    $this->logAction("Pedido", "DELETE", $id);
                    echo json_encode(['status'=>'ok','message'=>'Pedido eliminado exitosamente.']);
                } else {
                    echo json_encode(['status'=>'error','message'=>'Error al eliminar pedido.']);
                }
            } else {
                echo json_encode(['status'=>'error','message'=>'ID pedido no válido.']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }
    public function createProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recolectar datos de POST
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio_base = $_POST['precio_base'] ?? '';
            $tipo = trim($_POST['tipo'] ?? '');
            $img = trim($_POST['img'] ?? '');

            $errores = [];

            // Validaciones
            if (empty($nombre)) {
                $errores['nombre'] = 'El nombre del producto es obligatorio.';
            }

            if (empty($precio_base)) {
                $errores['precio_base'] = 'El precio base es obligatorio.';
            } elseif (!is_numeric($precio_base) || $precio_base <= 0) {
                $errores['precio_base'] = 'El precio base debe ser un número positivo.';
            }

            // Validar tipo
            $tiposPermitidos = ['Bowl', 'Postre', 'Bebida'];
            if (!in_array($tipo, $tiposPermitidos)) {
                $errores['tipo'] = 'El tipo de producto no es válido.';
            }

            // Validar imagen
            if (empty($img)) {
                $errores['img'] = 'La ruta de la imagen es obligatoria.';
            } else {

                if (!preg_match('/^img\/Productos\/.+\.(jpg|jpeg|png|gif|webp)$/i', $img)) {
                    $errores['img'] = 'La ruta de la imagen no tiene un formato válido.';
                }
            }

            // Puedes agregar más validaciones según tus necesidades

            // Si no hay errores, guardar el producto
            if (empty($errores)) {
                $producto = new Producto(
                    null, // id_producto autoincrement
                    $nombre,
                    $descripcion,
                    $precio_base,
                    $img, // Ruta de la imagen
                    $tipo
                );

                $resultado = ProductoDAO::store($producto);

                if ($resultado !== false) {
                    $this->logAction("Producto", "INSERT", $resultado);
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'ok', 'message' => 'Producto creado exitosamente.']);
                    exit();
                } else {
                    $errores['general'] = 'Error al guardar el producto. Inténtalo nuevamente.';
                }
            }

            // Devolver errores si los hay
            if (!empty($errores)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'errors' => $errores]);
                exit();
            }
        } else {
            // Método no permitido
            header("HTTP/1.1 405 Method Not Allowed");
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            exit();
        }
    }


    public function updateProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recolectar datos de POST
            $id_producto = $_POST['id_producto'] ?? null;
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio_base = $_POST['precio_base'] ?? '';
            $tipo = trim($_POST['tipo'] ?? '');
            $img = trim($_POST['img'] ?? '');

            $errores = [];

            if (!$id_producto) {
                $errores['id_producto'] = 'ID del producto no válido.';
            }

            // Validaciones
            if (empty($nombre)) {
                $errores['nombre'] = 'El nombre del producto es obligatorio.';
            }

            if (empty($precio_base)) {
                $errores['precio_base'] = 'El precio base es obligatorio.';
            } elseif (!is_numeric($precio_base) || $precio_base <= 0) {
                $errores['precio_base'] = 'El precio base debe ser un número positivo.';
            }

            // Validar tipo
            $tiposPermitidos = ['Bowl', 'Postre', 'Bebida'];
            if (!in_array($tipo, $tiposPermitidos)) {
                $errores['tipo'] = 'El tipo de producto no es válido.';
            }

            // Validar imagen
            if (empty($img)) {
                $errores['img'] = 'La ruta de la imagen es obligatoria.';
            } else {
                if (!preg_match('/^img\/Productos\/.+\.(jpg|jpeg|png|gif|webp)$/i', $img)) {
                    $errores['img'] = 'La ruta de la imagen no tiene un formato válido.';
                }
            }

            // Puedes agregar más validaciones según tus necesidades

            // Si no hay errores, actualizar el producto
            if (empty($errores)) {
                $producto = ProductoDAO::getProducto($id_producto);
                if (!$producto) {
                    $errores['producto'] = 'Producto no encontrado.';
                } else {
                    $producto->setNombre($nombre);
                    $producto->setDescripcion($descripcion);
                    $producto->setPrecio_base($precio_base);
                    $producto->setTipo($tipo);
                    $producto->setImg($img);

                    $resultado = ProductoDAO::updateProducto($producto);

                    if ($resultado !== false) {
                        $this->logAction("Producto", "UPDATE", $id_producto);
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'ok', 'message' => 'Producto actualizado exitosamente.']);
                        exit();
                    } else {
                        $errores['general'] = 'Error al actualizar el producto. Inténtalo nuevamente.';
                    }
                }
            }

            // Devolver errores si los hay
            if (!empty($errores)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'errors' => $errores]);
                exit();
            }
        } else {
            // Método no permitido
            header("HTTP/1.1 405 Method Not Allowed");
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            exit();
        }
    }

    public function deleteProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_producto'] ?? null;
            if ($id) {
                $ok = ProductoDAO::destroy($id);
                if ($ok !== false) {
                    $this->logAction("Producto", "DELETE", $id);
                    echo json_encode(['status'=>'ok','message'=>'Producto eliminado']);
                } else {
                    echo json_encode(['status'=>'error','message'=>'Error al eliminar producto']);
                }
            } else {
                echo json_encode(['status'=>'error','message'=>'ID producto no válido']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    private function logAction($tabla, $tipo_accion, $id_afectado)
    {
        // Conexión a la base de datos
        $con = DataBase::connect();
        // Preparar la sentencia
        $stmt = $con->prepare("INSERT INTO Logs (tabla, tipo_accion, id_afectado) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $tabla, $tipo_accion, $id_afectado);
        $stmt->execute();
        $stmt->close();
        $con->close();
    }
    // AdminController.php

    public function getLogsJSON()
    {
        // Tu consulta a la tabla logs
        $con = DataBase::connect();
        // Ajusta el nombre de tu tabla y campos
        $sql = "SELECT id_log, tabla, tipo_accion, id_afectado, fecha
                FROM Logs
                ORDER BY id_log DESC"; // Por ejemplo, ordenado descendente

        $result = $con->query($sql);
        $logs = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $logs[] = [
                    'id_log'      => $row['id_log'],
                    'tabla'       => $row['tabla'],
                    'tipo_accion' => $row['tipo_accion'],
                    'id_afectado' => $row['id_afectado'],
                    // Ajusta el formateo de fecha/hora si lo necesitas
                    'fecha'       => $row['fecha']
                ];
            }
        }

        $con->close();

        header('Content-Type: application/json');
        echo json_encode($logs);
    }



}
