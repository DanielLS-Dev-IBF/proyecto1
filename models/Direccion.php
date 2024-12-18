<?php
// models/Direccion.php
class Direccion {
    protected $id_direccion;
    protected $id_cliente;
    protected $direccion;
    protected $codigo_postal;

    public function getId_direccion() {
        return $this->id_direccion;
    }

    public function setId_direccion($id) {
        $this->id_direccion = $id;
        return $this;
    }

    public function getId_cliente() {
        return $this->id_cliente;
    }

    public function setId_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
        return $this;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
        return $this;
    }

    public function getCodigo_postal() {
        return $this->codigo_postal;
    }

    public function setCodigo_postal($codigo_postal) {
        $this->codigo_postal = $codigo_postal;
        return $this;
    }
}
