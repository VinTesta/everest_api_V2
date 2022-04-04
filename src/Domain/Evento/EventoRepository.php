<?php
namespace App\Domain\Evento;

use App\Domain\Evento\Evento;
use App\Domain\Config\Conexao;
use PDO;
use Firebase\JWT\JWT;
use App\Domain\Token\TokenService;

class EventoRepository
{
    #region
    private $_conn;
    #endregion

    public function __construct(Conexao $conexao)
    {
       $this->_conn = $conexao->getConexao();
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
        $query = $this->select();

        $query = str_replace("evento e", "evento e, usuarioevento ue WHERE ue.evento_idevento = e.idevento AND ue.usuario_idusuario = :idusuario", $this->select());
         
        $stmt = $this->_conn->prepare($query);
        $stmt->bindValue(":idusuario", $idusuario);

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $eventos = [];
        foreach($resultado as $res)
        {
            $eventos[] = new Evento($res);
        }

        $error = $stmt->errorInfo();
                        
        return $error[1] == 0
                    ?
                        array("eventos" => $eventos, "status" => 1)
                    :   
                        array("error" => $error[1], "mensagem" => $error[2]);
    }
}