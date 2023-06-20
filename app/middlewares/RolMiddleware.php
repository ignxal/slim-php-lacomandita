<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "AutentificadorJWT.php";

class RolMiddleware
{
    private $role;

    public function __construct($role)
    {
        $this->role = $role;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $payload = "";
        $response = new Response();

        try {
            $claim = AutentificadorJWT::ObtenerRol($request);

            if ($claim == $this->role || $claim == "socio") {
                $response = $handler->handle($request);
            } else {
                $payload = json_encode(array("Error" => "No estas autorizado"));
                $response = $response->withStatus(403);
            }
        } catch (\Throwable $e) {
            $payload = json_encode(array('Error' => $e->getMessage()));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
