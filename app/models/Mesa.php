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

    public function CrearMesa()
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

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_identificacion, nombre_cliente, codigo_estado_mesa, descripcion_estado_mesa FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function ActualizarMesa($idMesa, $codigoEstadoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas set codigo_estado_mesa = :codigo_estado_mesa, descripcion_estado_mesa = :descripcion_estado_mesa WHERE id = :id_mesa");
        $consulta->bindValue(':id_mesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_estado_mesa', $codigoEstadoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion_estado_mesa', Mesa::ESTADOS_DESCRIPCION[(int)$codigoEstadoMesa], PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function TienePedidosPendientePorIdPedido($id_pedido)
    {
        $codigoIdentificacion = Pedido::ObtenerCodigoIdentificacion($id_pedido)['codigo_identificacion_mesa'];
        $idMesa = Pedido::ObtenerIdMesa($id_pedido)['id_mesa'];
        $pedidosPendientes = Mesa::ObtenerPedidosSinEntregarDeMesa($idMesa, $codigoIdentificacion);

        if (gettype($pedidosPendientes) != "boolean") {
            return true;
        };

        return $idMesa;
    }

    public static function ObtenerPedidosSinEntregarDeMesa($id_mesa, $codigoIdentificacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM pedidos WHERE id_mesa = :id_mesa AND codigo_identificacion_mesa = :codigo_identificacion_mesa AND codigo_estado_pedido != :codigo_estado_pedido");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_identificacion_mesa', $codigoIdentificacion, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_estado_pedido', Pedido::ENTREGADO, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }
}
