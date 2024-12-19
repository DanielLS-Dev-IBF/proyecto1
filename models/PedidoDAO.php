<?php
// models/PedidoDAO.php

include_once("../config/db.php");
include_once("Pedido.php");

class PedidoDAO {
    /**
     * Obtener la conexión a la base de datos
     *
     * @return mysqli
     */
    public static function getConnection() {
        return DataBase::connect();
    }

    /**
     * Crear un nuevo pedido en la base de datos
     *
     * @param Pedido $pedido
     * @return Pedido|null
     */
    public static function crearPedido(Pedido $pedido) {
        $conn = self::getConnection();

        $sql = "INSERT INTO pedidos (id_usuario, nombre_completo, direccion, telefono, correo, metodo_pago, detalles_pago, subtotal, descuento, gastos_envio, total, fecha_pedido)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // Manejar error de preparación
            error_log("Error preparando la consulta: " . $conn->error);
            return null;
        }

        $stmt->bind_param(
            "issssssddds",
            $pedido->id_usuario,
            $pedido->nombre_completo,
            $pedido->direccion,
            $pedido->telefono,
            $pedido->correo,
            $pedido->metodo_pago,
            $pedido->detalles_pago,
            $pedido->subtotal,
            $pedido->descuento,
            $pedido->gastos_envio,
            $pedido->total,
            $pedido->fecha_pedido
        );

        if ($stmt->execute()) {
            $pedido->setIdPedido($stmt->insert_id);
            $stmt->close();
            $conn->close();
            return $pedido;
        } else {
            // Manejar error de ejecución
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return null;
        }
    }

    /**
     * Obtener los pedidos de un usuario
     *
     * @param int $id_usuario
     * @return array
     */
    public static function getPedidosByUsuario($id_usuario) {
        $conn = self::getConnection();

        $sql = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return [];
        }

        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return [];
        }

        $result = $stmt->get_result();

        $pedidos = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = $row;
        }

        $stmt->close();
        $conn->close();
        return $pedidos;
    }

    // Otros métodos según sea necesario
}
?>
