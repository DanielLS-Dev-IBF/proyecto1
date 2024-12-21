<?php
// models/UsuarioDAO.php

include_once("config/db.php");
include_once("Usuario.php");

class UsuarioDAO {
    /**
     * Obtener la conexión a la base de datos
     *
     * @return mysqli
     */
    public static function getConnection() {
        return DataBase::connect();
    }

    /**
     * Obtener un usuario por su ID
     *
     * @param int $id_usuario
     * @return Usuario|null
     */
    public static function obtenerUsuarioPorId($id_usuario) {
        $conn = self::getConnection();
        $sql = "SELECT * FROM Usuario WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return null;
        }

        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return null;
        }

        $result = $stmt->get_result();
        $usuario = null;
        if ($row = $result->fetch_assoc()) {
            $usuario = new Usuario();
            $usuario->setId_usuario($row['id_usuario']);
            $usuario->setNombre_completo($row['nombre_completo']);
            $usuario->setEmail($row['email']);
            $usuario->setPassword($row['password']);
            $usuario->setTelefono($row['telefono']);
            $usuario->setFecha_registro($row['fecha_registro']);
        }

        $stmt->close();
        $conn->close();
        return $usuario;
    }

    /**
     * Obtener un usuario por su email
     *
     * @param string $email
     * @return Usuario|null
     */
    public static function obtenerUsuarioPorEmail($email) {
        $conn = self::getConnection();
        $sql = "SELECT * FROM Usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return null;
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return null;
        }

        $result = $stmt->get_result();
        $usuario = null;
        if ($row = $result->fetch_assoc()) {
            $usuario = new Usuario();
            $usuario->setId_usuario($row['id_usuario']);
            $usuario->setNombre_completo($row['nombre_completo']);
            $usuario->setEmail($row['email']);
            $usuario->setPassword($row['password']);
            $usuario->setTelefono($row['telefono']);
            $usuario->setFecha_registro($row['fecha_registro']);
        }

        $stmt->close();
        $conn->close();
        return $usuario;
    }

    /**
     * Crear un nuevo usuario
     *
     * @param Usuario $usuario
     * @return int|false ID del usuario creado o false en caso de error
     */
    public static function newUser(Usuario $usuario) {
        $conn = self::getConnection();
        $sql = "INSERT INTO Usuario (nombre_completo, email, password, telefono, fecha_registro)
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return false;
        }

        $stmt->bind_param(
            "sssi",
            $usuario->getNombre_completo(),
            $usuario->getEmail(),
            $usuario->getPassword(),
            $usuario->getTelefono()
        );

        if ($stmt->execute()) {
            $id_usuario = $stmt->insert_id;
            $stmt->close();
            $conn->close();
            return $id_usuario;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    /**
     * Actualizar la información de un usuario
     *
     * @param Usuario $usuario
     * @return bool
     */
    public static function updateUser(Usuario $usuario) {
        $conn = self::getConnection();
        $sql = "UPDATE Usuario SET nombre_completo = ?, email = ?, telefono = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return false;
        }

        $stmt->bind_param(
            "sssi",
            $usuario->getNombre_completo(),
            $usuario->getEmail(),
            $usuario->getTelefono(),
            $usuario->getId_usuario()
        );

        $resultado = $stmt->execute();
        if (!$resultado) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
        return $resultado;
    }
}
?>
