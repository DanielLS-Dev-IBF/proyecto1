<?php
// controllers/UsuarioController.php

include_once("models/UsuarioDAO.php");
include_once("models/Usuario.php");
include_once("models/PedidoDAO.php");
include_once("models/DireccionDAO.php");

class UsuarioController {
    public function index() {
        header('Location: index.php?controller=usuario&action=login');
        exit();
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start(); // Iniciar la sesión para manejar errores y datos

            // Obtener y limpiar los datos del formulario
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmar_password = $_POST['confirm_password'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');

            $errores = [];

            // Validaciones usuario
            if (empty($nombre_completo)) {
                $errores['nombre_completo'] = 'El nombre completo es obligatorio.';
            }

            if (empty($email)) {
                $errores['email'] = 'El correo electrónico es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El correo electrónico no es válido.';
            }

            if (empty($password)) {
                $errores['password'] = 'La contraseña es obligatoria.';
            } elseif (strlen($password) < 6) {
                $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
            }

            if (empty($confirmar_password)) {
                $errores['confirm_password'] = 'Debes confirmar la contraseña.';
            } elseif ($password !== $confirmar_password) {
                $errores['confirm_password'] = 'Las contraseñas no coinciden.';
            }

            if (empty($telefono)) {
                $errores['telefono'] = 'El teléfono es obligatorio.';
            } elseif (!ctype_digit($telefono) || strlen($telefono) < 7 || strlen($telefono) > 15) {
                $errores['telefono'] = 'El teléfono no es válido. Debe tener entre 7 y 15 dígitos.';
            }

            // Validación opcional de dirección
            if (!empty($direccion) || !empty($codigo_postal)) {
                if (empty($direccion)) {
                    $errores['direccion'] = 'La dirección no puede estar vacía si vas a registrar una dirección.';
                }
                if (empty($codigo_postal) || !ctype_digit($codigo_postal) || strlen($codigo_postal) !== 5) {
                    $errores['codigo_postal'] = 'El código postal no es válido. Debe tener 5 dígitos.';
                }
            }

            // Verificar que el email no esté ya en uso
            if (empty($errores)) {
                $usuarioExistente = UsuarioDAO::obtenerUsuarioPorEmail($email);
                if ($usuarioExistente) {
                    $errores['email'] = 'El correo electrónico ya está en uso.';
                }
            }

            if (empty($errores)) {
                // Crear usuario
                $usuario = new Usuario();
                $usuario->setNombre_completo($nombre_completo);
                $usuario->setEmail($email);
                $usuario->setPassword(password_hash($password, PASSWORD_DEFAULT));
                $usuario->setTelefono((int)$telefono);

                $idUsuario = UsuarioDAO::newUser($usuario);

                if ($idUsuario) {
                    // Si el usuario ingresó una dirección, la guardamos
                    if (!empty($direccion) && !empty($codigo_postal)) {
                        $dirObj = new Direccion(null, $idUsuario, $direccion, (int)$codigo_postal);
                        DireccionDAO::agregarDireccion($dirObj);
                    }

                    // Redirigir al login con mensaje de éxito
                    $_SESSION['success'] = 'Registro exitoso. Puedes iniciar sesión ahora.';
                    header('Location: index.php?controller=usuario&action=login&registro=success');
                    exit();
                } else {
                    $errores['general'] = 'Hubo un error al registrar el usuario. Por favor, intenta nuevamente.';
                }
            }

            // Si hay errores, almacenarlos en la sesión y redirigir al formulario
            if (!empty($errores)) {
                $_SESSION['errors'] = $errores;
                $_SESSION['old'] = [
                    'nombre_completo' => $nombre_completo,
                    'email' => $email,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'codigo_postal' => $codigo_postal
                ];
                header('Location: index.php?controller=usuario&action=register');
                exit();
            }

            // Incluir la vista del formulario en caso de método no POST
            $this->register();
        }
    }

    public function register() {
        // Obtener errores y datos antiguos si existen
        $errores = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        // Limpiar las variables de sesión
        unset($_SESSION['errors'], $_SESSION['old']);

        $view = 'views/user/register.php';
        include_once 'views/main.php';
    }

    public function login() {
        $email = '';
        $errores = [];
        $registroExitoso = isset($_GET['registro']) && $_GET['registro'] === 'success';

        $view = 'views/user/login.php';
        include_once 'views/main.php';
    }

