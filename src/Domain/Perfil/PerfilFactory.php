<?php
namespace App\Domain\Perfil;

class PerfilFactory
{
    public function __construct()
    {
        
    }

    public function geraPerfil($params)
    {
        $idperfil = isset($params["idperfil"]) ? $params["idperfil"] : null;
        $nomeperfil = isset($params["nomeperfil"]) ? $params["nomeperfil"] : null;
        $setorperfil = isset($params["setorperfil"]) ? $params["setorperfil"] : null;
        $status = isset($params["status"]) ? $params["status"] : null;

        return new Perfil(["idperfil" => $idperfil,
                            "nomeperfil" => $nomeperfil,
                            "setorperfil" => $setorperfil,
                            "status" => $status
                            ]);
    }
}