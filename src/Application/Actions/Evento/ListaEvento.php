<?php

declare(strict_types=1);

namespace App\Application\Actions\Evento;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Evento\EventoRepository;
use App\Domain\Token\TokenService;
use Firebase\JWT\JWT;

final class ListaEvento
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        $er = new EventoRepository(new ConexaoMySql());

        $tokenService = new TokenService();
        $headers = apache_request_headers();
        $infoUsuario = JWT::decode(explode(" ", $headers['Authorization'])[1], $tokenService->getKey(), array_keys(JWT::$supported_algs));

        $res->getBody()->write(
            (string) json_encode($er->buscaEventoUsuario($infoUsuario->id))
        );

        return $res
                ->withHeader("Content-Type", "application/json; charset=utf-8");
    }
}
