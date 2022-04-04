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
                        e.idevento,
                        e.tituloevento,
                        e.descevento,
                        e.statusevento
                    FROM
                        evento e";
    }

    public function buscaEvento()
    {
        $query = $this->select();
        $tokenService = new TokenService();
        $headers = apache_request_headers();
        var_dump(JWT::decode(explode(" ", $headers['Authorization'])[1], $tokenService->getKey(), array_keys(JWT::$supported_algs)));
        die();

        $stmt = $this->_conn->prepare($query);

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