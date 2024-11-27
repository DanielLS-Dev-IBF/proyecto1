<?php
//Clase Padre Producto
abstract class Producto{
    protected $id_producto;
    protected $nombre;
    protected $descripcion;
    protected $img;
    protected $precio_base;

    //Constructor de la clase Producto
    public function __construct($id_producto, $nombre, $descripcion, $precio_base){
        $this->id_producto = $id_producto;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio_base = $precio_base;
    }

    /**
     * Get the value of id_producto
     */ 
    public function getId_producto()
    {
        return $this->id_producto;
    }

    /**
     * Set the value of id_producto
     */
    public function setId_producto($id_producto)
    {
        $this->id_producto = $id_producto;

        return $this;
    }
    
    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of descripcion
     */ 
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     */ 
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of precio_base
     */ 
    public function getPrecio_base()
    {
        return $this->precio_base;
    }

    /**
     * Set the value of precio_base
     */ 
    public function setPrecio_base($precio_base)
    {
        $this->precio_base = $precio_base;

        return $this;
    }
    

    /**
     * Get the value of img
     */ 
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set the value of img
     *
     * @return  self
     */ 
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }
}