<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $tipo;
    // fecha baja alta, modificacion, nombre apellido y activo o no activo
    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, tipo) VALUES (:usuario, :clave, :tipo)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, tipo FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function verificarDatos($usuario, $clave)
    {
        $usuarioRecibido = self::obtenerUsuario($usuario);

        if (isset($usuarioRecibido) && (password_verify($clave, $usuarioRecibido->clave) ||  $usuarioRecibido->clave == $clave)) {
            return array(
                "tipo" => $usuarioRecibido->tipo,
                "id" => $usuarioRecibido->id
            );
        }

        return null;
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, tipo FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}
