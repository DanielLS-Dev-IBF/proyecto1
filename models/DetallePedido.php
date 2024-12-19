<?php
// models/DetallePedido.php

class DetallePedido {
    private $id_pedido;
    private $id_producto;
    private $nombre_producto;
    private $precio_unitario;
    private $cantidad;
    private $total_producto;

    public function __construct($id_pedido, $id_producto, $nombre_producto, $precio_unitario, $cantidad, $total_producto) {
        $this->id_pedido = $id_pedido;
        $this->id_producto = $id_producto;
        $this->nombre_producto = $nombre_producto;
        $this->precio_unitario = $precio_unitario;
        $this->cantidad = $cantidad;
        $this->total_producto = $total_producto;
    }

    // Getters y Setters

    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function getIdProducto() {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }

    public function getNombreProducto() {
        return $this->nombre_producto;
    }

    public function setNombreProducto($nombre_producto) {
        $this->nombre_producto = $nombre_producto;
    }

    public function getPrecioUnitario() {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario($precio_unitario) {
        $this->precio_unitario = $precio_unitario;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function getTotalProducto() {
        return $this->total_producto;
    }

    public function setTotalProducto($total_producto) {
        $this->total_producto = $total_producto;
    }
}
?>
