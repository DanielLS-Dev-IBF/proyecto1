<?php
// models/Usuario.php
class Usuario {
    protected $id_usuario;
    protected $nombre_completo;
    protected $email;
    protected $password;
    protected $direccion;
    protected $codigo_postal;
    protected $telefono;
    protected $fecha_registro;

    public function __construct() {}

    public function getId_usuario() {
        return $this->id_usuario;
    }

    public function setId_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function getNombre_completo() {
        return $this->nombre_completo;
    }

    public function setNombre_completo($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
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

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
        return $this;
    }

    public function getFecha_registro() {
        return $this->fecha_registro;
    }

    public function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
        return $this;
    }
}
