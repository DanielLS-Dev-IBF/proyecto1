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
        $con = DataBase::connect();

        $sql = "INSERT INTO Detalles_pedidos (id_pedido, id_producto, nombre_producto, precio_unitario, cantidad, total_producto)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return false;
        }

        // **Asignar los valores a variables temporales**
        $id_pedido = $detalle->getIdPedido();           // int
        $id_producto = $detalle->getIdProducto();       // int
        $nombre_producto = $detalle->getNombreProducto(); // string
        $precio_unitario = $detalle->getPrecioUnitario(); // double
        $cantidad = $detalle->getCantidad();             // int
        $total_producto = $detalle->getTotalProducto();   // double

        // **Corregir la cadena de tipos: "iisdid"**
        $stmt->bind_param(
            "iisdid",
            $id_pedido,
            $id_producto,
            $nombre_producto,
            $precio_unitario,
            $cantidad,
            $total_producto
        );

        if ($stmt->execute()) {
            $stmt->close();
            $con->close();
            return true;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
            return false;
        }
    }

    /**
     * Obtener detalles de pedido por ID de pedido
     *
     * @param int $id_pedido
     * @return array
     */
    public static function obtenerDetallesPorPedido($id_pedido, $con = null) {
        $ownConnection = false;
        if ($con === null) {
            $con = DataBase::connect();
            $ownConnection = true;
        }
    
        $sql = "SELECT * FROM Detalles_pedidos WHERE id_pedido = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            if ($ownConnection) $con->close();
            return [];
        }
    
        $stmt->bind_param("i", $id_pedido);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            if ($ownConnection) $con->close();
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
            $detalle->setIdPedido($row['id_detalle_pedido']);
            $detalles[] = $detalle;
        }
    
        $stmt->close();
        if ($ownConnection) $con->close();
        return $detalles;
    }
    
    /**
     * Actualizar un detalle del pedido
     *
     * @param DetallePedido $detalle
     * @param mysqli $con
     * @return bool
     */
    public static function actualizarDetallePedido(DetallePedido $detalle, $con = null) {
        $ownConnection = false;
        if ($con === null) {
            $con = DataBase::connect();
            $ownConnection = true;
        }
    
        $sql = "UPDATE Detalles_pedidos 
                SET id_producto = ?, nombre_producto = ?, precio_unitario = ?, cantidad = ?, total_producto = ?
                WHERE id_detalle_pedido = ?";
    
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            if ($ownConnection) $con->close();
            return false;
        }
    
        $id_producto = $detalle->getIdProducto();
        $nombre_producto = $detalle->getNombreProducto();
        $precio_unitario = $detalle->getPrecioUnitario();
        $cantidad = $detalle->getCantidad();
        $total_producto = $detalle->getTotalProducto();
        $id_detalle_pedido = $detalle->getIdPedido();
    
        $stmt->bind_param(
            "isdidi",
            $id_producto,
            $nombre_producto,
            $precio_unitario,
            $cantidad,
            $total_producto,
            $id_detalle_pedido
        );
    
        if ($stmt->execute()) {
            $stmt->close();
            if ($ownConnection) $con->close();
            return true;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            if ($ownConnection) $con->close();
            return false;
        }
    }
    
    /**
     * Eliminar un detalle del pedido
     *
     * @param int $id_detalle_pedido
     * @param mysqli $con
     * @return bool
     */
    public static function eliminarDetallePedido($id_detalle_pedido, $con = null) {
        $ownConnection = false;
        if ($con === null) {
            $con = DataBase::connect();
            $ownConnection = true;
        }
    
        $sql = "DELETE FROM Detalles_pedidos WHERE id_detalle_pedido = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            if ($ownConnection) $con->close();
            return false;
        }
    
        $stmt->bind_param("i", $id_detalle_pedido);
        if ($stmt->execute()) {
            $stmt->close();
            if ($ownConnection) $con->close();
            return true;
        } else {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            if ($ownConnection) $con->close();
            return false;
        }
    }
    
}
?>
