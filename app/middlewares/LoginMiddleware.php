<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LoginMiddleware
{
    public function __invoke(Request $req, RequestHandler $handler): Response
    {
        $res = new Response();
        $parametros = $req->getParsedBody();

        if (isset($parametros["clave"]) && isset($parametros["usuario"])) {
            $res = $handler->handle($req);
        } else {
            $res->getBody()->write(json_encode(array('error' => "Datos incompletos")));
            $res = $res->withStatus(400);
        }

        return $res->withHeader(
            'Content-Type',
            'application/json'
        );
    }
}
