<?php
session_start();

// Importación de archivos necesarios
foreach (glob("controllers/*Controller.php") as $file) {
    include_once $file;
}
include_once("config/parameters.php");

// Controlador y acción por defecto
$default_controller = 'productoController';
$default_action = 'index';

// Obtener el controlador de la URL o usar el predeterminado
if (!isset($_GET['controller'])){
    $nombre_controller = $default_controller;
}else{
    $nombre_controller = $_GET['controller']."Controller";
}

// Verificar si el controlador existe
if (class_exists($nombre_controller)){
    $controller = new $nombre_controller();

    // Obtener la acción o usar la predeterminada
    if (isset($_GET["action"]) && (method_exists($controller, $_GET["action"]))) {
        $action = $_GET["action"];
    }else{
        $action = $default_action;
    }

    // Ejecutar la acción
    $controller->$action();

}else{
    // Si no existe el controlador, mostrar error o redirigir
    echo "No existe el controller ". $nombre_controller;
    // header('Location: index.php?controller=error&action=index');
}
?>
