<?php
namespace App\Domain\Evento;

use App\Domain\Evento\Evento;

class EventoRepository
{
    public function __construct()
    {
        
    }

    public function buscaEvento()
    {
        $eventos = [];
        
        for ($i=0; $i < 3; $i++) { 
            $params = ["idevento" => $i,
                    "tituloevento" => "Evento" . $i,
                    "descevento" => "Evento" . $i,
                    "dataevento" => $i
            ];
            $eventos[] = new Evento($params);
        }

        return $eventos;
    }
}