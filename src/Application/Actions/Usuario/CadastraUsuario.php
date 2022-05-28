<?php

declare(strict_types=1);

namespace App\Application\Actions\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Usuario\UsuarioRepository;
use App\Domain\Usuario\Usuario;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Perfil\PerfilRepository;
use App\Domain\Token\TokenService;
use Firebase\JWT\JWT;
use App\Domain\Historico\Historico;
use App\Domain\Historico\HistoricoFactory;
use App\Domain\Historico\HistoricoRepository;
use Exception;

final class CadastraUsuario
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

            $tokenService = new TokenService();
            $ur = new UsuarioRepository($conexao);
            $pr = new PerfilRepository($conexao);
            $hr = new HistoricoRepository($conexao);
            $historicofactory = new HistoricoFactory();
            $requestData = (array)$req->getParsedBody();
            $requestHeader = $tokenService->getTokenBody();

            // Adiciona o usuario
            $response = $ur->cadastrarUsuario($requestData);

            if($response["usuario"]->idusuario != null)
            {
                $perfisInsert = $pr->buscaPerfil(array("nomeperfil" => "default"));
                // Adiciona o perfil do usuario
                $responsePerfilUsuario = $pr->atualizaPerfilUsuario($perfisInsert, $response["usuario"]);
                // Busca o perfil do usuario
                $newPerfilUsuario = $pr->buscaPerfilUsuario($response["usuario"]);
                
                $historicoUtilizacao = $historicofactory->geraHistorico(array("titulo" => 1, "descricao" => "O usuário criou uma conta no sistema", "tipo" => 1));
                // Inserir histórico do usuario
                $historicoUtilizacao = $hr->insert($historicoUtilizacao);
                $historicoUsuario = $hr->insertHistoricoUsuario($historicoUtilizacao->idhistorico, $response["usuario"]->idusuario);
                $token = JWT::encode(
                            ["id" => $response["usuario"]->idusuario, 
                            "email" => $response["usuario"]->emailusuario, 
                            "nome" => $response["usuario"]->nomeusuario, 
                            "perfil" => $tokenService->criptString(json_encode($newPerfilUsuario), "encrypt")],
                            $tokenService->getKey(),
                            "HS256"
                );     
            }
            else
            {
                throw new Exception("Dados ínvalidos!");
            }
                   
            $res->getBody()->write(
                (string) json_encode(
                    $response["usuario"]->idusuario != null 
                    ?
                        array( 
                            "token" => $token,
                            "usuario" => [
                                "emailusuario" => $response["usuario"]->emailusuario
                            ],
                            "status" => 200,
                            "mensagem" => "Usuario cadastrado com sucesso!")
                    :
                        array( 
                            "token" => "",
                            "usuario" => [],
                            "status" => 201,
                            "mensagem" => "Houve um erro ao cadastrar o usuario")
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
                                "mensagem" => "Houve um erro ao cadastrar o usuario!",
                                "erro" => (string) $ex->getMessage()
                            )
                        )
                    );
            return $res->withHeader("Content-Type", "application/json");
        }
    }
}
