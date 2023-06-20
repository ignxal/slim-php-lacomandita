<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'] ?? null;
    $clave = $parametros['clave'] ?? null;
    $tipo = $parametros['tipo'] ?? null;
    $nombre = $parametros['nombre'] ?? null;
    $apellido = $parametros['apellido'] ?? null;

    if (isset($usuario) && isset($clave) && isset($tipo) && isset($nombre) && isset($apellido)) {
      $nuevoUsuario = new Usuario();
      $nuevoUsuario->usuario = $usuario;
      $nuevoUsuario->clave = $clave;
      $nuevoUsuario->tipo = $tipo;
      $nuevoUsuario->nombre = $nombre;
      $nuevoUsuario->apellido = $apellido;
      $nuevoUsuario->crearUsuario();

      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
      $response = $response->withStatus(200);
    } else {
      $payload = json_encode(array("mensaje" => "Datos incompletos"));
      $response = $response->withStatus(400);
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $payload = json_encode(array("mensaje" => "Función Próximamente!"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $payload = json_encode(array("mensaje" => "Función Próximamente!"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Login($request, $response)
  {
    $parametros = $request->getParsedBody();
    $usuario = $parametros['usuario'] ?? null;
    $clave = $parametros['clave'] ?? null;

    if (isset($usuario) && isset($clave)) {
      $claims = Usuario::verificarDatos($usuario, $clave);

      if (isset($claims)) {
        $token = AutentificadorJWT::CrearToken($claims);
        $payload = json_encode(array('ok' => $token));

        $response->getBody()->write($payload);
        $response = $response->withStatus(200);
        return $response
          ->withHeader(
            'Content-Type',
            'application/json'
          );
      } else {
        $response->getBody()->write(json_encode(array('error' => "Datos incorrectos")));
        $response = $response->withStatus(403);
      }
    } else {
      $response->getBody()->write(json_encode(array('error' => "Datos incompletos")));
      $response = $response->withStatus(400);
    }

    return $response
      ->withHeader(
        'Content-Type',
        'application/json'
      );
  }
}
