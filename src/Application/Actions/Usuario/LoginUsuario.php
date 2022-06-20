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
use Exception;

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

            $body = (array)$req->getParsedBody();
            $response = $ur->logar($body);
            
            if($response["usuario"]->emailusuario != null)
            {
                $token = $tokenService->geraTokenAcesso(["id" => $tokenService->criptString(json_encode($response["usuario"]->idusuario), "encrypt"), 
                                                        "emailusuario" => $response["usuario"]->emailusuario, 
                                                        "nomeusuario" => $response["usuario"]->nomeusuario]);

                $historicoUtilizacao = $historicoFactory->geraHistorico(array("titulo" => 3, "descricao" => "O usuário se logou pelo sistema", "tipo" => 1));      
                // Inserir histórico do usuario
                $historicoUtilizacao = $hr->insert($historicoUtilizacao);
                $historicoUsuario = $hr->insertHistoricoUsuario($historicoUtilizacao->idhistorico, $response["usuario"]->idusuario);                                              
            }       
            else
            {
                throw new Exception("Login ou senha incorretos!", 1);
            }

            $res->getBody()->write(
                (string) json_encode(
                    array( 
                            "token" => $token,
                            "usuario" => [
                                "idusuario" => $response["usuario"]->idusuario,
                                "emailusuario" => $response["usuario"]->emailusuario,
                                "nomeusuario" => $response["usuario"]->nomeusuario
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
            $res->getBody()->write(
                        (string) json_encode(
                            array( 
                                "status" => 500,
                                "mensagem" => "Houve um erro ao fazer login!",
                                "erro" => (string) $ex
                            )
                        )
                    );
            return $res->withHeader("Content-Type", "application/json");
        }
    }
}
