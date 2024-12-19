<?php
// models/DetallePedidoDAO.php

include_once("config/db.php");
include_once("models/DetallePedido.php");

class DetallePedidoDAO {
    private static function getConnection() {
        // Asume que config/db.php establece una conexión en $conn
        global $conn;
        return $conn;
    }

    public static function agregarDetallePedido(DetallePedido $detalle) {
        $conn = self::getConnection();

        $sql = "INSERT INTO detalle_pedidos (id_pedido, id_producto, nombre_producto, precio_unitario, cantidad, total_producto)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issdii",
            $detalle->id_pedido,
            $detalle->id_producto,
            $detalle->nombre_producto,
            $detalle->precio_unitario,
            $detalle->cantidad,
            $detalle->total_producto
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            // Manejar error
            $stmt->close();
            return false;
        }
    }
}
?>
