<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $tipo = $parametros['tipo'];

    if (isset($usuario) && isset($clave) && isset($tipo)) {
      $nuevoUsuario = new Usuario();
      $nuevoUsuario->usuario = $usuario;
      $nuevoUsuario->clave = $clave;
      $nuevoUsuario->tipo = $tipo;
      $nuevoUsuario->crearUsuario();

      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Datos incompletos"));
    }
    // Creamos el usuario

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
}
