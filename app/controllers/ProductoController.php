<?php

require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $precio = $parametros['precio'];
        $tipo = $parametros['tipo'];
        $descripcion = $parametros['descripcion'];
        $estimado = $parametros['estimado_preparacion'];

        if ((isset($precio) &&
            isset($tipo) &&
            isset($descripcion) &&
            isset($estimado))) {

            $nuevoProducto = new Producto();
            $nuevoProducto->tipo = $tipo;
            $nuevoProducto->precio = $precio;
            $nuevoProducto->descripcion = $descripcion;
            $nuevoProducto->estimado_preparacion = $estimado;
            $id = $nuevoProducto->crearProducto();

            $payload = json_encode(array("mensaje" => "Producto creado con exito. Id: " . $id));
        } else {
            $payload = json_encode(array("mensaje" => "Datos incompletos"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("Producto" => $lista), JSON_PRETTY_PRINT);

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

    function ExportarCSV($request, $response, $args)
    {
        $productos = Producto::ObtenerTodos();
        $payload = json_decode(json_encode($productos), true);
        $filename = 'productos.csv';
        $directory = 'export';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filePath = $directory . '/' . $filename;

        $fp = fopen($filePath, 'w');

        if ($fp) {
            foreach ($payload as $row) {
                fputcsv($fp, $row);
            }
            fclose($fp);

            header('Content-Type: application/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);

            return $response->withHeader('Content-Type', 'application/csv');
        } else {
            $payload = json_encode(array("error" => "Error al crear el CSV."));
            $response->getBody()->write($payload);
            $response = $response->withStatus(500);

            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}
