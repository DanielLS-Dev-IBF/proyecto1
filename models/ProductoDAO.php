<?php
include_once("Producto.php");
include_once("config/db.php");

class ProductoDAO {
    // Obtener todos los productos ordenados alfabéticamente
    public static function getAll() {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto ORDER BY nombre ASC");
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
                $row['tipo']
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
                $row['tipo']
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
    public static function store(Producto $producto): bool
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO Proyecto1.Producto
            (nombre, descripcion, precio_base, img, tipo)
            VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Prepare failed in store: (" . $con->errno . ") " . $con->error);
            return false;
        }

        $nombre       = $producto->getNombre();
        $descripcion  = $producto->getDescripcion();
        $precio_base  = $producto->getPrecio_base();
        $img          = $producto->getImg();
        $tipo         = $producto->getTipo();

        // Ajusta los tipos (s=string, d=double) según lo que tengas en la BD
        $stmt->bind_param("ssdss", $nombre, $descripcion, $precio_base, $img, $tipo);

        $res = $stmt->execute();
        if (!$res) {
            error_log("Execute failed in store: " . $stmt->error);
        }

        $stmt->close();
        $con->close();
        return $res;
    }

    public static function updateProducto(Producto $producto): bool
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE Proyecto1.Producto
            SET nombre = ?, descripcion = ?, precio_base = ?, img = ?, tipo = ?
            WHERE id_producto = ?");

        if (!$stmt) {
            error_log("Prepare failed in updateProducto: (" . $con->errno . ") " . $con->error);
            return false;
        }

        $nombre       = $producto->getNombre();
        $descripcion  = $producto->getDescripcion();
        $precio_base  = $producto->getPrecio_base();
        $img          = $producto->getImg();
        $tipo         = $producto->getTipo();
        $id_producto  = $producto->getId_producto();

        $stmt->bind_param("ssdssi", 
            $nombre,
            $descripcion,
            $precio_base,
            $img,
            $tipo,
            $id_producto
        );

        $res = $stmt->execute();
        if (!$res) {
            error_log("Execute failed in updateProducto: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
        $con->close();
        return $res; 
    }

    // Eliminar un producto por ID
    public static function destroy($id): bool
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM Proyecto1.Producto WHERE id_producto = ?");
        if (!$stmt) {
            error_log("Prepare failed in destroy: (" . $con->errno . ") " . $con->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        if (!$res) {
            error_log("Execute failed in destroy: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();
        $con->close();
        return $res;
    }

    // Obtener productos por tipo con paginación y orden alfabético
    public static function getProductosPorTipoPaginados($tipo, $limit, $offset){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto WHERE tipo = ? ORDER BY nombre ASC LIMIT ? OFFSET ?");
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
                $row['tipo']
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

    // Obtener todos los productos con paginación y orden alfabético
    public static function getAllPaginados($limit, $offset){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto ORDER BY nombre ASC LIMIT ? OFFSET ?");
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
                $row['tipo']
            );
            $productos[] = $producto;
        }
        $stmt->close();
        $con->close();

        // Depuración: Imprimir la cantidad de productos obtenidos
        error_log("getAllPaginados - Productos obtenidos: " . count($productos));

        return $productos;
    }

    /**
     * Obtener productos filtrados por tipo y término de búsqueda con paginación.
     *
     * @param string|null $tipo
     * @param string|null $search
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getProductosFiltrados($tipo, $search, $limit, $offset){
        $con = DataBase::connect();

        // Construir la consulta con condiciones dinámicas
        $sql = "SELECT * FROM Proyecto1.Producto WHERE 1=1";
        $params = [];
        $types = "";

        if ($tipo && $tipo !== 'Todos') {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
            $types .= "s";
        }

        if (!empty($search)) {
            $sql .= " AND nombre LIKE ?";
            $params[] = '%' . $search . '%';
            $types .= "s";
        }

        $sql .= " ORDER BY nombre ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $types .= "i";
        $params[] = $offset;
        $types .= "i";

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return [];
        }

        // Enlazar parámetros dinámicamente
        $stmt->bind_param($types, ...$params);

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
                $row['tipo']
            );
            $productos[] = $producto;
        }
        $stmt->close();
        $con->close();

        return $productos;
    }

    /**
     * Contar productos filtrados por tipo y término de búsqueda.
     *
     * @param string|null $tipo
     * @param string|null $search
     * @return int
     */
    public static function countProductosFiltrados($tipo, $search){
        $con = DataBase::connect();

        // Construir la consulta con condiciones dinámicas
        $sql = "SELECT COUNT(*) as total FROM Proyecto1.Producto WHERE 1=1";
        $params = [];
        $types = "";

        if ($tipo && $tipo !== 'Todos') {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
            $types .= "s";
        }

        if (!empty($search)) {
            $sql .= " AND nombre LIKE ?";
            $params[] = '%' . $search . '%';
            $types .= "s";
        }

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
            return 0;
        }

        // Enlazar parámetros dinámicamente
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
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

        return $total;
    }
}
?>
