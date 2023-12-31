<?php

require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_producto = $parametros['id_producto'] ?? null;
        $id_mesa = $parametros['id_mesa'] ?? null;
        $codigo_identificacion = $parametros['codigo_identificacion'] ?? null;

        if ((isset($id_producto) &&
            isset($id_mesa) &&
            isset($codigo_identificacion))) {

            $nuevoPedido = new Pedido();
            $nuevoPedido->id_producto = $id_producto;
            $nuevoPedido->id_mesa = $id_mesa;
            $nuevoPedido->codigo_identificacion_mesa = $codigo_identificacion;
            $id = $nuevoPedido->CrearPedido();

            $payload = json_encode(array("mensaje" => "Pedido creado con exito. Id: " . $id));
        } else {
            $payload = json_encode(array("mensaje" => "Datos incompletos"));
            $response = $response->withStatus(400);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::ObtenerTodos();
        $payload = json_encode(array("Pedido" => $lista), JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function ModificarUno($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Función Próximamente!"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function BorrarUno($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Función Próximamente!"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function ActualizarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id_pedido'];

        if (isset($id)) {
            Pedido::ActualizarPedidoPorId($id);
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        } else {
            $payload = json_encode(array('error' => "Datos incompletos"));
            $response = $response->withStatus(400);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
