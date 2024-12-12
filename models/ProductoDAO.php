<?php
include_once("Producto.php");
include_once("config/db.php");

class ProductoDAO {
    // Obtener todos los productos
    public static function getAll() {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return [];
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();

        if (!$result) {
            error_log("Get result failed: (" . $stmt->errno . ") " . $stmt->error);
            return [];
        }

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
        $stmt->close();
        $con->close();

        return $productos;
    }

    // Obtener un producto por ID
    public static function getProducto($id) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto WHERE id_producto = ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return null;
        }
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return null;
        }

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
            $stmt->close();
            $con->close();
            return $producto;
        } else {
            $stmt->close();
            $con->close();
            return null;
        }
    }

    // Insertar un nuevo producto
    public static function store($producto){
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO Proyecto1.Producto (nombre, descripcion, precio_base, img, tipo, volumen, calorias) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return;
        }

        $nombre = $producto->getNombre();
        $descripcion = $producto->getDescripcion();
        $precio_base = $producto->getPrecio_base();
        $img = $producto->getImg();
        $tipo = $producto->getTipo();
        $volumen = $producto->getVolumen();
        $calorias = $producto->getCalorias();

        // Ajusta los tipos según los datos reales
        $stmt->bind_param("ssdsssi", $nombre, $descripcion, $precio_base, $img, $tipo, $volumen, $calorias);

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
        $con->close();
    }

    // Eliminar un producto por ID
    public static function destroy($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM Proyecto1.Producto WHERE id_producto = ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return;
        }
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
        $con->close();
    }

    // Obtener productos por tipo con paginación
    public static function getProductosPorTipoPaginados($tipo, $limit, $offset){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto WHERE tipo = ? LIMIT ? OFFSET ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return [];
        }

        // 's' para string, 'i' para integer
        $stmt->bind_param("sii", $tipo, $limit, $offset);

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        if (!$result) {
            error_log("Get result failed: (" . $stmt->errno . ") " . $stmt->error);
            return [];
        }

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
        $stmt->close();
        $con->close();

        // Depuración: Imprimir la cantidad de productos obtenidos
        error_log("getProductosPorTipoPaginados - Tipo: " . $tipo . ", Productos obtenidos: " . count($productos));

        return $productos;
    }

    // Contar productos por tipo
    public static function countProductosPorTipo($tipo){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT COUNT(*) as total FROM Proyecto1.Producto WHERE tipo = ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return 0;
        }

        $stmt->bind_param("s", $tipo);

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return 0;
        }

        $result = $stmt->get_result();
        if (!$result) {
            error_log("Get result failed: (" . $stmt->errno . ") " . $stmt->error);
            return 0;
        }

        $row = $result->fetch_assoc();
        $total = $row['total'];

        $stmt->close();
        $con->close();

        // Depuración: Imprimir el total de productos
        error_log("countProductosPorTipo - Tipo: " . $tipo . ", Total productos: " . $total);

        return $total;
    }

    // Contar todos los productos
    public static function countProductos(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT COUNT(*) as total FROM Proyecto1.Producto");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return 0;
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return 0;
        }

        $result = $stmt->get_result();
        if (!$result) {
            error_log("Get result failed: (" . $stmt->errno . ") " . $stmt->error);
            return 0;
        }

        $row = $result->fetch_assoc();
        $total = $row['total'];

        $stmt->close();
        $con->close();

        // Depuración: Imprimir el total de productos
        error_log("countProductos - Total productos: " . $total);

        return $total;
    }

    // Obtener todos los productos con paginación
    public static function getAllPaginados($limit, $offset){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto LIMIT ? OFFSET ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);

        if (!$stmt->execute()) {
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        if (!$result) {
            error_log("Get result failed: (" . $stmt->errno . ") " . $stmt->error);
            return [];
        }

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
        $stmt->close();
        $con->close();

        // Depuración: Imprimir la cantidad de productos obtenidos
        error_log("getAllPaginados - Productos obtenidos: " . count($productos));

        return $productos;
    }
}
?>
