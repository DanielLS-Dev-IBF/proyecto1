<?php
// models/UsuarioDAO.php

include_once("Usuario.php");
include_once("config/db.php"); // Debe contener la clase DataBase con el método connect()

class UsuarioDAO {

    public static function newUser(Usuario $usuario) {
        $con = DataBase::connect();
    
        // Ahora la consulta no incluye direccion ni codigo_postal
        $stmt = $con->prepare("INSERT INTO Proyecto1.Usuario (nombre_completo, email, password, telefono, fecha_registro) VALUES (?, ?, ?, ?, CURDATE())");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return false;
        }
    
        $nombre_completo = $usuario->getNombre_completo();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword();
        $telefono = $usuario->getTelefono();
    
        if (!$stmt->bind_param("sssi", $nombre_completo, $email, $password, $telefono)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return false;
        }
    
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
    
        $stmt->close();
        $lastId = $con->insert_id; // Obtenemos el id del usuario recién creado
        $con->close();
    
        return $lastId; // Retornamos el ID para usarlo si queremos insertar la dirección después
    }

    public static function updateUser(Usuario $usuario) {
        $con = DataBase::connect();
    
        $stmt = $con->prepare("UPDATE Proyecto1.Usuario SET nombre_completo = ?, email = ?, telefono = ? WHERE id_usuario = ?");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return false;
        }
    
        $nombre_completo = $usuario->getNombre_completo();
        $email = $usuario->getEmail();
        $telefono = $usuario->getTelefono();
        $id_usuario = $usuario->getId_usuario();
    
        // Cambiar el tipo de 'telefono' a cadena
        if (!$stmt->bind_param("sssi", $nombre_completo, $email, $telefono, $id_usuario)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return false;
        }
    
        $resultado = $stmt->execute();
        $stmt->close();
        $con->close();
    
        return $resultado;
    }

    public static function obtenerUsuarioPorEmail($email) {
        $con = DataBase::connect();

        $stmt = $con->prepare("SELECT * FROM Proyecto1.Usuario WHERE email = ? LIMIT 1");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return null;
        }

        if (!$stmt->bind_param("s", $email)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return null;
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return null;
        }

        $result = $stmt->get_result();
        $usuario = null;

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $usuario = self::mapearUsuario($data);
        }

        $stmt->close();
        $con->close();

        return $usuario;
    }

    public static function obtenerUsuarioPorId($id_usuario) {
        $con = DataBase::connect();

        $stmt = $con->prepare("SELECT * FROM Proyecto1.Usuario WHERE id_usuario = ? LIMIT 1");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return null;
        }

        if (!$stmt->bind_param("i", $id_usuario)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return null;
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return null;
        }

        $result = $stmt->get_result();
        $usuario = null;

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $usuario = self::mapearUsuario($data);
        }

        $stmt->close();
        $con->close();

        return $usuario;
    }

    private static function mapearUsuario($data) {
        $usuario = new Usuario();
        $usuario->setId_usuario($data['id_usuario']);
        $usuario->setNombre_completo($data['nombre_completo']);
        $usuario->setEmail($data['email']);
        $usuario->setPassword($data['password']);
        $usuario->setTelefono($data['telefono']);
        $usuario->setFecha_registro($data['fecha_registro']);
        return $usuario;
    }
}
