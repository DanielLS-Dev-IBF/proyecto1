<?php
// models/Direccion.php

class Direccion {
    private $id_direccion;
    private $id_usuario;
    private $direccion;
    private $codigo_postal;

     // Constructor con valores por defecto
     public function __construct($id_direccion = null, $id_usuario = null, $direccion = '', $codigo_postal = 0) {
        $this->id_direccion = $id_direccion;
        $this->id_usuario = $id_usuario;
        $this->direccion = $direccion;
        $this->codigo_postal = $codigo_postal;
    }

    // Getters y Setters

    public function getIdDireccion() {
        return $this->id_direccion;
    }

    public function setIdDireccion($id_direccion) {
        $this->id_direccion = $id_direccion;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function getCodigoPostal() {
        return $this->codigo_postal;
    }

    public function setCodigoPostal($codigo_postal) {
        $this->codigo_postal = $codigo_postal;
    }

}
?>
