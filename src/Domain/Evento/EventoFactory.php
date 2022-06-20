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
        $dataInicioEvento = isset($params["dataInicioEvento"]) ? $params["dataInicioEvento"] : null;
        $dataFimEvento = isset($params["dataFimEvento"]) ? $params["dataFimEvento"] : null;
        $lembrete = isset($params["lembrete"]) ? $params["lembrete"] : null;
        $status = isset($params["status"]) ? $params["status"] : null;

        return new Evento(["idevento" => $idevento,
                            "tituloevento" => $tituloevento,
                            "descevento" => $descevento,
                            "dataInicioEvento" => $dataInicioEvento,
                            "dataFimEvento" => $dataFimEvento,
                            "lembrete" => $lembrete,
                            "status" => $status
                            ]);
    }
}