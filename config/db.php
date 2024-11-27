<?php

// Creación de la clase DataBase, encargada de la base de datos
class DataBase{
    //Función que realiza la connexión con la base de datos
    public static function connect($host = 'localhost', $user = 'root', $pass = 'Asdqwe!23', $db = 'Proyecto1', $sport = '3307'){
        $con = new mysqli($host, $user, $pass, $db, port: $sport);
        if ($con === false) {
            die("ERROR!!: NO te puedes conectar. " . mysqli_connect_error());
        }
        return $con;
    }
}