    /**
     * Acción para verificar si un correo electrónico ya está en uso.
     * Esta acción está diseñada para ser llamada vía AJAX.
     */
    public function checkEmail() {
        // Solo permitir solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Método No Permitido
            echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
            exit();
        }

        // Verificar si es una solicitud AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(400); // Solicitud Incorrecta
            echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
            exit();
        }

        // Obtener y sanitizar el correo electrónico del POST
        $email = trim($_POST['email'] ?? '');

        // Validar que el correo electrónico no esté vacío
        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'El correo electrónico está vacío.']);
            exit();
        }

        // Validar el formato del correo electrónico
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Formato de correo electrónico inválido.']);
            exit();
        }

        // Verificar si el correo electrónico ya está en uso
        $usuarioExistente = UsuarioDAO::obtenerUsuarioPorEmail($email);

        if ($usuarioExistente) {
            echo json_encode(['status' => 'exists', 'message' => 'El correo electrónico ya está en uso.']);
        } else {
            echo json_encode(['status' => 'available', 'message' => 'El correo electrónico está disponible.']);
        }

        exit();
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $errores = [];

            if (empty($email)) {
                $errores[] = 'El correo electrónico es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no es válido.';
            }

            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria.';
            }

            if (empty($errores)) {
                $usuario = UsuarioDAO::obtenerUsuarioPorEmail($email);

                if ($usuario && password_verify($password, $usuario->getPassword())) {
                    // Credenciales correctas, iniciar sesión
                    session_start();
                    session_regenerate_id(true);
                    $_SESSION['id_usuario'] = $usuario->getId_usuario();
                    $_SESSION['nombre_completo'] = $usuario->getNombre_completo();

                    // Redirigir a una página principal o dashboard
                    header('Location: index.php?controller=Producto&action=index');
                    exit();
                } else {
                    $errores[] = 'Correo electrónico o contraseña incorrectos.';
                }
            }

            $view = 'views/user/login.php';
            include_once 'views/main.php';
        } else {
            $this->login();
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: index.php?controller=Producto&action=index');
        exit();
    }

    public function show() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }
    
        $usuario = UsuarioDAO::obtenerUsuarioPorId($_SESSION['id_usuario']);
    
        if ($usuario) {
            // Obtener direcciones del usuario
            $direcciones = DireccionDAO::getDireccionesByUsuario($usuario->getId_usuario());
    
            // Incluir la vista y pasar los datos
            $view = 'views/user/profile.php';
            include_once 'views/main.php';
        } else {
            http_response_code(404);
            echo "Usuario no encontrado.";
        }
    }

    public function pedidos() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = "Debes iniciar sesión para ver tus pedidos.";
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }

        $usuario_id = $_SESSION['id_usuario'];

        // Obtener la información del usuario
        $usuario = UsuarioDAO::obtenerUsuarioPorId($usuario_id);

        if ($usuario) {
            // Obtener direcciones del usuario
            $direcciones = DireccionDAO::getDireccionesByUsuario($usuario_id);

            // Implementación de Paginación
            // Parámetros de Paginación
            $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 2; // Pedidos por página
            $offset = ($currentPage - 1) * $limit;

            // Obtener el total de pedidos
            $totalPedidos = PedidoDAO::contarPedidosPorUsuario($usuario_id);
            $totalPaginas = ceil($totalPedidos / $limit);
            if ($totalPaginas < 1) $totalPaginas = 1;

            // Asegurarse de que la página actual está dentro de los límites
            if ($currentPage > $totalPaginas) {
                $currentPage = $totalPaginas;
                $offset = ($currentPage - 1) * $limit;
            }
            if ($currentPage < 1) {
                $currentPage = 1;
                $offset = 0;
            }

            // Obtener los pedidos paginados
            $pedidos = PedidoDAO::obtenerPedidosPorUsuarioPaginados($usuario_id, $limit, $offset);

            // Pasar variables a la vista
            $view = 'views/user/profile.php';
            include_once 'views/main.php';
        } else {
            http_response_code(404);
            echo "Usuario no encontrado.";
        }
    }

    public function update() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
    
            $errores = [];
    
            // Validaciones
            if (empty($nombre_completo)) {
                $errores[] = 'El nombre completo es obligatorio.';
            }
    
            if (empty($email)) {
                $errores[] = 'El correo electrónico es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no es válido.';
            }
    
            if (empty($telefono)) {
                $errores[] = 'El teléfono es obligatorio.';
            } elseif (!ctype_digit($telefono) || strlen($telefono) < 7 || strlen($telefono) > 15) {
                $errores[] = 'El teléfono no es válido. Debe tener entre 7 y 15 dígitos.';
            }
    
            // Comprobar que el email no esté en uso por otro usuario
            if (empty($errores)) {
                $usuarioExistente = UsuarioDAO::obtenerUsuarioPorEmail($email);
                if ($usuarioExistente && $usuarioExistente->getId_usuario() !== $_SESSION['id_usuario']) {
                    $errores[] = 'El correo electrónico ya está en uso por otro usuario.';
                }
            }
    
            if (empty($errores)) {
                // Actualizar usuario
                $usuario = UsuarioDAO::obtenerUsuarioPorId($_SESSION['id_usuario']);
                if ($usuario) {
                    $usuario->setNombre_completo($nombre_completo);
                    $usuario->setEmail($email);
                    $usuario->setTelefono($telefono);
    
                    $resultado = UsuarioDAO::updateUser($usuario);
                    if ($resultado) {
                        // Actualizar variables de sesión
                        $_SESSION['nombre_completo'] = $nombre_completo;
    
                        // Establecer mensaje de éxito en la sesión
                        $_SESSION['success'] = 'Perfil actualizado exitosamente.';
    
                        // Redirigir al perfil
                        header('Location: index.php?controller=usuario&action=show');
                        exit();
                    } else {
                        $errores[] = 'Hubo un error al actualizar el usuario. Por favor, intenta nuevamente.';
                    }
                } else {
                    $errores[] = 'Usuario no encontrado.';
                }
            }
    
            // Si hay errores, establecerlos en la sesión y redirigir al perfil
            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                header('Location: index.php?controller=usuario&action=show');
                exit();
            }
        } else {
            header('Location: index.php?controller=usuario&action=show');
            exit();
        }
    }
    /**
     * Acción para agregar una nueva dirección
     */
    public function addDireccion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $direccion = trim($_POST['direccion'] ?? '');
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');

            $errores = [];

            // Validaciones
            if (empty($direccion)) {
                $errores[] = 'La dirección es obligatoria.';
            }

            if (empty($codigo_postal)) {
                $errores[] = 'El código postal es obligatorio.';
            } elseif (!ctype_digit($codigo_postal) || strlen($codigo_postal) !== 5) {
                $errores[] = 'El código postal no es válido. Debe tener 5 dígitos.';
            }

            if (empty($errores)) {
                $direccionObj = new Direccion(null, $_SESSION['id_usuario'], $direccion, $codigo_postal);
                $resultado = DireccionDAO::agregarDireccion($direccionObj);
                if ($resultado) {
                    $_SESSION['success'] = 'Dirección agregada exitosamente.';
                } else {
                    $_SESSION['error'] = 'Hubo un error al agregar la dirección. Por favor, intenta nuevamente.';
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errores);
            }

            header('Location: index.php?controller=usuario&action=show');
            exit();
        } else {
            header('Location: index.php?controller=usuario&action=show');
            exit();
        }
    }

    /**
     * Acción para eliminar una dirección
     */
    public function deleteDireccion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_direccion = intval($_POST['id_direccion'] ?? 0);

            if ($id_direccion > 0) {
                // Verificar que la dirección pertenece al usuario
                $direcciones = DireccionDAO::getDireccionesByUsuario($_SESSION['id_usuario']);
                $direccionValida = false;
                foreach ($direcciones as $dir) {
                    if ($dir->getIdDireccion() == $id_direccion) {
                        $direccionValida = true;
                        break;
                    }
                }

                if ($direccionValida) {
                    $resultado = DireccionDAO::eliminarDireccion($id_direccion);
                    if ($resultado) {
                        $_SESSION['success'] = 'Dirección eliminada exitosamente.';
                    } else {
                        $_SESSION['error'] = 'Hubo un error al eliminar la dirección. Por favor, intenta nuevamente.';
                    }
                } else {
                    $_SESSION['error'] = 'Dirección no encontrada o no autorizada.';
                }
            } else {
                $_SESSION['error'] = 'ID de dirección inválido.';
            }

            header('Location: index.php?controller=usuario&action=show');
            exit();
        } else {
            header('Location: index.php?controller=usuario&action=show');
            exit();
        }
    }
    
}
?>
