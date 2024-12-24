<?php
class Producto {
    protected $id_producto;
    protected $nombre;
    protected $descripcion;
    protected $img;
    protected $precio_base;
    protected $tipo;


    public function __construct($id_producto, $nombre, $descripcion, $precio_base, $img, $tipo) {
        $this->id_producto = $id_producto;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio_base = $precio_base;
        $this->img = $img;
        $this->tipo = $tipo;

    }

    // Getters
    public function getId_producto() {
        return $this->id_producto;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getImg() {
        return $this->img;
    }

    public function getPrecio_base() {
        return $this->precio_base;
    }

    public function getTipo() {
        return $this->tipo;
    }

    // Setters (si los necesitas)
    public function setId_producto($id_producto) {
        $this->id_producto = $id_producto;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setImg($img) {
        $this->img = $img;
    }

    public function setPrecio_base($precio_base) {
        $this->precio_base = $precio_base;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
}
?>
