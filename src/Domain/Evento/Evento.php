<?php
namespace App\Domain\Evento;

class Evento
{
    #region Parâmetros Privados
    public $idevento;
    public $tituloevento;
    public $descevento;
    public $dataevento;
    #endregion

    public function __construct($params)
    {
        $this->idevento = isset($params["idevento"]) ? $params["idevento"] : null;
        $this->tituloevento = isset($params["tituloevento"]) ? $params["tituloevento"] : null;
        $this->descevento = isset($params["descevento"]) ? $params["descevento"] : null;
        $this->dataevento = isset($params["dataevento"]) ? $params["dataevento"] : null;
    }
}