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
        $perfilusuario = isset($params["perfilusuario"]) ? $params["perfilusuario"] : null;
        $statususuario = isset($params["statususuario"]) ? $params["statususuario"] : null;

        return new Usuario(["idusuario" => $idusuario,
                            "nomeusuario" => $nomeusuario,
                            "emailusuario" => $emailusuario,
                            "senhausuario" => $senhausuario,
                            "perfilusuario" => $perfilusuario,
                            "statususuario" => $statususuario
                            ]);
    }
}