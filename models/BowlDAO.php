<?php
//Incluimos la clase Bowl y db para poder connectarnos a la base de datos
include_once("models/Bowl.php");
include_once("config/db.php");

//Clase DAO de Bowl para realizar acciones en la base de datos
class BowlDAO{

    //Funcion getAll encargada de obtener los datos de la base de datos
    public static function getAll(){
        //Realizamos la conexión a la DB
        $con = DataBase::connect();
        //Preparamos la consulta SQL    
        $stmt = $con->prepare("Select * from Proyecto1.Producto");
        //$stmt->bind_param("s", $nombre);

        //Ejecutamos la consulta
        $stmt->execute();
        $result = $stmt->get_result();

        //Creamos el array donde almacenaremos los datos que encontremos con fetch
        $productos = [];
        //Bucle para recopilar los datos con fetch
        while ($producto = $result->fetch_object("Bowl")) {
            $productos[] = $producto;
        }
        $con->close();

        return $productos;
    }

    //Funcion getProducto encargada de obtener el producto seleccionado a partir del id
    public static function getProducto($id) {
        // Conexión a la base de datos
        $con = DataBase::connect();
    
        try {
            // Preparamos la consulta SQL
            $stmt = $con->prepare("SELECT * FROM Proyecto1.Producto WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            
            // Ejecutamos la consulta
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Verificamos si se encontraron resultados
            if ($result->num_rows > 0) {
                // Retornamos el producto como objeto de la clase Bowl
                return $result->fetch_object("Bowl");
            } else {
                // Producto no encontrado
                return null;
            }
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error al obtener el producto: " . $e->getMessage());
            return null;
        } finally {
            // Cerramos la conexión
            $stmt->close();
            $con->close();
        }
    }
    

    //Función store encargada de insertar datos en la base datos
    public static function store($producto){
        //Realizamos la conexión a la DB
        $con = DataBase::connect();
        //Preparamos la consulta SQL
        $stmt = $con->prepare("INSERT INTO Producto (nombre, descripcion, precio_base) VALUES (?, ?, ?)");

        /*V1*/
        $stmt->bind_param("ssd", $producto->getNombre(), $producto->getDescripcion(), $producto->getPrecio_base());

        /**V2
        $nombre = $producto->getNombre();
        $talla = $producto->getTalla();
        $precio = $producto->getPrecio();
        $stmt->bind_param("ssd", $nombre, $talla, $precio);
        */

        //Ejecutamos la consulta
        $stmt->execute();
        $con->close();
    }
    //Función destroy para borrar información de la base de datos
    public static function destroy($id){
        //Realizamos la conexión a la DB
        $con = DataBase::connect();
        //Preparamos la consulta SQL
        $stmt = $con->prepare("DELETE FROM Producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);

        $stmt->execute();
        $con->close();

    }
}