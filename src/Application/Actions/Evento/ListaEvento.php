<?php

declare(strict_types=1);

namespace App\Application\Actions\Evento;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Evento\EventoRepository;

final class ListaEvento
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        $er = new EventoRepository(new ConexaoMySql());

        $res->getBody()->write(
            (string) json_encode($er->buscaEvento())
        );

        return $res
                ->withHeader("Content-Type", "application/json; charset=utf-8");
    }
}
