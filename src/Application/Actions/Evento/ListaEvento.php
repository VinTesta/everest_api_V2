<?php

declare(strict_types=1);

namespace App\Application\Actions\Evento;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Evento\EventoRepository;
use App\Domain\Token\TokenService;
use Firebase\JWT\JWT;
use App\Domain\Usuario\UsuarioFactory;

final class ListaEvento
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        try {
            $conexao = new ConexaoMySql();
            $conn = $conexao->getConexao();

            $er = new EventoRepository($conexao);
            $usuarioFactory = new UsuarioFactory();
            $conn->beginTransaction();

            $tokenService = new TokenService();

            $infoToken = $tokenService->getTokenBody();
            $result = $er->buscaEventoUsuario($tokenService->criptString(json_encode($infoToken->id), "decrypt"));
            
            $res->getBody()->write(
                (string) json_encode(
                    array("status" => 200, "eventos" => $result, "mensagem" => "Busca realizada com sucesso!")                    
                )
            );

            $conn->commit();
            return $res
                    ->withHeader("Content-Type", "application/json; charset=utf-8");
        } catch (Exception $ex) {
            
            $conn->rollBack();
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
