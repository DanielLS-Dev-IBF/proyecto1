<?php
// models/Direccion.php

class Direccion {
    private $id_direccion;
    private $id_usuario;
    private $direccion;
    private $codigo_postal;

    public function __construct($id_direccion, $id_usuario, $direccion, $codigo_postal) {
        $this->id_direccion = $id_direccion;
        $this->id_usuario = $id_usuario;
        $this->direccion = $direccion;
        $this->codigo_postal = $codigo_postal;
    }

    // Getters
    public function getIdDireccion() {
        return $this->id_direccion;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getCodigo_postal() {
        return $this->codigo_postal;
    }

}
?>
