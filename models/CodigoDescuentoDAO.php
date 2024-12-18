<?php
// models/CodigoDescuentoDAO.php

include_once("config/db.php");

class CodigoDescuentoDAO {

    /**
     * Obtener un descuento por código.
     *
     * @param string $codigo
     * @return array|null
     */
    public static function obtenerDescuentoPorCodigo($codigo) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Codigo_Descuento WHERE codigo = ? AND (fecha_expiracion IS NULL OR fecha_expiracion >= CURDATE()) LIMIT 1");
        
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return null;
        }

        if (!$stmt->bind_param("s", $codigo)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return null;
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return null;
        }

        $result = $stmt->get_result();
        $descuento = null;

        if ($result->num_rows > 0) {
            $descuento = $result->fetch_assoc();
        }

        $stmt->close();
        $con->close();

        return $descuento;
    }
}
?>
