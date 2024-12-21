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

        $sql = "INSERT INTO Pedidos (
                    id_usuario, 
                    nombre_completo, 
                    direccion, 
                    telefono, 
                    correo, 
                    metodo_pago, 
                    detalles_pago, 
                    subtotal, 
                    descuento, 
                    gastos_envio, 
                    total, 
                    fecha_pedido
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // Manejar error de preparación
            error_log("Error preparando la consulta: " . $conn->error);
            return null;
        }

        // Asignar cada valor a una variable
        $id_usuario = $pedido->getIdUsuario();
        $nombre_completo = $pedido->getNombreCompleto();
        $direccion = $pedido->getDireccion();
        $telefono = $pedido->getTelefono();
        $correo = $pedido->getCorreo();
        $metodo_pago = $pedido->getMetodoPago();
        $detalles_pago = $pedido->getDetallesPago();
        $subtotal = $pedido->getSubtotal();
        $descuento = $pedido->getDescuento();
        $gastos_envio = $pedido->getGastosEnvio();
        $total = $pedido->getTotal();
        $fecha_pedido = $pedido->getFechaPedido();

        // Cadena de Tipos Correcta
        $stmt->bind_param(
            "issssssdddds",
            $id_usuario, 
            $nombre_completo, 
            $direccion, 
            $telefono, 
            $correo, 
            $metodo_pago, 
            $detalles_pago, 
            $subtotal, 
            $descuento, 
            $gastos_envio, 
            $total, 
            $fecha_pedido
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
     * Obtener pedidos paginados de un usuario junto con sus detalles.
     *
     * @param int $id_usuario
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function obtenerPedidosPorUsuarioPaginados($id_usuario, $limit, $offset) {
        $conn = DataBase::connect();
        $sql = "SELECT p.*, dp.nombre_producto, dp.precio_unitario, dp.cantidad, dp.total_producto
                FROM Pedidos p
                LEFT JOIN Detalles_pedidos dp ON p.id_pedido = dp.id_pedido
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_pedido DESC, dp.id_detalle_pedido ASC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return [];
        }

        $stmt->bind_param("iii", $id_usuario, $limit, $offset);
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
        return array_values($pedidos); // Reindexar array
    }

    /**
     * Contar el total de pedidos de un usuario.
     *
     * @param int $id_usuario
     * @return int
     */
    public static function contarPedidosPorUsuario($id_usuario) {
        $conn = DataBase::connect();
        $sql = "SELECT COUNT(*) as total FROM Pedidos WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $conn->error);
            return 0;
        }

        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $conn->close();
            return 0;
        }

        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        $total = isset($fila['total']) ? (int)$fila['total'] : 0;

        $stmt->close();
        $conn->close();

        return $total;
    }

    /**
     * Obtener pedidos completos de un usuario (sin paginación).
     *
     * @param int $id_usuario
     * @return array
     */
    public static function getPedidosConDetallesByUsuario($id_usuario) {
        // Mantener este método si aún lo necesitas sin paginación
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
        return array_values($pedidos); // Reindexar array
    }

}
?>
