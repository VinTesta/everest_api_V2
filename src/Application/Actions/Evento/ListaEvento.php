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
        try {
            $er = new EventoRepository(new ConexaoMySql());

            $tokenService = new TokenService();

            $infoToken = $tokenService->getTokenHeader();

            $result = $er->buscaEventoUsuario($infoToken->id);
            $res->getBody()->write(
                (string) json_encode($result)
            );

            return $res
                    ->withHeader("Content-Type", "application/json; charset=utf-8");
        } catch (Exception $ex) {
            return $res->getBody()->write(
                (string) json_encode(
                    array( 
                        "status" => 500,
                        "mensagem" => "Houve um erro ao buscar os eventos!",
                        "erro" => (string) $ex
                    )
                )
            )
            ->withHeader("Content-Type", "application/json");
        }
        
    }
}
