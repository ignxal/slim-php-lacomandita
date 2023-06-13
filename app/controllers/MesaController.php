<?php

require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo_identificacion = $parametros['codigo_identificacion'];
        $nombre_cliente = $parametros['nombre_cliente'];

        if ((isset($codigo_identificacion) &&
            isset($nombre_cliente) && strlen($codigo_identificacion) == 5)) {

            $nuevaMesa = new Mesa();
            $nuevaMesa->codigo_identificacion = $codigo_identificacion;
            $nuevaMesa->nombre_cliente = $nombre_cliente;
            $id = $nuevaMesa->crearMesa();

            $payload = json_encode(array("mensaje" => "Mesa creada con exito. Id: " . $id));
        } else {
            $payload = json_encode(array("mensaje" => "Datos incompletos"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Mesa" => $lista), JSON_PRETTY_PRINT);

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
