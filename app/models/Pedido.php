<?php

use function PHPSTORM_META\type;

class Pedido
{
    public $id;
    public $id_mesa;
    public $id_producto;
    public $codigo_identificacion_mesa;
    public $fecha_inicio;
    public $codigo_estado_pedido;
    public $descripcion_estado_pedido;
    public $descripcion;
    public $tipo;

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

    function CrearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos(id_producto, id_mesa, codigo_identificacion_mesa, fecha_inicio, codigo_estado_pedido, descripcion_estado_pedido) VALUES (:id_producto, :id_mesa, :codigo_identificacion_mesa, :fecha_inicio, :codigo_estado_pedido, :descripcion_estado_pedido)");
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_identificacion_mesa', $this->codigo_identificacion_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_inicio', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $consulta->bindvalue(':codigo_estado_pedido', Pedido::NUEVO, PDO::PARAM_INT);
        $consulta->bindvalue(':descripcion_estado_pedido', Pedido::ESTADOS_DESCRIPCION[Pedido::NUEVO], PDO::PARAM_STR);
        $consulta->execute();
        $id = $objAccesoDatos->obtenerUltimoId();

        Mesa::ActualizarMesa($this->id_mesa, Mesa::ESPERANDO);

        return $id;
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.id_mesa, p.codigo_identificacion_mesa, p.id_producto, p.fecha_inicio, p.codigo_estado_pedido, p.descripcion_estado_pedido, pr.descripcion, pr.tipo 
            FROM pedidos AS p
            INNER JOIN productos AS pr ON p.id_producto = pr.id");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ActualizarPedidoPorId($id_pedido)
    {
        $ultimoEstado = Pedido::ObtenerUltimoEstado($id_pedido);

        if (!isset($ultimoEstado)) {
            $ultimoEstado = Pedido::NUEVO;
        } else {
            $ultimoEstado = $ultimoEstado['codigo_estado_pedido'];
        }

        $nuevoCodigoEstado = $ultimoEstado + 1;

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET codigo_estado_pedido = :codigo_estado_pedido, descripcion_estado_pedido = :descripcion_estado_pedido WHERE id = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_estado_pedido', $nuevoCodigoEstado, PDO::PARAM_INT);
        $consulta->bindvalue(':descripcion_estado_pedido', Pedido::ESTADOS_DESCRIPCION[$nuevoCodigoEstado], PDO::PARAM_STR);
        $consulta->execute();

        $resultadoPendiente = Mesa::TienePedidosPendientePorIdPedido($id_pedido);

        if ($nuevoCodigoEstado == Pedido::ENTREGADO && gettype($resultadoPendiente) == "integer") {
            return Mesa::ActualizarMesa($resultadoPendiente, Mesa::COMIENDO);
        }

        return true;
    }

    public static function ObtenerUltimoEstado($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT codigo_estado_pedido FROM pedidos WHERE id = :id_pedido');
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    public static function ObtenerCodigoIdentificacion($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT codigo_identificacion_mesa FROM pedidos WHERE id = :id_pedido');
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    public static function ObtenerIdMesa($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id_mesa FROM pedidos WHERE id = :id_pedido');
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    public static function ObtenerTipoPedido($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pr.tipo 
            FROM pedidos AS p 
            INNER JOIN productos AS pr ON p.id_producto = pr.id
            WHERE p.id = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch();
    }

    public static function UsuarioPuedeModificar($ultimoEstado, $tipoPedido, $rolUsuario)
    {
        if ($rolUsuario == "socio") {
            return true;
        } else if ($rolUsuario == "mozo" && $ultimoEstado == 3) {
            return true;
        } else if ($ultimoEstado == 1 || $ultimoEstado == 2) {
            if ($rolUsuario == "cocinero" && $tipoPedido == "Comida")
                return true;
            else if ($rolUsuario == "bartender" && $tipoPedido == "Trago")
                return true;
            else if ($rolUsuario == "cervecero" && $tipoPedido == "Cerveza")
                return true;

            return false;
        }
        return false;
    }
}
