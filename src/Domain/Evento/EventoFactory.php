<?php
namespace App\Domain\Evento;

class EventoFactory
{
    public function __construct()
    {
        
    }

    public function geraEvento($params)
    {
        $idevento = isset($params["idevento"]) ? $params["idevento"] : null;
        $tituloevento = isset($params["tituloevento"]) ? $params["tituloevento"] : null;
        $descevento = isset($params["descevento"]) ? $params["descevento"] : null;
        $dataevento = isset($params["dataevento"]) ? $params["dataevento"] : null;
        $status = isset($params["status"]) ? $params["status"] : null;

        return new Evento(["idevento" => $idevento,
                            "tituloevento" => $tituloevento,
                            "descevento" => $descevento,
                            "dataevento" => $dataevento,
                            "status" => $status
                            ]);
    }
}