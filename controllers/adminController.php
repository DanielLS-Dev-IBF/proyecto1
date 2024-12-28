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
                'tipo'        => $prod->getTipo()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function createUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre_completo'] ?? '';
            $email  = $_POST['email'] ?? '';
            $rol    = $_POST['rol'] ?? 'usuario';
            $telefono = $_POST['telefono'] ?? '';

            // Validar
            if (empty($nombre) || empty($email)) {
                echo json_encode(['status'=>'error','message'=>'Faltan campos']);
                return;
            }

            // Crear objeto Usuario
            $u = new Usuario();
            $u->setNombre_completo($nombre);
            $u->setEmail($email);
            $u->setTelefono($telefono);
            $u->setRol($rol);

            // Llamar DAO
            $idNuevo = UsuarioDAO::newUser($u);
            if ($idNuevo) {
                echo json_encode(['status'=>'ok','message'=>'Usuario creado']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Error al crear usuario']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    public function updateUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? null;
            $nombre = $_POST['nombre_completo'] ?? '';
            $email  = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

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
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio_base = $_POST['precio_base'] ?? 0;
            $tipo = $_POST['tipo'] ?? '';

            $p = new Producto(
                null, // id_producto autoincrement
                $nombre,
                $descripcion,
                $precio_base,
                $_POST['img'] ?? '',
                $tipo
            );

            $ok = ProductoDAO::store($p);
            if ($ok !== false) {
                echo json_encode(['status'=>'ok','message'=>'Producto creado']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Error al crear producto']);
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
        }
    }

    public static function updateProducto(Producto $producto): bool
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE Proyecto1.Producto
            SET nombre = ?, descripcion = ?, precio_base = ?, img = ?, tipo = ?
            WHERE id_producto = ?");

        if (!$stmt) {
            error_log("Prepare failed in updateProducto: (" . $con->errno . ") " . $con->error);
            return false;
        }

        $nombre       = $producto->getNombre();
        $descripcion  = $producto->getDescripcion();
        $precio_base  = $producto->getPrecio_base();
        $img          = $producto->getImg();
        $tipo         = $producto->getTipo();
        $id_producto  = $producto->getId_producto();

        $stmt->bind_param("ssdssi", 
            $nombre,
            $descripcion,
            $precio_base,
            $img,
            $tipo,
            $id_producto
        );

        $res = $stmt->execute();
        if (!$res) {
            error_log("Execute failed in updateProducto: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
        $con->close();
        return $res; 
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