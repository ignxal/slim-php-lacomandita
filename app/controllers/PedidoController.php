<?php

require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_producto = $parametros['id_producto'];
        $id_mesa = $parametros['id_mesa'];

        if ((isset($id_producto) &&
            isset($id_mesa))) {

            $nuevoPedido = new Pedido();
            $nuevoPedido->id_producto = $id_producto;
            $nuevoPedido->id_mesa = $id_mesa;
            $id = $nuevoPedido->crearPedido();

            $payload = json_encode(array("mensaje" => "Pedido creado con exito. Id: " . $id));
        } else {
            $payload = json_encode(array("mensaje" => "Datos incompletos"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
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
}
