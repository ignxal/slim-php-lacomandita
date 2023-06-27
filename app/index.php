<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

require_once("./middlewares/LoginMiddleware.php");
require_once("./middlewares/RolMiddleware.php");
require_once("./middlewares/JwtMiddleware.php");
require_once("./middlewares/PedidosMiddleware.php");

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos')->add(new RolMiddleware("socio"));
  $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new RolMiddleware("socio"));
  $group->post('/login', \UsuarioController::class . ':Login')->add(new LoginMiddleware);
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new RolMiddleware("socio"));
  $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(new RolMiddleware("mozo"));
  $group->get('/exportarCSV', \ProductoController::class . ':ExportarCSV')->add(new RolMiddleware("socio"));
  $group->post('/importarCSV', \ProductoController::class . ':ImportarCSV')->add(new RolMiddleware("socio"));
})->add(new JwtCheckMiddleware());

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(new RolMiddleware("mozo"));
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(new RolMiddleware("mozo"));
})->add(new JwtCheckMiddleware());

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new RolMiddleware("mozo"));
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->post('/actualizar', \PedidoController::class . ':ActualizarPedido')->add(new PedidosMiddleware());
})->add(new JwtCheckMiddleware());

$app->get('[/]', function (Request $request, Response $response) {
  $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));

  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
