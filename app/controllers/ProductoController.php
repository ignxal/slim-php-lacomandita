<?php

require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

use Dompdf\Dompdf;

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

            if ($tipo == "Comida" || $tipo == "Trago" || $tipo == "Cerveza") {
                $nuevoProducto = new Producto();
                $nuevoProducto->tipo = $tipo;
                $nuevoProducto->precio = $precio;
                $nuevoProducto->descripcion = $descripcion;
                $nuevoProducto->estimado_preparacion = $estimado;
                $id = $nuevoProducto->crearProducto();

                $payload = json_encode(array("mensaje" => "Producto creado con exito. Id: " . $id));
            } else {
                $payload = json_encode(array("error" => "Tipo inválido"));
                $response = $response->withStatus(400);
            }
        } else {
            $payload = json_encode(array("error" => "Datos incompletos"));
            $response = $response->withStatus(400);
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
        $response = $response->withStatus(501);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    function BorrarUno($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Función Próximamente!"));

        $response->getBody()->write($payload);
        $response = $response->withStatus(501);
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

    function ImportarCSV($request, $response, $args)
    {
        $archivo = $request->getUploadedFiles()['archivo'];
        $payload = "";
        $directory = 'import';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (isset($archivo)) {
            $extension = pathinfo($archivo->getClientFileName(), PATHINFO_EXTENSION);

            if ($extension === 'csv') {
                $filename = uniqid('csv_', true) . '.csv';
                $path = $directory . '/' . $filename;
                $archivo->moveTo($path);
                $payload = json_encode(Producto::LeerArchivo($path));
            } else {
                $payload = json_encode(array('error' => 'Extensión inválida. Solo se permiten CSV.'));
                $response = $response->withStatus(400);
            }
        } else {
            $payload = json_encode(array('error' => 'No se encontró un archivo.'));
            $response = $response->withStatus(400);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function ExportarPDF($request, $response, $args)
    {
        $dompdf = new Dompdf();

        $array = Producto::ObtenerTodos();
        $stringHTML = Producto::TraerTablaHtml($array);
        $dompdf->loadHtml($stringHTML);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        $directory = "export";
        $filePath = $directory . "/exported_pdf.pdf";

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($filePath, $pdfContent);
        $payload = json_encode(array('ok' => 'Pdf exportado y guardado exitosamente.'));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
