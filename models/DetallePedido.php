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

    // Getters y Setters (si es necesario)
    // ...
}
?>
