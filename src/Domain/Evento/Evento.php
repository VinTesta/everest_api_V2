<?php
namespace App\Domain\Evento;

class Evento
{
    #region Parâmetros Privados
    public $_idevento;
    public $_tituloevento;
    public $_descevento;
    public $_dataevento;
    #endregion

    public function __construct($params)
    {
        $this->_idevento = isset($params["idevento"]) ? $params["idevento"] : null;
        $this->_tituloevento = isset($params["tituloevento"]) ? $params["tituloevento"] : null;
        $this->_descevento = isset($params["descevento"]) ? $params["descevento"] : null;
        $this->_dataevento = isset($params["dataevento"]) ? $params["dataevento"] : null;
    }
}