<?php

// models/DireccionDAO.php
include_once("Direccion.php");
include_once("config/db.php");

class DireccionDAO {
    public static function agregarDireccion(Direccion $direccion) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO Proyecto1.Direccion (id_cliente, direccion, codigo_postal) VALUES (?, ?, ?)");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return false;
        }

        $id_cliente = $direccion->getId_cliente();
        $dir = $direccion->getDireccion();
        $cp = $direccion->getCodigo_postal();

        if (!$stmt->bind_param("isi", $id_cliente, $dir, $cp)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return false;
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }

        $stmt->close();
        $con->close();
        return true;
    }

    public static function obtenerDireccionesPorUsuario($id_usuario) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM Proyecto1.Direccion WHERE id_cliente = ?");
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $con->error);
            return [];
        }

        if (!$stmt->bind_param("i", $id_usuario)) {
            error_log("Error al enlazar parámetros: " . $stmt->error);
            return [];
        }

        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        $direcciones = [];

        while ($data = $result->fetch_assoc()) {
            $direccion = new Direccion();
            $direccion->setId_direccion($data['id_direccion']);
            $direccion->setId_cliente($data['id_cliente']);
            $direccion->setDireccion($data['direccion']);
            $direccion->setCodigo_postal($data['codigo_postal']);
            $direcciones[] = $direccion;
        }

        $stmt->close();
        $con->close();

        return $direcciones;
    }
}
