<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $tipo;
    public $nombre;
    public $apellido;
    public $fechaAlta;
    public $fechaModificacion;
    public $fechaBaja;
    public $activo;

    public function CrearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, tipo, nombre, apellido, fechaAlta, fechaModificacion, fechaBaja, activo) VALUES (:usuario, :clave, :tipo, :nombre, :apellido, :fechaAlta, :fechaModificacion, :fechaBaja, :activo)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $consulta->bindValue(':fechaModificacion', "", PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', "", PDO::PARAM_STR);
        $consulta->bindValue(':activo', true, PDO::PARAM_BOOL);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, tipo FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function verificarDatos($usuario, $clave)
    {
        $usuarioRecibido = self::ObtenerUsuario($usuario);

        if (isset($usuarioRecibido) && (password_verify($clave, $usuarioRecibido->clave) ||  $usuarioRecibido->clave == $clave)) {
            return array(
                "tipo" => $usuarioRecibido->tipo,
                "id" => $usuarioRecibido->id
            );
        }

        return null;
    }

    public static function ObtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, tipo FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}
