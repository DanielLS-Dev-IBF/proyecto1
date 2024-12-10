<?php
// models/UsuarioDAO.php

include_once("Usuario.php");
include_once("config/db.php"); // Asegúrate de que este archivo define la clase DataBase con el método connect()

class UsuarioDAO {

    public static function newUser(Usuario $usuario) {
        $con = DataBase::connect();

        // Preparar la sentencia SQL
        $stmt = $con->prepare("INSERT INTO Proyecto1.Usuario (nombre_completo, email, password, direccion, codigo_postal, telefono, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
        if (!$stmt) {
            // Manejar error de preparación
            error_log("Error al preparar la consulta: " . $con->error);
            return false;
        }

        // Obtener los datos del usuario
        $nombre_completo = $usuario->getNombre_completo();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword(); // Asegúrate de que ya está hasheada
        $direccion = $usuario->getDireccion();
        $codigo_postal = $usuario->getCodigo_postal();
        $telefono = $usuario->getTelefono();

        // Bind de parámetros: s = string, i = integer
        if (!$stmt->bind_param("ssssii", $nombre_completo, $email, $password, $direccion, $codigo_postal, $telefono)) {
            // Manejar error de binding
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return false;
        }

        // Ejecutar la sentencia
        if (!$stmt->execute()) {
            // Manejar error de ejecución
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }

        // Cerrar la sentencia y la conexión
        $stmt->close();
        $con->close();

        return true;
    }


    public static function obtenerUsuarioPorEmail($email) {
        $con = DataBase::connect();

        // Preparar la sentencia SQL
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Usuario WHERE email = ? LIMIT 1");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return null;
        }

        // Bind de parámetros
        if (!$stmt->bind_param("s", $email)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return null;
        }

        // Ejecutar la sentencia
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return null;
        }

        // Obtener el resultado
        $result = $stmt->get_result();
        $usuario = null;

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $usuario = self::mapearUsuario($data);
        }

        // Cerrar la sentencia y la conexión
        $stmt->close();
        $con->close();

        return $usuario;
    }

    public static function obtenerUsuarioPorId($id_usuario) {
        $con = DataBase::connect();

        // Preparar la sentencia SQL
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Usuario WHERE id_usuario = ? LIMIT 1");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return null;
        }

        // Bind de parámetros
        if (!$stmt->bind_param("i", $id_usuario)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return null;
        }

        // Ejecutar la sentencia
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return null;
        }

        // Obtener el resultado
        $result = $stmt->get_result();
        $usuario = null;

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $usuario = self::mapearUsuario($data);
        }

        // Cerrar la sentencia y la conexión
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
        $usuario->setDireccion($data['direccion']);
        $usuario->setCodigo_postal($data['codigo_postal']);
        $usuario->setTelefono($data['telefono']);
        $usuario->setFecha_registro($data['fecha_registro']);
        return $usuario;
    }

    // Métodos adicionales como actualizar o eliminar usuarios pueden agregarse aquí.
}
?>
