<?php
// models/DetallePedidoDAO.php

include_once("config/db.php");
include_once("DetallePedido.php");

class DetallePedidoDAO {
    /**
     * Agregar un detalle de pedido a la base de datos
     *
     * @param DetallePedido $detalle
     * @return bool
     */
    public static function agregarDetallePedido(DetallePedido $detalle) {
        $conn = DataBase::connect();

        $sql = "INSERT INTO Detalles_pedidos (id_pedido, id_producto, nombre_producto, precio_unitario, cantidad, total_producto)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return false;
        }

        $stmt->bind_param(
            "iisdii",
            $detalle->getIdPedido(),
            $detalle->getIdProducto(),
            $detalle->getNombreProducto(),
            $detalle->getPrecioUnitario(),
            $detalle->getCantidad(),
            $detalle->getTotalProducto()
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return true;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    /**
     * Obtener detalles de pedido por ID de pedido
     *
     * @param int $id_pedido
     * @return array
     */
    public static function obtenerDetallesPorPedido($id_pedido) {
        $conn = DataBase::connect();
        $sql = "SELECT * FROM Detalles_pedidos WHERE id_pedido = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return [];
        }

        $stmt->bind_param("i", $id_pedido);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return [];
        }

        $result = $stmt->get_result();
        $detalles = [];
        while ($row = $result->fetch_assoc()) {
            $detalle = new DetallePedido(
                $row['id_pedido'],
                $row['id_producto'],
                $row['nombre_producto'],
                $row['precio_unitario'],
                $row['cantidad'],
                $row['total_producto']
            );
            $detalles[] = $detalle;
        }

        $stmt->close();
        $conn->close();
        return $detalles;
    }

    // Otros métodos según sea necesario (e.g., actualizarDetallePedido, eliminarDetallePedido)
}
?>
