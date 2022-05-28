<?php

declare(strict_types=1);

namespace App\Application\Actions\Usuario;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Usuario\UsuarioRepository;
use App\Domain\Usuario\UsuarioFactory;
use App\Domain\Usuario\Usuario;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Perfil\PerfilRepository;
use Firebase\JWT\JWT;
use App\Domain\Historico\HistoricoFactory;
use App\Domain\Historico\HistoricoRepository;
use App\Domain\Token\TokenService;
use Exception;

final class AlterarUsuario
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

            $perfilRepository = new PerfilRepository($conexao);
            $usuarioRepository = new UsuarioRepository($conexao);
            $historicoRepository = new HistoricoRepository($conexao);
            $tokenService = new TokenService();
            $usuarioFactory =  new UsuarioFactory();
            $historicoFactory = new HistoricoFactory();

            $bodyReq = (array)$req->getParsedBody();

            $tokenBody = $tokenService->getTokenBody();
            $idusuario = $tokenService->criptString($tokenBody->id, "decrypt");

            $usuario_alteracao = $usuarioFactory->geraUsuario($bodyReq["altUsuario"]);

            if(isset($usuario_alteracao->idusuario) && ($perfilRepository->validaPermissaoUsuario($idusuario, ["altUsuario"]) || $usuario_alteracao->idusuario == $idusuario))
            {
                // PERMITE ALTERAR OUTROS USUÁRIOS
                $alterado = $usuarioRepository->update($usuario_alteracao, $usuario_alteracao->idusuario);
                
                $historicoUtilizacao = $historicoFactory->geraHistorico(array("titulo" => 5, "descricao" => "Responsável -> $tokenBody->nome", "tipo" => 1));
                // Inserir histórico do usuario
                $historicoUtilizacao = $historicoRepository->insert($historicoUtilizacao);
                $historicoUsuario = $historicoRepository->insertHistoricoUsuario($historicoUtilizacao->idhistorico, $idusuario);
                $historicoUsuario = $historicoRepository->insertHistoricoUsuario($historicoUtilizacao->idhistorico, $usuario_alteracao->idusuario);

                $msg = "Usuário alterado com sucesso! ($alterado registros alterados)";
            }
            else
            {
                throw new Exception("Usuário inválido ou sem permissão para realizar a ação!");
            }
            
            $res->getBody()->write(
                (string) json_encode(array(
                    "status" => 200,
                    "msg" => $msg
                ))
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
                                "mensagem" => "Houve um erro ao alterar os dados do usuario!",
                                "erro" => (string) $ex->getMessage()
                            )
                        )
                    );

            return $res->withHeader("Content-Type", "application/json");
        }
    }
}
