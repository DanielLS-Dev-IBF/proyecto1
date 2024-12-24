<?php
// models/Usuario.php

class Usuario {
    private $id_usuario;
    private $nombre_completo;
    private $email;
    private $password;
    private $telefono;
    private $fecha_registro;
    private $rol; // <--- NUEVO ATRIBUTO

    // Constructor vacío
    public function __construct() {
        // Puedes inicializar valores predeterminados si es necesario
    }

    // Getters
    public function getId_usuario() {
        return $this->id_usuario;
    }

    public function getNombre_completo() {
        return $this->nombre_completo;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getFecha_registro() {
        return $this->fecha_registro;
    }

    public function getRol() {
        return $this->rol; // <--- GETTER PARA ROL
    }

    // Setters
    public function setId_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setNombre_completo($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    public function setRol($rol) {
        $this->rol = $rol; // <--- SETTER PARA ROL
    }
}
?>
