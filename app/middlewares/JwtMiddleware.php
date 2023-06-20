<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "./models/AutentificadorJWT.php";

class jwtCheckMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = "";
        $payload = "";
        $response = new Response();
        $esValido = false;

        try {
            $header = $request->getHeaderLine("Authorization");

            if (isset($header)) {
                $token = trim(explode("Bearer", $header)[1]);
            }

            $esValido = AutentificadorJWT::VerificarToken($token);

            if ($esValido) {
                $payload = json_encode(array("válido" => "true"));
                $response = $response->withStatus(200);
                $response = $handler->handle($request);
            } else {
                $payload = json_encode(array("válido" => "false"));
                $response = $response->withStatus(400);
                $response = $handler->handle($request);
            }
        } catch (\Throwable $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus(400);
            return $response->withHeader('Content-Type', 'application/json');
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
