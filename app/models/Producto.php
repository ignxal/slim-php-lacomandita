<?php
class Producto
{
    public $id;
    public $tipo;
    public $precio;
    public $descripcion;
    public $estimado_preparacion;

    public function CrearProducto()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productos (tipo, precio, descripcion, estimado_preparacion) VALUES (:tipo, :precio, :descripcion, :estimado_preparacion)');
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':estimado_preparacion', date("H:i:s", strtotime($this->estimado_preparacion)), PDO::PARAM_STR);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        } catch (PDOException $e) {
            throw new Exception('Error al crear producto: ' . $e->getMessage());
        }
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo, precio, descripcion, estimado_preparacion FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function LeerArchivo($archivo)
    {
        $flag = false;
        $addedProducts = 0;

        if (is_readable($archivo)) {
            $handle = fopen($archivo, 'r');

            if ($handle) {
                while (!feof($handle)) {
                    $array = fgetcsv($handle);

                    if ($array !== false) {
                        $producto = new Producto();
                        $producto->id = $array[0];
                        $producto->precio = $array[1];
                        $producto->descripcion = $array[2];
                        $producto->tipo = $array[3];
                        $producto->estimado_preparacion = $array[4];
                        echo $producto->id;
                        echo $producto->precio;
                        echo $producto->descripcion;
                        echo $producto->tipo;
                        echo $producto->estimado_preparacion;
                        if (!Producto::BuscarUno($producto->id, $producto->descripcion)) {
                            $producto->CrearProducto();
                            $flag = true;
                            $addedProducts++;
                        }
                    }
                }

                fclose($handle);
            }
        }

        if ($flag) {
            return array("ok" => "Se agregaron correctamente $addedProducts productos no existentes.");
        } else {
            return array("ok" => "No se agregó ningún producto nuevo.");
        }
    }

    public static function BuscarUno($id_producto, $desc)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 1 FROM productos WHERE id = :id OR descripcion = :descripcion");
        $consulta->bindValue(':id', $id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $desc, PDO::PARAM_STR);
        $consulta->execute();

        $result = $consulta->fetch();
        return $result !== false;
    }
}
