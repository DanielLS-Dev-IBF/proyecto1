<?php
// controllers/UsuarioController.php

include_once("models/UsuarioDAO.php");
include_once("models/Usuario.php");

class UsuarioController{
    /**
     * Acción por defecto. Redirige al formulario de inicio de sesión.
     */
    public function index(){
        header('Location: index.php?controller=usuario&action=login');
        exit();
    }

    /**
     * Mostrar el formulario de registro.
     */
    public function register(){
        // Inicializar variables para evitar errores de variables indefinidas en la vista
        $nombre_completo = $email = $direccion = $codigo_postal = $telefono = '';
        $errores = [];

        // Incluir la vista de registro dentro del layout principal
        $view = 'views/user/register.php';
        include_once 'views/main.php';
    }

    /**
     * Procesar y almacenar los datos de registro.
     */
    public function store(){
        // Verificar si se ha enviado el formulario de registro
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y sanitizar los datos del formulario
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmar_password = $_POST['confirmar_password'] ?? '';
            $direccion = trim($_POST['direccion'] ?? '');
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');
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

            if (empty($direccion)) {
                $errores[] = 'La dirección es obligatoria.';
            }

            if (empty($codigo_postal)) {
                $errores[] = 'El código postal es obligatorio.';
            } elseif (!ctype_digit($codigo_postal) || strlen($codigo_postal) < 4 || strlen($codigo_postal) > 5) {
                $errores[] = 'El código postal no es válido.';
            }

            if (empty($telefono)) {
                $errores[] = 'El teléfono es obligatorio.';
            } elseif (!ctype_digit($telefono) || strlen($telefono) < 7 || strlen($telefono) > 15) {
                $errores[] = 'El teléfono no es válido.';
            }

            // Verificar si el email ya está registrado
            if (empty($errores)) {
                $usuarioExistente = UsuarioDAO::obtenerUsuarioPorEmail($email);
                if ($usuarioExistente) {
                    $errores[] = 'El correo electrónico ya está en uso.';
                }
            }

            if (empty($errores)) {
                // Crear un objeto Usuario y asignar los valores
                $usuario = new Usuario();
                $usuario->setNombre_completo($nombre_completo);
                $usuario->setEmail($email);
                $usuario->setPassword(password_hash($password, PASSWORD_DEFAULT)); // Hash de la contraseña
                $usuario->setDireccion($direccion);
                $usuario->setCodigo_postal((int)$codigo_postal);
                $usuario->setTelefono((int)$telefono);
                // La fecha de registro se establece automáticamente en la base de datos

                // Insertar el usuario usando el DAO
                $resultado = UsuarioDAO::newUser($usuario);

                if ($resultado) {
                    // Registro exitoso, redirigir al formulario de inicio de sesión con un mensaje de éxito
                    header('Location: index.php?controller=usuario&action=login&registro=success');
                    exit();
                } else {
                    // Manejar el error (puedes mejorar esto agregando mensajes más específicos)
                    $errores[] = 'Hubo un error al registrar el usuario. Por favor, intenta nuevamente.';
                }
            }

            // Si hay errores, mostrar el formulario de registro nuevamente con los mensajes de error
            $view = 'views/user/register.php';
            include_once 'views/main.php';
        } else {
            // Si no es una solicitud POST, mostrar el formulario de registro
            $this->register();
        }
    }

    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function login(){
        // Inicializar variables para evitar errores de variables indefinidas en la vista
        $email = '';
        $errores = [];

        // Verificar si se ha registrado exitosamente
        $registroExitoso = isset($_GET['registro']) && $_GET['registro'] === 'success';

        // Incluir la vista de inicio de sesión dentro del layout principal
        $view = 'views/user/login.php';
        include_once 'views/main.php';
    }

    /**
     * Procesar y verificar las credenciales de inicio de sesión.
     */
    public function authenticate(){
        // Verificar si se ha enviado el formulario de inicio de sesión
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y sanitizar los datos del formulario
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $errores = [];

            // Validaciones
            if (empty($email)) {
                $errores[] = 'El correo electrónico es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no es válido.';
            }

            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria.';
            }

            if (empty($errores)) {
                // Obtener el usuario por email
                $usuario = UsuarioDAO::obtenerUsuarioPorEmail($email);

                if ($usuario && password_verify($password, $usuario->getPassword())) {
                    // Contraseña correcta, iniciar sesión
                    session_start();
                    session_regenerate_id(true); // Prevenir ataques de fijación de sesión

                    $_SESSION['id_usuario'] = $usuario->getId_usuario();
                    $_SESSION['nombre_completo'] = $usuario->getNombre_completo();

                    // Redirigir al usuario a la página principal o dashboard
                    header('Location: index.php?controller=dashboard&action=index');
                    exit();
                } else {
                    $errores[] = 'Correo electrónico o contraseña incorrectos.';
                }
            }

            // Si hay errores, mostrar el formulario de inicio de sesión nuevamente con los mensajes de error
            $view = 'views/user/login.php';
            include_once 'views/main.php';
        } else {
            // Si no es una solicitud POST, mostrar el formulario de inicio de sesión
            $this->login();
        }
    }

    /**
     * Manejar el cierre de sesión del usuario.
     */
    public function logout(){
        session_start();
        session_unset();
        session_destroy();

        // Redirigir al usuario al formulario de inicio de sesión
        header('Location: index.php?controller=usuario&action=login');
        exit();
    }

    /**
     * Mostrar el perfil del usuario (opcional).
     */
    public function show(){
        session_start();
        if (!isset($_SESSION['id_usuario'])) {
            // Si el usuario no está autenticado, redirigir al inicio de sesión
            header('Location: index.php?controller=usuario&action=login');
            exit();
        }

        // Obtener el usuario desde la base de datos
        $usuario = UsuarioDAO::obtenerUsuarioPorId($_SESSION['id_usuario']);

        if ($usuario) {
            $view = 'views/user/profile.php';
            include_once 'views/main.php';
        } else {
            // Usuario no encontrado
            http_response_code(404);
            echo "Usuario no encontrado.";
        }
    }
}
?>
