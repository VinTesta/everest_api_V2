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
        try {
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
                            array("status" => 200, "eventos" => $eventos, "mensagem" => "Busca realizada com sucesso!")
                        :   
                            array("status" => 500, "eventos" => [], "error" => $error[1], "mensagem" => "Houve um erro ao trazer as informações!");
        } catch (Exception $ex) {
            return array("status" => 500, "error" => $ex, "eventos" => [], "mensagem" => "Houve um erro ao trazer as informações!");
        }
        
    }
}