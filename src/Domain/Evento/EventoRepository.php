<?php
namespace App\Domain\Evento;

use App\Domain\Evento\Evento;
use App\Domain\Config\Conexao;
use PDO;
use Firebase\JWT\JWT;
use App\Domain\Usuario\UsuarioFactory;
use App\Domain\Token\TokenService;
use App\Domain\Evento\EventoFactory;

class EventoRepository
{
    #region
    private $_conn;
    #endregion

    public function __construct(Conexao $conexao)
    {
       $this->_conn = $conexao;
    }

    public function select()
    {
        return "SELECT 
                        *
                    FROM
                        evento e";
    }

    public function buscaEventoUsuario($idusuario)
    {
        $eventoFactory = new EventoFactory;
        $query = $this->select();

        $query = str_replace("evento e", "evento e, eventousuario eu WHERE eu.evento_idevento = e.idevento AND eu.usuario_idusuario = :idusuario AND e.status = 1", $this->select());
        
        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":idusuario", $idusuario);

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $eventos = [];
        foreach($resultado as $res)
        {
            $eventos[] = $eventoFactory->geraEvento($res);
        }

        return $eventos; 
    }

    public function insert(Evento $e)
    {
        $query = "INSERT INTO 
                            evento 
                            (
                                tituloevento, 
                                descevento, 
                                dataInicioEvento, 
                                dataFimEvento, 
                                lembrete
                            ) 
                        VALUES 
                            (
                                :tituloevento, 
                                :descevento, 
                                :dataInicioEvento,
                                :dataFimEvento,
                                :lembrete
                            )";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":tituloevento", $e->tituloevento);
        $stmt->bindValue(":descevento", $e->descevento);
        $stmt->bindValue(":dataInicioEvento", $e->dataInicioEvento);
        $stmt->bindValue(":dataFimEvento", $e->dataFimEvento);
        $stmt->bindValue(":lembrete", $e->lembrete);

        $stmt->execute();

        $e->idevento = $conexao->lastInsertId();
        
        return $e;
    }

    public function insertUsuarioEvento($idevento, $idusuario)
    {
        $query = "INSERT INTO eventousuario (evento_idevento, usuario_idusuario) VALUES (:idevento, :idusuario)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":idevento", $idevento);
        $stmt->bindValue(":idusuario", $idusuario);

        $stmt->execute();

        $insert_id = $conexao->lastInsertId();

        return $insert_id;
    }

    public function update(Evento $e)
    {
        $query = "UPDATE 
                        evento 
                    SET 
                        tituloevento = :tituloevento, 
                        descevento = :descevento, 
                        dataInicioEvento = :dataInicioEvento,
                        dataFimEvento = :dataFimEvento,
                        lembrete = :lembrete
                    WHERE 
                        idevento = :idevento";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":tituloevento", $e->tituloevento);
        $stmt->bindValue(":descevento", $e->descevento);
        $stmt->bindValue(":dataInicioEvento", $e->dataInicioEvento);
        $stmt->bindValue(":dataFimEvento", $e->dataFimEvento);
        $stmt->bindValue(":lembrete", $e->lembrete);
        $stmt->bindValue(":idevento", $e->idevento);

        $stmt->execute();
        return $stmt->rowCount();

    }

    public function buscaUsuarioEvento(Evento $e)
    {
        $usuarioFactory = new UsuarioFactory();
        $query = str_replace("evento e", "evento e
                                INNER JOIN eventousuario AS euser
                                ON euser.evento_idevento = e.idevento
                                INNER JOIN usuario AS u
                                ON u.idusuario = euser.usuario_idusuario
                            WHERE 
                                e.idevento = :idevento", 
                            $this->select());
        
        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":idevento", $e->idevento);

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $usuarios = [];
        foreach($resultado as $res)
        {
            $usuarios[$res["idusuario"]] = $usuarioFactory->geraUsuario($res);
        }

        return $usuarios;
    }

    public function insertEnderecoEvento(Evento $evento, $latitude, $longitude)
    {
        $query = "INSERT INTO eventoendereco (latitude, longitude, evento_idevento) VALUES (:latitude, :longitude, :idevento)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":latitude", $latitude);
        $stmt->bindValue(":longitude", $longitude);
        $stmt->bindValue(":idevento", $evento->idevento);

        $stmt->execute();
        
        $id_endereco = $conexao->lastInsertId();

        if($conexao->errorInfo()[1] > 0)
        {
            throw new Exception($conexao->errorInfo()[2]);
        }

        return $id_endereco;
    }
}