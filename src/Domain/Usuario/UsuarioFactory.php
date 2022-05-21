<?php
namespace App\Domain\Usuario;

class UsuarioFactory
{
    public function __construct()
    {
        
    }

    public function geraUsuario($params)
    {
        $idusuario = isset($params["idusuario"]) ? $params["idusuario"] : null;
        $nomeusuario = isset($params["nomeusuario"]) ? $params["nomeusuario"] : null;
        $emailusuario = isset($params["emailusuario"]) ? $params["emailusuario"] : null;
        $senhausuario = isset($params["senhausuario"]) ? $params["senhausuario"] : null;
        $status = isset($params["status"]) ? $params["status"] : null;

        return new Usuario(["idusuario" => $idusuario,
                            "nomeusuario" => $nomeusuario,
                            "emailusuario" => $emailusuario,
                            "senhausuario" => $senhausuario,
                            "status" => $status
                            ]);
    }
}