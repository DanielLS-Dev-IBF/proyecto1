<?php
// models/PedidoDAO.php

include_once("config/db.php");
include_once("Pedido.php");

class PedidoDAO {
    /**
     * Crear un nuevo pedido en la base de datos
     *
     * @param Pedido $pedido
     * @return Pedido|null
     */
    public static function crearPedido(Pedido $pedido) {
        $conn = DataBase::connect();

        $sql = "INSERT INTO Pedidos (id_usuario, nombre_completo, direccion, telefono, correo, metodo_pago, detalles_pago, subtotal, descuento, gastos_envio, total, fecha_pedido)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // Manejar error de preparación
            error_log("Error preparando la consulta: " . $conn->error);
            return null;
        }

        // Manejar NULL para id_codigo_descuento si es necesario
        // Suponiendo que 'detalles_pago' es una descripción del pago y no un ID de código de descuento
        $stmt->bind_param(
            "issssssddds",
            $pedido->getIdUsuario(),
            $pedido->getNombreCompleto(),
            $pedido->getDireccion(),
            $pedido->getTelefono(),
            $pedido->getCorreo(),
            $pedido->getMetodoPago(),
            $pedido->getDetallesPago(),
            $pedido->getSubtotal(),
            $pedido->getDescuento(),
            $pedido->getGastosEnvio(),
            $pedido->getTotal(),
            $pedido->getFechaPedido()
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
     * Obtener los pedidos de un usuario junto con sus detalles
     *
     * @param int $id_usuario
     * @return array
     */
    public static function getPedidosConDetallesByUsuario($id_usuario) {
        $conn = DataBase::connect();
        $sql = "SELECT p.*, dp.nombre_producto, dp.precio_unitario, dp.cantidad, dp.total_producto
                FROM Pedidos p
                LEFT JOIN Detalles_pedidos dp ON p.id_pedido = dp.id_pedido
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_pedido DESC, dp.id_detalle_pedido ASC";
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
            $pedido_id = $row['id_pedido'];
            if (!isset($pedidos[$pedido_id])) {
                $pedidos[$pedido_id] = [
                    'id_pedido' => $pedido_id,
                    'fecha_pedido' => $row['fecha_pedido'],
                    'total' => $row['total'],
                    'direccion' => $row['direccion'],
                    'metodo_pago' => $row['metodo_pago'],
                    'detalles_pago' => $row['detalles_pago'],
                    'detalles' => []
                ];
            }

            if ($row['nombre_producto']) {
                $pedidos[$pedido_id]['detalles'][] = [
                    'nombre_producto' => $row['nombre_producto'],
                    'precio_unitario' => $row['precio_unitario'],
                    'cantidad' => $row['cantidad'],
                    'total_producto' => $row['total_producto']
                ];
            }
        }

        $stmt->close();
        $conn->close();
        return $pedidos;
    }

    // Otros métodos según sea necesario (e.g., actualizarPedido, eliminarPedido)
}
?>
