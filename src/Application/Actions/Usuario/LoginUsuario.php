<?php

declare(strict_types=1);

namespace App\Application\Actions\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Usuario\UsuarioRepository;
use App\Domain\Usuario\Usuario;
use App\Domain\Config\ConexaoMySql;

final class LoginUsuario
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        try
        {
            $conexao = new ConexaoMySql();
            $conn = $conexao->getConexao();
            
            $ur = new UsuarioRepository($conexao);
            $conn->beginTransaction();

            $response = $ur->logar((array)$req->getParsedBody());
            
            $res->getBody()->write(
                (string) json_encode(
                    $response
                )
            );
                
            $conn->commit();

            return $res
                    ->withHeader("Content-Type", "application/json");
        }
        catch (Exception $ex)
        {
            $conn->rollBack();
            return $res->getBody()->write(
                        (string) json_encode(
                            array( 
                                "status" => 500,
                                "mensagem" => "Houve um erro ao fazer login!",
                                "erro" => (string) $ex
                            )
                        )
                    )
                    ->withHeader("Content-Type", "application/json");
        }
    }
}
