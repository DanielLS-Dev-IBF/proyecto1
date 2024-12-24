<?php
// models/CodigoDescuentoDAO.php

include_once("config/db.php");

class CodigoDescuentoDAO {

    // Obtener descuento por código
    public static function obtenerDescuentoPorCodigo($codigo) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Codigo_Descuento WHERE codigo = ?");
        if (!$stmt) {
            die("Preparación de consulta fallida: " . $con->error);
        }
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $descuento = $resultado->fetch_assoc();
        $stmt->close();
        return $descuento;
    }

    // Obtener uso por usuario
    public static function obtenerUsoPorUsuario($id_usuario, $id_codigo_descuento) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT veces_usado FROM Usuario_Descuento WHERE id_usuario = ? AND id_codigo_descuento = ?");
        if (!$stmt) {
            die("Preparación de consulta fallida: " . $con->error);
        }
        $stmt->bind_param("ii", $id_usuario, $id_codigo_descuento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $uso = $resultado->fetch_assoc();
        $stmt->close();
        return $uso ? $uso['veces_usado'] : 0;
    }

    // Incrementar usos totales
    public static function incrementarUsosTotales($id_codigo_descuento) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE Codigo_Descuento SET usado = usado + 1 WHERE id_codigo_descuento = ?");
        if (!$stmt) {
            die("Preparación de consulta fallida: " . $con->error);
        }
        $stmt->bind_param("i", $id_codigo_descuento);
        if (!$stmt->execute()) {
            die("Ejecución de consulta fallida: " . $stmt->error);
        }
        $stmt->close();
    }

    // Incrementar usos por usuario
    public static function incrementarUsosPorUsuario($id_usuario, $id_codigo_descuento) {
        $con = DataBase::connect();
        // Verificar si ya existe un registro para este usuario y código
        $stmt = $con->prepare("SELECT veces_usado FROM Usuario_Descuento WHERE id_usuario = ? AND id_codigo_descuento = ?");
        if (!$stmt) {
            die("Preparación de consulta fallida: " . $con->error);
        }
        $stmt->bind_param("ii", $id_usuario, $id_codigo_descuento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $uso = $resultado->fetch_assoc();
        $stmt->close();

        if ($uso) {
            // Actualizar el contador existente
            $stmt = $con->prepare("UPDATE Usuario_Descuento SET veces_usado = veces_usado + 1 WHERE id_usuario = ? AND id_codigo_descuento = ?");
            if (!$stmt) {
                die("Preparación de consulta fallida: " . $con->error);
            }
            $stmt->bind_param("ii", $id_usuario, $id_codigo_descuento);
            if (!$stmt->execute()) {
                die("Ejecución de consulta fallida: " . $stmt->error);
            }
            $stmt->close();
        } else {
            // Insertar un nuevo registro
            $stmt = $con->prepare("INSERT INTO Usuario_Descuento (id_usuario, id_codigo_descuento, veces_usado) VALUES (?, ?, 1)");
            if (!$stmt) {
                die("Preparación de consulta fallida: " . $con->error);
            }
            $stmt->bind_param("ii", $id_usuario, $id_codigo_descuento);
            if (!$stmt->execute()) {
                die("Ejecución de consulta fallida: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}
?>
