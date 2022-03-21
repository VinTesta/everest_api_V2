<?php

declare(strict_types=1);

namespace App\Application\Actions\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Usuario\UsuarioRepository;
use App\Domain\Usuario\Usuario;

final class LoginUsuario
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        try
        {
            $ur = new UsuarioRepository();
            $token = $ur->logar((array)$req->getParsedBody());
            
            $res->getBody()->write(
                (string) json_encode(
                $token
                ?   
                    array( 
                        "status" => 200,
                        "token" => $token,
                        "mensagem" => "Usuário logado com sucesso!"
                    )
                :   
                    array( 
                        "status" => 201,
                        "mensagem" => "Usuário ou senha inválidos!"
                    )
                )
            );

            return $res
                    ->withHeader("Content-Type", "application/json");
        }
        catch (Exception $ex)
        {
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
