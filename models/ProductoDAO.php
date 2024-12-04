<?php
include_once("models/Producto.php");
include_once("config/db.php");

class ProductoDAO {
    public static function getAll() {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto");
        $stmt->execute();
        $result = $stmt->get_result();

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $producto = new Producto(
                $row['id_producto'],
                $row['nombre'],
                $row['descripcion'],
                $row['precio_base'],
                $row['img'],
                $row['tipo']
            );
            $productos[] = $producto;
        }
        $con->close();

        return $productos;
    }

    public static function getProducto($id) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $producto = new Producto(
                $row['id_producto'],
                $row['nombre'],
                $row['descripcion'],
                $row['precio_base'],
                $row['img'],
                $row['tipo']
            );
            return $producto;
        } else {
            return null;
        }
    }
}
?>
