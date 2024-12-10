<?php
include_once("Producto.php");
include_once("config/db.php");

class ProductoDAO {
    // Obtener todos los productos
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
                $row['tipo'],
                isset($row['volumen']) ? $row['volumen'] : null,
                isset($row['calorias']) ? $row['calorias'] : null
            );
            $productos[] = $producto;
        }
        $con->close();

        return $productos;
    }

    // Obtener un producto por ID
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
                $row['tipo'],
                isset($row['volumen']) ? $row['volumen'] : null,
                isset($row['calorias']) ? $row['calorias'] : null
            );
            return $producto;
        } else {
            return null;
        }
    }

    // Insertar un nuevo producto
    public static function store($producto){
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO Proyecto1.Producto (nombre, descripcion, precio_base, img, tipo, volumen, calorias) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $nombre = $producto->getNombre();
        $descripcion = $producto->getDescripcion();
        $precio_base = $producto->getPrecio_base();
        $img = $producto->getImg();
        $tipo = $producto->getTipo();
        $volumen = $producto->getVolumen();
        $calorias = $producto->getCalorias();

        $stmt->bind_param("ssdsssi", $nombre, $descripcion, $precio_base, $img, $tipo, $volumen, $calorias);

        $stmt->execute();
        $con->close();
    }

    // Eliminar un producto por ID
    public static function destroy($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM Proyecto1.Producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);

        $stmt->execute();
        $con->close();
    }
}
?>
