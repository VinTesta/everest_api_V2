<?php

declare(strict_types=1);

namespace App\Application\Actions\Evento;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Evento\EventoRepository;
use App\Domain\Evento\Evento;

final class ListaEvento
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        $er = new EventoRepository();

        $res->getBody()->write(
            json_encode($er->buscaEvento())
        );

        return $res
                ->withHeader("Content-Type", "application/json; charset=utf-8");
    }
}
