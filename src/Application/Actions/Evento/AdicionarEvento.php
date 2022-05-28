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
use Firebase\JWT\JWT;
use Exception;
use App\Domain\Usuario\UsuarioFactory;

final class AdicionarEvento
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
            $usuarioFactory = new UsuarioFactory();
            $historicoFactory = new HistoricoFactory();
            $tokenService = new TokenService();
            $historicoRepository = new HistoricoRepository($conexao);
            $eventoFactory = new EventoFactory();

            $conn->beginTransaction();

            $infoToken = $tokenService->getTokenBody();
            $idusuario = $tokenService->criptString($infoToken->id, "decrypt");
            
            $bodyReq = (array)$req->getParsedBody();

            $evento = $eventoFactory->geraEvento($bodyReq);
            $evento = $eventoRepository->insert($evento);

            if($evento->idevento != null)
            {
                $relative_id = $eventoRepository->insertUsuarioEvento($evento->idevento, $idusuario);
                if($relative_id != null)
                {
                    $historicoUtilizacao = $historicoFactory->geraHistorico(array("titulo" => 6, "descricao" => "Responsável -> $infoToken->nome", "tipo" => 1));
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
                throw new Exception("Dados incorretos!");
            }

            $res->getBody()->write(
                (string) json_encode(
                    array(
                        "status" => 200,
                        "evento" => $evento,
                        "mensagem" => "Evento cadastrado com sucesso!"
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
                        "mensagem" => "Houve um erro ao adicionar os eventos!",
                        "erro" => (string) $ex
                    )
                )
            );
            return $res->withHeader("Content-Type", "application/json");
        }
        
    }
}
