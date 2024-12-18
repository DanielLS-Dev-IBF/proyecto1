<?php
// controllers/UsuarioController.php

include_once("models/UsuarioDAO.php");
include_once("models/Usuario.php");

class UsuarioController {
    public function index() {
        header('Location: index.php?controller=usuario&action=login');
        exit();
    }

    public function register() {
        // Variables por si quieres mostrarlas en la vista, aunque el JS ya las gestiona
        $nombre_completo = $email = $direccion = $codigo_postal = $telefono = '';
        $errores = [];

        $view = 'views/user/register.php';
        include_once 'views/main.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmar_password = $_POST['confirm_password'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
    
            // Datos de dirección opcional (sin ciudad)
            $direccion = trim($_POST['direccion'] ?? '');
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    
            $errores = [];
    
            // Validaciones usuario
            if (empty($nombre_completo)) {
                $errores[] = 'El nombre completo es obligatorio.';
            }
    
            if (empty($email)) {
                $errores[] = 'El correo electrónico es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no es válido.';
            }
    
            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria.';
            } elseif (strlen($password) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
            }
    
            if (empty($confirmar_password)) {
                $errores[] = 'Debes confirmar la contraseña.';
            } elseif ($password !== $confirmar_password) {
                $errores[] = 'Las contraseñas no coinciden.';
            }
    
            if (empty($telefono)) {
                $errores[] = 'El teléfono es obligatorio.';
            } elseif (!ctype_digit($telefono) || strlen($telefono) < 7 || strlen($telefono) > 15) {
                $errores[] = 'El teléfono no es válido. Debe tener entre 7 y 15 dígitos.';
            }
    
            // Validación opcional de dirección
            if (!empty($direccion) || !empty($codigo_postal)) {
                if (empty($direccion)) {
                    $errores[] = 'La dirección no puede estar vacía si va a registrar una dirección.';
                }
                if (empty($codigo_postal) || !ctype_digit($codigo_postal) || strlen($codigo_postal) !== 5) {
                    $errores[] = 'El código postal no es válido. Debe tener 5 dígitos.';
                }
            }
    
            // Verificar que el email no esté ya en uso
            if (empty($errores)) {
                $usuarioExistente = UsuarioDAO::obtenerUsuarioPorEmail($email);
                if ($usuarioExistente) {
                    $errores[] = 'El correo electrónico ya está en uso.';
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
                        include_once("models/DireccionDAO.php");
                        $dirObj = new Direccion();
                        $dirObj->setId_cliente($idUsuario);
                        $dirObj->setDireccion($direccion);
                        $dirObj->setCodigo_postal((int)$codigo_postal);
    
                        DireccionDAO::agregarDireccion($dirObj);
                    }
    
                    // Redirigir al login con mensaje de éxito
                    header('Location: index.php?controller=usuario&action=login&registro=success');
                    exit();
                } else {
                    $errores[] = 'Hubo un error al registrar el usuario. Por favor, intenta nuevamente.';
                }
            }
    
            // Si hay errores, volver a mostrar el formulario
            $view = 'views/user/register.php';
            include_once 'views/main.php';
        } else {
            $this->register();
        }
    }
    

    public function login() {
        $email = '';
        $errores = [];
        $registroExitoso = isset($_GET['registro']) && $_GET['registro'] === 'success';

        $view = 'views/user/login.php';
        include_once 'views/main.php';
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
            include_once("models/DireccionDAO.php");
            $direcciones = DireccionDAO::obtenerDireccionesPorUsuario($usuario->getId_usuario());
    
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
                        $_SESSION['email'] = $email;
                        $_SESSION['telefono'] = $telefono;
    
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
}
