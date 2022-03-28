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
        $params = ["idevento" => 1,
                "tituloevento" => "Evento 1",
                "descevento" => "Evento 1",
                "dataevento" => 1
        ];
        $eventos = new Evento($params);
        return $eventos;
    }
}