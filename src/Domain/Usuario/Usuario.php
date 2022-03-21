<?php
namespace App\Domain\Usuario;

class Usuario
{
    #region Parâmetros Privados
    public $_idusuario;
    public $_nomeusuario;
    public $_emailusuario;
    public $_senhausuario;
    public $_perfilusuario;
    public $_statususuario;
    #endregion

    public function __construct($params)
    {
        $this->_idusuario = isset($params["idusuario"]) ? $params["idusuario"] : null;
        $this->_nomeusuario = isset($params["nomeusuario"]) ? $params["nomeusuario"] : null;
        $this->_emailusuario = isset($params["emailusuario"]) ? $params["emailusuario"] : null;
        $this->_senhausuario = isset($params["senhausuario"]) ? $params["senhausuario"] : null;
        $this->_perfilusuario = isset($params["perfilusuario"]) ? $params["perfilusuario"] : null;
        $this->_statususuario = isset($params["statususuario"]) ? $params["statususuario"] : null;
    }
}