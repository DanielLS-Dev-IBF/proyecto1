<?php
// models/DireccionDAO.php

include_once("config/db.php");
include_once("Direccion.php");

class DireccionDAO {
    /**
     * Obtener todas las direcciones de un usuario
     *
     * @param int $id_cliente
     * @return array
     */
    public static function getDireccionesByUsuario($id_cliente) {
        $conn = DataBase::connect();
        $sql = "SELECT * FROM Direccion WHERE id_cliente = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return [];
        }

        $stmt->bind_param("i", $id_cliente);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
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
        $conn->close();
        return $direcciones;
    }

    // Otros métodos según sea necesario (e.g., agregarDireccion, eliminarDireccion)
}
?>
