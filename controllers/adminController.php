<?php
// controllers/AdminController.php

include_once 'models/UsuarioDAO.php';
include_once 'models/PedidoDAO.php';
include_once 'models/ProductoDAO.php';

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? null;
            $direccion  = $_POST['direccion'] ?? '';
            $telefono   = $_POST['telefono'] ?? '';
            $correo     = $_POST['correo'] ?? '';

            $pedido = new Pedido(
                $id_usuario,
                $_POST['nombre_completo'] ?? '',
                $direccion, $telefono, $correo, 
                $_POST['metodo_pago'] ?? '', $_POST['detalles_pago'] ?? '',
                $_POST['subtotal'] ?? 0, $_POST['descuento'] ?? 0, 
                $_POST['gastos_envio'] ?? 0, $_POST['total'] ?? 0
            );
            
            $res = PedidoDAO::crearPedido($pedido);
            if ($res) {
                echo json_encode(['status'=>'ok','message'=>'Pedido creado']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Error al crear pedido']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    public function updatePedido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pedido = $_POST['id_pedido'] ?? null;
            if (!$id_pedido) {
                echo json_encode(['status'=>'error','message'=>'ID de pedido no válido']);
                return;
            }

            $pedido = new Pedido(
                $_POST['id_usuario'] ?? 0,
                $_POST['nombre_completo'] ?? '',
                $_POST['direccion'] ?? '',
                $_POST['telefono'] ?? '',
                $_POST['correo'] ?? '',
                $_POST['metodo_pago'] ?? '',
                $_POST['detalles_pago'] ?? '',
                $_POST['subtotal'] ?? 0,
                $_POST['descuento'] ?? 0,
                $_POST['gastos_envio'] ?? 0,
                $_POST['total'] ?? 0
            );
            $pedido->setIdPedido($id_pedido);

            $ok = PedidoDAO::updatePedido($pedido);
            if ($ok) {
                echo json_encode(['status'=>'ok','message'=>'Pedido actualizado']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Error al actualizar pedido']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    public function deletePedido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_pedido'] ?? null;
            if ($id) {
                $ok = PedidoDAO::deletePedido($id);
                if ($ok) {
                    echo json_encode(['status'=>'ok','message'=>'Pedido eliminado']);
                } else {
                    echo json_encode(['status'=>'error','message'=>'Error al eliminar pedido']);
                }
            } else {
                echo json_encode(['status'=>'error','message'=>'ID pedido no válido']);
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

}
