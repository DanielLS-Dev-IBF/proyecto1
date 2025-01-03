<?php
// models/DireccionDAO.php

class DireccionDAO {
    /**
     * Obtener todas las direcciones de un usuario
     *
     * @param int $id_cliente
     * @return array
     */
    public static function getDireccionesByUsuario($id_cliente) {
        $con = DataBase::connect();
        $sql = "SELECT * FROM Direccion WHERE id_cliente = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return [];
        }

        $stmt->bind_param("i", $id_cliente);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
            return [];
        }

        $result = $stmt->get_result();
        $direcciones = [];
        while ($row = $result->fetch_assoc()) {
            $direccion = new Direccion(
                $row['id_direccion'],
                $row['id_cliente'],
                $row['direccion'],
                $row['codigo_postal']
            );
            $direcciones[] = $direccion;
        }

        $stmt->close();
        $con->close();
        return $direcciones;
    }

    /**
     * Agregar una nueva dirección para un usuario
     *
     * @param Direccion $direccion
     * @return bool
     */
    public static function agregarDireccion($direccion) {
        $con = DataBase::connect();
        $sql = "INSERT INTO Direccion (id_cliente, direccion, codigo_postal) VALUES (?, ?, ?)";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return false;
        }

        $id_cliente = $direccion->getIdUsuario();
        $direccion_text = $direccion->getDireccion();
        $codigo_postal = $direccion->getCodigoPostal();

        $stmt->bind_param("isi", $id_cliente, $direccion_text, $codigo_postal);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
            return false;
        }

        $stmt->close();
        $con->close();
        return true;
    }

    /**
     * Eliminar una dirección por su ID
     *
     * @param int $id_direccion
     * @return bool
     */
    public static function eliminarDireccion($id_direccion) {
        $con = DataBase::connect();
        $sql = "DELETE FROM Direccion WHERE id_direccion = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return false;
        }

        $stmt->bind_param("i", $id_direccion);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
            return false;
        }

        $stmt->close();
        $con->close();
        return true;
    }
}
?>
