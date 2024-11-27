<?php
//Controlador frontal PHP
//Tiene la función de redirigir las peticiones a diferentes controladores y acciones

//Importación de archivos necesarios para el funcionamiento del codigo, incluye la lógica y las configuraciones pertinentes
include_once("controllers/productoController.php");
include_once("config/parameters.php");

//Verificación de parametro controller con valor predeterminado
if (!isset($_GET['controller'])){
    header("Location:" . $url . "?controller=Producto");

}else{
    //Se obtiene el nombre del controlador al concatenar
    //Si la clase existe crea una instancia de ella
    $nombre_controller = $_GET['controller']."Controller";
    if (class_exists($nombre_controller)){
        $controller = new $nombre_controller();
        
        //Verifición y ejecución de la acción
        if (isset($_GET["action"]) && (method_exists($controller, $_GET["action"]))) {
            $action = $_GET["action"];
        }else{
            $action = default_action;
        }

        $controller ->$action();

    }else{
        //Si no existe el controlador
        echo "No existe el controller ". $nombre_controller;
    }
}