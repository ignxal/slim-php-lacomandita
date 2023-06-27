<?php
class Producto
{
    public $id;
    public $tipo;
    public $precio;
    public $descripcion;
    public $estimado_preparacion;

    public function CrearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productos (tipo, precio, descripcion, estimado_preparacion) VALUES (:tipo,:precio,:descripcion,:estimado_preparacion)');
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':estimado_preparacion', date("H:i:s", strtotime($this->estimado_preparacion)), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, precio, descripcion, estimado_preparacion FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }
}
