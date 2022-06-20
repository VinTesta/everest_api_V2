<?php
namespace App\Domain\Usuario;

class Usuario
{
    #region Parâmetros Privados
    public $idusuario;
    public $nomeusuario;
    public $emailusuario;
    public $senhausuario;
    public $status;
    #endregion

    public function __construct($params)
    {
        $this->idusuario = isset($params["idusuario"]) ? $params["idusuario"] : null;
        $this->nomeusuario = isset($params["nomeusuario"]) ? $params["nomeusuario"] : null;
        $this->emailusuario = isset($params["emailusuario"]) ? $params["emailusuario"] : null;
        $this->senhausuario = isset($params["senhausuario"]) ? $params["senhausuario"] : null;
        $this->status = isset($params["status"]) ? $params["status"] : null;
    }
}