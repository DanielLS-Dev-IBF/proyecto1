<?php
// models/Pedido.php

class Pedido {
    private $id_usuario;
    private $nombre_completo;
    private $direccion;
    private $telefono;
    private $correo;
    private $metodo_pago;
    private $detalles_pago;
    private $subtotal;
    private $descuento;
    private $gastos_envio;
    private $total;
    private $fecha_pedido;
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

    // Getters y Setters

    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getNombreCompleto() {
        return $this->nombre_completo;
    }

    public function setNombreCompleto($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    public function getMetodoPago() {
        return $this->metodo_pago;
    }

    public function setMetodoPago($metodo_pago) {
        $this->metodo_pago = $metodo_pago;
    }

    public function getDetallesPago() {
        return $this->detalles_pago;
    }

    public function setDetallesPago($detalles_pago) {
        $this->detalles_pago = $detalles_pago;
    }

    public function getSubtotal() {
        return $this->subtotal;
    }

    public function setSubtotal($subtotal) {
        $this->subtotal = $subtotal;
    }

    public function getDescuento() {
        return $this->descuento;
    }

    public function setDescuento($descuento) {
        $this->descuento = $descuento;
    }

    public function getGastosEnvio() {
        return $this->gastos_envio;
    }

    public function setGastosEnvio($gastos_envio) {
        $this->gastos_envio = $gastos_envio;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getFechaPedido() {
        return $this->fecha_pedido;
    }

    public function setFechaPedido($fecha_pedido) {
        $this->fecha_pedido = $fecha_pedido;
    }
}
?>
