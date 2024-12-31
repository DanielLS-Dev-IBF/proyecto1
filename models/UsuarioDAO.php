<?php
// models/UsuarioDAO.php

include_once("config/db.php");
include_once("Usuario.php");

class UsuarioDAO {
    
    /**
     * Obtiene todos los usuarios de la base de datos.
     *
     * @return array Array de objetos Usuario.
     */
    public static function getAllUsers() {
        $con = DataBase::connect();
        $sql = "SELECT * FROM Usuario";
        $result = $con->query($sql);
    
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $u = new Usuario();
            $u->setId_usuario($row['id_usuario']);
            $u->setNombre_completo($row['nombre_completo']);
            $u->setEmail($row['email']);
            $u->setPassword($row['password']);
            $u->setTelefono($row['telefono']);
            $u->setFecha_registro($row['fecha_registro']);
            $u->setRol($row['rol']);
            $usuarios[] = $u;
        }
    
        $con->close();
        return $usuarios;
    }
    

    /**
     * Obtener un usuario por su ID
     *
     * @param int $id_usuario
     * @return Usuario|null
     */
    public static function obtenerUsuarioPorId($id_usuario) {
        $con = DataBase::connect();

        $sql = "SELECT * FROM Usuario WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return null;
        }

        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
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
            $usuario->setRol($row['rol']);
        }

        $stmt->close();
        $con->close();
        return $usuario;
    }

    /**
     * Obtener un usuario por su email
     *
     * @param string $email
     * @return Usuario|null
     */
    public static function obtenerUsuarioPorEmail($email) {
        $con = DataBase::connect();

        $sql = "SELECT * FROM Usuario WHERE email = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return null;
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
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
            $usuario->setRol($row['rol']);
        }
        

        $stmt->close();
        $con->close();
        return $usuario;
    }

    /**
     * Crear un nuevo usuario
     *
     * @param Usuario $usuario
     * @return int|false ID del usuario creado o false en caso de error
     */
    public static function newUser(Usuario $usuario) {
        $con = DataBase::connect();

        $sql = "INSERT INTO Usuario (nombre_completo, email, password, telefono, fecha_registro, rol)
                VALUES (?, ?, ?, ?, NOW(), ?)";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return false;
        }
    
        $nombre_completo = $usuario->getNombre_completo();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword();
        $telefono = $usuario->getTelefono();
        $rol = $usuario->getRol() ?: 'usuario';
    
        // Corregir la cadena de tipos a "sssss" ya que todos los campos son strings
        $stmt->bind_param(
            "sssss",
            $nombre_completo,
            $email,
            $password,
            $telefono,
            $rol
        );
    
        if ($stmt->execute()) {
            $id_usuario = $stmt->insert_id;
            $stmt->close();
            $con->close();
            return $id_usuario;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
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
        $con = DataBase::connect();

        // Si la contraseña está establecida, actualizarla también
        if (!empty($usuario->getPassword())) {
            $sql = "UPDATE Usuario SET nombre_completo = ?, email = ?, telefono = ?, password = ? WHERE id_usuario = ?";
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $con->error);
                return false;
            }

            $nombre_completo = $usuario->getNombre_completo();
            $email = $usuario->getEmail();
            $telefono = $usuario->getTelefono();
            $password = $usuario->getPassword();
            $id_usuario = $usuario->getId_usuario();

            $stmt->bind_param(
                "ssssi",
                $nombre_completo,
                $email,
                $telefono,
                $password,
                $id_usuario
            );
        } else {
            // Si no se actualiza la contraseña
            $sql = "UPDATE Usuario SET nombre_completo = ?, email = ?, telefono = ? WHERE id_usuario = ?";
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                error_log("Error preparando la consulta: " . $con->error);
                return false;
            }

            $nombre_completo = $usuario->getNombre_completo();
            $email = $usuario->getEmail();
            $telefono = $usuario->getTelefono();
            $id_usuario = $usuario->getId_usuario();

            $stmt->bind_param(
                "sssi",
                $nombre_completo,
                $email,
                $telefono,
                $id_usuario
            );
        }

        $resultado = $stmt->execute();
        if (!$resultado) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
        }

        $stmt->close();
        $con->close();
        return $resultado;
    }

    /**
     * Eliminar un usuario por su ID
     *
     * @param int $id
     * @return bool
     */
    public static function deleteUser($id)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
        if (!$stmt) {
            error_log("Error preparando stmt: " . $con->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        if (!$res) {
            error_log("Error ejecutando stmt en deleteUser: " . $stmt->error);
        }
        $stmt->close();
        $con->close();
        return $res;
    }

    
    /**
     * Verificar si un email ya está registrado
     *
     * @param string $email
     * @return bool
     */
    public static function emailExists($email)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM Usuario WHERE email = ?");

        if (!$stmt) {
            error_log("Prepare failed in emailExists: (" . $con->errno . ") " . $con->error);
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        $stmt->close();
        $con->close();

        return $count > 0;
    }
    
}
?>
