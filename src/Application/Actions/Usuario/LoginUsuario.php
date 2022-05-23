<?php

declare(strict_types=1);

namespace App\Application\Actions\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Usuario\UsuarioRepository;
use App\Domain\Usuario\Usuario;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Token\TokenService;
use App\Domain\Historico\Historico;
use App\Domain\Historico\HistoricoFactory;
use App\Domain\Historico\HistoricoRepository;

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
            $conn->beginTransaction();
            
            $hr = new HistoricoRepository($conexao);
            $historicoFactory = new HistoricoFactory($conexao);
            $ur = new UsuarioRepository($conexao);
            $tokenService = new TokenService();

            $response = $ur->logar((array)$req->getParsedBody());

            $token = $tokenService->geraTokenAcesso(["id" => $response["usuario"]->idusuario, 
                                                    "email" => $response["usuario"]->emailusuario, 
                                                    "nome" => $response["usuario"]->nomeusuario, 
                                                    "perfil" => $response["perfil"]]);

            $historicoUtilizacao = $historicoFactory->geraHistorico(array("titulo" => 3, "descricao" => "O usuário se logou pelo sistema", "tipo" => 1));      
            // Inserir histórico do usuario
            $historicoUtilizacao = $hr->insert($historicoUtilizacao);
            $historicoUsuario = $hr->insertHistoricoUsuario($historicoUtilizacao->idhistorico, $response["usuario"]->idusuario);                                              
                        
            $res->getBody()->write(
                (string) json_encode(
                    array( 
                            "token" => $token,
                            "usuario" => [
                                "idusuario" => $response["usuario"]->idusuario,
                                "emailusuario" => $response["usuario"]->emailusuario,
                                "perfil" => $response["perfil"]
                            ],
                            "status" => 200,
                            "mensagem" => "Usuário logado com sucesso!")
                )
            );
                
            $conn->commit();
            // $conn->rollBack();
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
