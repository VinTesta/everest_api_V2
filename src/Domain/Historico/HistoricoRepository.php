<?php
namespace App\Domain\Historico;

use Firebase\JWT\JWT;
use App\Domain\Token\TokenService;
use App\Domain\Config\Conexao;
use PDO;
use App\Domain\Usuario\Usuario;
use App\Domain\Historico\HistoricoFactory;

class HistoricoRepository
{

    private $_conn;

    public function __construct(Conexao $conexao)
    {
        $this->_conn = $conexao;
    }

    public function select()
    {
        return "SELECT * FROM historico";
    }

    public function insert(Historico $h)
    {
        $historicofactory = new HistoricoFactory();
        $query = "INSERT INTO historico (titulo, descricao, tipo) VALUES (:titulo, :descricao, :tipo)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":titulo", $h->titulo);
        $stmt->bindValue(":descricao", $h->descricao);
        $stmt->bindValue(":tipo", $h->tipo);
        
        $stmt->execute();
        $id_historico = $conexao->lastInsertId();

        $h->idhistorico = $id_historico;
        return $h;
    }

    public function insertHistoricoUsuario($id_historico, $id_usuario)
    {
        $query = "INSERT INTO historicousuario (usuario_idusuario, historico_idhistorico) VALUES (:idusuario, :idhistorico)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":idusuario", $id_usuario);
        $stmt->bindValue(":idhistorico", $id_historico);

        $stmt->execute();
        $id_historico_usuario = $conexao->lastInsertId();

        return $id_historico_usuario;
    }

    public function insertHistoricoEvento($id_historico, $id_evento)
    {
        $query = "INSERT INTO historicoevento (evento_idevento, historico_idhistorico) VALUES (:idevento, :idhistorico)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":idevento", $id_evento);
        $stmt->bindValue(":idhistorico", $id_historico);

        $stmt->execute();
        $id_historico_evento = $conexao->lastInsertId();

        return $id_historico_evento;
    }
}