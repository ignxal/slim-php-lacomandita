<?php

class Pedido
{
    public $id;
    public $id_mesa;
    public $id_producto;
    public $fecha_inicio;
    public $codigo_estado_pedido;
    public $descripcion_estado_pedido;
    public $descripcion;
    public $tipo;
    // codigo de agrupacion de pedido 
    const NUEVO = 1;
    const EN_PREPARACION = 2;
    const LISTO = 3;
    const ENTREGADO = 4;

    const ESTADOS_DESCRIPCION = [
        1 => "nuevo",
        2 => "en preparación",
        3 => "listo para servir",
        4 => "entregado"
    ];
    // ... pedidos AS p WHERE p.id = ... 

    function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos(id_producto, id_mesa, fecha_inicio, codigo_estado_pedido, descripcion_estado_pedido) VALUES (:id_producto, :id_mesa, :fecha_inicio, :codigo_estado_pedido, :descripcion_estado_pedido)");
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_inicio', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $consulta->bindvalue(':codigo_estado_pedido', Pedido::NUEVO, PDO::PARAM_INT);
        $consulta->bindvalue(':descripcion_estado_pedido', Pedido::ESTADOS_DESCRIPCION[Pedido::NUEVO], PDO::PARAM_STR);
        $consulta->execute();
        $id = $objAccesoDatos->obtenerUltimoId();

        Mesa::actualizarMesa($this->id_mesa, Mesa::ESPERANDO);

        return $id;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.id_mesa, p.id_producto, p.fecha_inicio, p.codigo_estado_pedido, p.descripcion_estado_pedido, pr.descripcion, pr.tipo 
            FROM pedidos AS p
            INNER JOIN productos AS pr ON p.id_producto = pr.id");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}
