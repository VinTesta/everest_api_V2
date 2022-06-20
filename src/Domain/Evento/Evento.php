<?php
namespace App\Domain\Evento;

class Evento
{
    #region Parâmetros Privados
    public $idevento;
    public $tituloevento;
    public $descevento;
    public $dataInicioEvento;
    public $dataFimEvento;
    public $lembrete;
    public $status;
    #endregion

    public function __construct($params)
    {
        $this->idevento = isset($params["idevento"]) ? $params["idevento"] : null;
        $this->tituloevento = isset($params["tituloevento"]) ? $params["tituloevento"] : null;
        $this->descevento = isset($params["descevento"]) ? $params["descevento"] : null;
        $this->dataInicioEvento = isset($params["dataInicioEvento"]) ? $params["dataInicioEvento"] : null;
        $this->dataFimEvento = isset($params["dataFimEvento"]) ? $params["dataFimEvento"] : null;
        $this->lembrete = isset($params["lembrete"]) ? $params["lembrete"] : null;
        $this->status = isset($params["status"]) ? $params["status"] : null;
    }
}