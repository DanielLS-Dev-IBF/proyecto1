<?php

include_once("Producto.php");

class Bowl extends Producto{
    
    public function __construct($id_producto, $nombre, $descripcion, $precio_base, $img){
        parent::__construct($id_producto, $nombre, $descripcion, $precio_base);
        $this->img = $img;
    }

}
