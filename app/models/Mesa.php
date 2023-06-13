<?php

class Mesa
{
    public $id;
    public $codigo_identificacion;
    public $nombre_cliente;
    public $codigo_estado_mesa;
    public $descripcion_estado_mesa;

    const ESPERANDO = 1;
    const COMIENDO = 2;
    const PAGANDO = 3;
    const CERRADA = 4;
    const ESTADOS_DESCRIPCION = [
        1 => "con cliente esperando pedido",
        2 => "con cliente comiendo",
        3 => "con cliente pagando",
        4 => "cerrada"
    ];

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo_identificacion, nombre_cliente, codigo_estado_mesa, descripcion_estado_mesa) VALUES (:codigo_identificacion, :nombre_cliente, :codigo_estado_mesa, :descripcion_estado_mesa)");
        $consulta->bindValue(':codigo_identificacion', $this->codigo_identificacion, PDO::PARAM_STR);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_estado_mesa', Mesa::ESPERANDO, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion_estado_mesa', Mesa::ESTADOS_DESCRIPCION[Mesa::ESPERANDO], PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_identificacion, nombre_cliente, codigo_estado_mesa, descripcion_estado_mesa FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function actualizarMesa($id_mesa, $codigo_estado_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas set codigo_estado_mesa = :codigo_estado_mesa, descripcion_estado_mesa = :descripcion_estado_mesa where id = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_estado_mesa', $codigo_estado_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion_estado_mesa', Mesa::ESTADOS_DESCRIPCION[(int)$codigo_estado_mesa], PDO::PARAM_STR);
        $consulta->execute();
    }
}
