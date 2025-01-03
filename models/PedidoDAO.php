<?php
// models/PedidoDAO.php

class PedidoDAO {
    public static function getPedidoById($id_pedido, $con = null) {
        $ownConnection = false;
        if ($con === null) {
            $con = DataBase::connect();
            $ownConnection = true;
        }
    
        $sql = "SELECT * FROM Pedidos WHERE id_pedido = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            if ($ownConnection) $con->close();
            return null;
        }
    
        $stmt->bind_param("i", $id_pedido);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            if ($ownConnection) $con->close();
            return null;
        }
    
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pedido = new Pedido(
                $row['id_usuario'],
                $row['nombre_completo'],
                $row['direccion'],
                $row['telefono'],
                $row['correo'],
                $row['metodo_pago'],
                $row['detalles_pago'],
                $row['subtotal'],
                $row['descuento'],
                $row['gastos_envio'],
                $row['total'],
            );
            $pedido->setIdPedido($row['id_pedido']);
            $stmt->close();
            if ($ownConnection) $con->close();
            return $pedido;
        } else {
            $stmt->close();
            if ($ownConnection) $con->close();
            return null;
        }
    }
    
    public static function getAllPedidos()
    {
        // Si no tienes un método en PedidoDAO, lo implementas de forma simple:
        $con = DataBase::connect();
        $sql = "SELECT * FROM Pedidos ORDER BY fecha_pedido DESC";
        $result = $con->query($sql);

        $pedidos = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = $row; 
        }
        $con->close();
        return $pedidos;
    }

    /**
     * Crear un nuevo pedido en la base de datos
     *
     * @param Pedido $pedido
     * @return Pedido|null
     */
    public static function crearPedido(Pedido $pedido) {
        $con = DataBase::connect();

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

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            // Manejar error de preparación
            error_log("Error preparando la consulta: " . $con->error);
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
            $con->close();
            return $pedido;
        } else {
            // Manejar error de ejecución
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
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
        $con = DataBase::connect();
    
        // Paso 1: Obtener los pedidos paginados
        $sql_pedidos = "SELECT * FROM Pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC LIMIT ? OFFSET ?";
        $stmt_pedidos = $con->prepare($sql_pedidos);
        if (!$stmt_pedidos) {
            error_log("Error preparando la consulta de pedidos: " . $con->error);
            return [];
        }
        $stmt_pedidos->bind_param("iii", $id_usuario, $limit, $offset);
        if (!$stmt_pedidos->execute()) {
            error_log("Error ejecutando la consulta de pedidos: " . $stmt_pedidos->error);
            $stmt_pedidos->close();
            $con->close();
            return [];
        }
        $result_pedidos = $stmt_pedidos->get_result();
        $pedidos = [];
        $pedido_ids = [];
        while ($row = $result_pedidos->fetch_assoc()) {
            $pedido_id = $row['id_pedido'];
            $pedidos[$pedido_id] = [
                'id_pedido' => $pedido_id,
                'fecha_pedido' => $row['fecha_pedido'],
                'total' => $row['total'],
                'direccion' => $row['direccion'],
                'metodo_pago' => $row['metodo_pago'],
                'detalles_pago' => $row['detalles_pago'],
                'detalles' => []
            ];
            $pedido_ids[] = $pedido_id;
        }
        $stmt_pedidos->close();
    
        if (!empty($pedido_ids)) {
            // Paso 2: Obtener todos los detalles para los pedidos obtenidos
            $placeholders = implode(',', array_fill(0, count($pedido_ids), '?'));
            $sql_detalles = "SELECT dp.*, pr.nombre AS nombre_producto 
                             FROM Detalles_pedidos dp 
                             LEFT JOIN Producto pr ON dp.id_producto = pr.id_producto 
                             WHERE dp.id_pedido IN ($placeholders) 
                             ORDER BY dp.id_detalle_pedido ASC";
            $stmt_detalles = $con->prepare($sql_detalles);
            if (!$stmt_detalles) {
                error_log("Error preparando la consulta de detalles: " . $con->error);
                $con->close();
                return array_values($pedidos);
            }
    
            // Crear el tipo de parámetros para bind_param
            $types = str_repeat('i', count($pedido_ids)); // 'i' para enteros
            $stmt_detalles->bind_param($types, ...$pedido_ids);
    
            if (!$stmt_detalles->execute()) {
                error_log("Error ejecutando la consulta de detalles: " . $stmt_detalles->error);
                $stmt_detalles->close();
                $con->close();
                return array_values($pedidos);
            }
    
            $result_detalles = $stmt_detalles->get_result();
            while ($row = $result_detalles->fetch_assoc()) {
                $pedido_id = $row['id_pedido'];
                if (isset($pedidos[$pedido_id])) {
                    $pedidos[$pedido_id]['detalles'][] = [
                        'nombre_producto' => $row['nombre_producto'],
                        'precio_unitario' => $row['precio_unitario'],
                        'cantidad' => $row['cantidad'],
                        'total_producto' => $row['total_producto']
                    ];
                }
            }
            $stmt_detalles->close();
        }
    
        $con->close();
        return array_values($pedidos); // Reindexar array
    }    
    

    /**
     * Contar el total de pedidos de un usuario.
     *
     * @param int $id_usuario
     * @return int
     */
    public static function contarPedidosPorUsuario($id_usuario) {
        $con = DataBase::connect();
        $sql = "SELECT COUNT(*) as total FROM Pedidos WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return 0;
        }
    
        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
            return 0;
        }
    
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        $total = isset($fila['total']) ? (int)$fila['total'] : 0;
    
        $stmt->close();
        $con->close();
    
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
        $con = DataBase::connect();
        $sql = "SELECT p.*, dp.nombre_producto, dp.precio_unitario, dp.cantidad, dp.total_producto
                FROM Pedidos p
                LEFT JOIN Detalles_pedidos dp ON p.id_pedido = dp.id_pedido
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_pedido DESC, dp.id_detalle_pedido ASC";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando la consulta: " . $con->error);
            return [];
        }

        $stmt->bind_param("i", $id_usuario);
        if (!$stmt->execute()) {
            error_log("Error ejecutando la consulta: " . $stmt->error);
            $stmt->close();
            $con->close();
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
        $con->close();
        return array_values($pedidos); // Reindexar array
    }

    public static function deletePedido($id, $con = null)
    {
        $ownConnection = false;
        if ($con === null) {
            $con = DataBase::connect();
            $ownConnection = true;
        }

        $con->begin_transaction();

        try {
            // Eliminar detalles del pedido
            $sqlDetalles = "DELETE FROM Detalles_pedidos WHERE id_pedido = ?";
            $stmtDetalles = $con->prepare($sqlDetalles);
            if (!$stmtDetalles) {
                throw new Exception("Error preparando la consulta de detalles: " . $con->error);
            }
            $stmtDetalles->bind_param("i", $id);
            if (!$stmtDetalles->execute()) {
                throw new Exception("Error ejecutando la consulta de detalles: " . $stmtDetalles->error);
            }
            $stmtDetalles->close();

            // Eliminar el pedido
            $sqlPedido = "DELETE FROM Pedidos WHERE id_pedido = ?";
            $stmtPedido = $con->prepare($sqlPedido);
            if (!$stmtPedido) {
                throw new Exception("Error preparando la consulta de pedido: " . $con->error);
            }
            $stmtPedido->bind_param("i", $id);
            if (!$stmtPedido->execute()) {
                throw new Exception("Error ejecutando la consulta de pedido: " . $stmtPedido->error);
            }
            $stmtPedido->close();

            // Confirmar transacción
            $con->commit();

            if ($ownConnection) $con->close();
            return true;
        } catch (Exception $e) {
            // Revertir transacción
            $con->rollback();
            error_log($e->getMessage());
            if ($ownConnection) $con->close();
            return false;
        }
    }
        public static function updatePedido(Pedido $pedido): bool
    {
        $con = DataBase::connect();
        // Ajusta los campos que quieras actualizar
        $sql = "UPDATE Pedidos
                SET
                    id_usuario = ?,
                    nombre_completo = ?,
                    direccion = ?,
                    telefono = ?,
                    correo = ?,
                    metodo_pago = ?,
                    detalles_pago = ?,
                    subtotal = ?,
                    descuento = ?,
                    gastos_envio = ?,
                    total = ?
                WHERE id_pedido = ?";

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando updatePedido: " . $con->error);
            return false;
        }

        $id_usuario      = $pedido->getIdUsuario();
        $nombre_completo = $pedido->getNombreCompleto();
        $direccion       = $pedido->getDireccion();
        $telefono        = $pedido->getTelefono();
        $correo          = $pedido->getCorreo();
        $metodo_pago     = $pedido->getMetodoPago();
        $detalles_pago   = $pedido->getDetallesPago();
        $subtotal        = $pedido->getSubtotal();
        $descuento       = $pedido->getDescuento();
        $gastos_envio    = $pedido->getGastosEnvio();
        $total           = $pedido->getTotal();
        $id_pedido       = $pedido->getIdPedido(); // para el WHERE

        // Asigna los tipos correctos (int para id_usuario e id_pedido, double o float para subtotal, etc.)
        $stmt->bind_param(
            "issssssddddi",
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
            $id_pedido
        );

        $res = $stmt->execute();
        if (!$res) {
            error_log("Error ejecutando updatePedido: " . $stmt->error);
        }

        $stmt->close();
        $con->close();
        return $res; // true o false según éxito
    }
    public static function obtenerUltimoPedidoPorUsuario($id_usuario) {
        $conn = DataBase::connect();
        $stmt = $conn->prepare("SELECT * FROM Pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC LIMIT 1");
        $stmt->bind_param("i", $id_usuario);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($pedido = $result->fetch_assoc()) {
                return $pedido;
            }
        }
        return null;
    }


    


}
?>
