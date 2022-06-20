<?php

declare(strict_types=1);

namespace App\Application\Actions\Evento;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Config\ConexaoMySql;
use App\Domain\Evento\EventoRepository;
use App\Domain\Token\TokenService;
use App\Domain\Evento\EventoFactory;
use App\Domain\Historico\HistoricoRepository;
use App\Domain\Historico\HistoricoFactory;
use App\Domain\Perfil\PerfilRepository;
use Firebase\JWT\JWT;
use Exception;

final class AlterarEvento
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $req, Response $res, array $args): Response
    {
        try {
            $conexao = new ConexaoMySql();
            $conn = $conexao->getConexao();

            $eventoRepository = new EventoRepository($conexao);
            $historicoFactory = new HistoricoFactory();
            $tokenService = new TokenService();
            $historicoRepository = new HistoricoRepository($conexao);
            $eventoFactory = new EventoFactory();
            $perfilRepository = new PerfilRepository($conexao);

            $conn->beginTransaction();

            $infoToken = $tokenService->getTokenBody();
            $idusuario = $tokenService->criptString($infoToken->id, "decrypt");
            
            $bodyReq = (array)$req->getParsedBody();

            $evento = $eventoFactory->geraEvento($bodyReq);
            $usuariosEvento = $eventoRepository->buscaUsuarioEvento($evento);

            if($evento->idevento != null && (array_key_exists($idusuario, $usuariosEvento) || $perfilRepository->validaPermissaoUsuario($idusuario, ["altEvento"])))
            {
                $linhasAlteradas = $eventoRepository->update($evento);
                if($linhasAlteradas >= 0)
                {
                    $historicoUtilizacao = $historicoFactory->geraHistorico(array("titulo" => 7, "descricao" => "Alterado via API", "tipo" => 1));
                    // Inserir histórico do usuario
                    $historicoUtilizacao = $historicoRepository->insert($historicoUtilizacao);
                    $historicoUsuario = $historicoRepository->insertHistoricoUsuario($historicoUtilizacao->idhistorico, $idusuario);
                    $historicoUsuario = $historicoRepository->insertHistoricoEvento($historicoUtilizacao->idhistorico, $evento->idevento);
                }
                else
                {
                    throw new Exception("Dados incorretos!");
                }
            }
            else
            {
                throw new Exception("Sem permissões para alterar o evento!");
            }

            $res->getBody()->write(
                (string) json_encode(
                    array(
                        "status" => 200,
                        "evento" => $evento,
                        "mensagem" => "Evento alterado com sucesso! ($linhasAlteradas linhas alterados)"
                        )                    
                )
            );
 
            $conn->commit();
            return $res
                    ->withHeader("Content-Type", "application/json; charset=utf-8");
        } catch (Exception $ex) {
            $conn->rollBack();

            $res->getBody()->write(
                (string) json_encode(
                    array( 
                        "status" => 500,
                        "mensagem" => "Houve um erro ao alterar o eventos!",
                        "erro" => (string) $ex->getMessage()
                    )
                )
            );
            return $res->withHeader("Content-Type", "application/json");
        }
    }
}
