<?php
namespace App\Domain\Perfil;

class Perfil
{
    #region Parâmetros Privados
    public $idperfil;
    public $nomeperfil;
    public $setorperfil;
    public $status;
    #endregion

    public function __construct($params)
    {
        $this->idperfil = isset($params["idperfil"]) ? $params["idperfil"] : null;
        $this->nomeperfil = isset($params["nomeperfil"]) ? $params["nomeperfil"] : null;
        $this->setorperfil = isset($params["setorperfil"]) ? $params["setorperfil"] : null;
        $this->status = isset($params["status"]) ? $params["status"] : null;
    }
}