<?php
namespace App\Domain\Historico;

class Historico
{
    #region Parâmetros Privados
    public $idhistorico;
    public $data;
    public $titulo;
    public $descricao;
    public $tipo;
    #endregion

    public function __construct($params)
    {
        $this->idhistorico = isset($params["idhistorico"]) ? $params["idhistorico"] : null;
        $this->data = isset($params["data"]) ? $params["data"] : null;
        $this->titulo = isset($params["titulo"]) ? $params["titulo"] : null;
        $this->descricao = isset($params["descricao"]) ? $params["descricao"] : null;
        $this->tipo = isset($params["tipo"]) ? $params["tipo"] : null;
    }
}