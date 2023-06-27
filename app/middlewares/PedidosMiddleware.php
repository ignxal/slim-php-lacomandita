<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/AutentificadorJWT.php";

class PedidosMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $payload = "";
        $response = new Response();

        try {
            $parametros = $request->getParsedBody();

            if (isset($parametros["id_pedido"])) {
                $rolUsuario = AutentificadorJWT::ObtenerRol($request);
                $ultimoEstado = Pedido::ObtenerUltimoEstado($parametros["id_pedido"]);
                $tipoPedido = Pedido::ObtenerTipoPedido($parametros["id_pedido"]);

                if (isset($ultimoEstado) && isset($tipoPedido)) {
                    if (Pedido::UsuarioPuedeModificar($ultimoEstado["codigo_estado_pedido"], $tipoPedido["tipo"], $rolUsuario)) {
                        $response = $handler->handle($request);
                    } else {
                        $payload = json_encode(array("error" => "No estas autorizado"));
                        $response = $response->withStatus(403);
                        $response->getBody()->write($payload);
                    }
                } else {
                    $payload = json_encode(array("error" => "Pedido no existente"));
                    $response = $response->withStatus(400);
                    $response->getBody()->write($payload);
                }
            } else {
                $payload = json_encode(array("error" => "Faltan parametros"));
                $response = $response->withStatus(400);
                $response->getBody()->write($payload);
            }
        } catch (\Throwable $e) {

            $payload = json_encode(array('error' => $e->getMessage()));
            $response = $response->withStatus(400);
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
