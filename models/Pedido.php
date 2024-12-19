<?php
// models/Pedido.php

class Pedido {
    public $id_usuario;
    public $nombre_completo;
    public $direccion;
    public $telefono;
    public $correo;
    public $metodo_pago;
    public $detalles_pago;
    public $subtotal;
    public $descuento;
    public $gastos_envio;
    public $total;
    public $fecha_pedido;
    private $id_pedido;

    public function __construct($id_usuario, $nombre_completo, $direccion, $telefono, $correo, $metodo_pago, $detalles_pago, $subtotal, $descuento, $gastos_envio, $total) {
        $this->id_usuario = $id_usuario;
        $this->nombre_completo = $nombre_completo;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->metodo_pago = $metodo_pago;
        $this->detalles_pago = $detalles_pago;
        $this->subtotal = $subtotal;
        $this->descuento = $descuento;
        $this->gastos_envio = $gastos_envio;
        $this->total = $total;
        $this->fecha_pedido = date('Y-m-d H:i:s');
    }

    // Getter y Setter para id_pedido
    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    // Otros getters y setters si es necesario
}
?>
