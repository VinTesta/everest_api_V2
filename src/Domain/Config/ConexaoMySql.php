<?php
namespace App\Domain\Config;

use App\Domain\Config\Conexao;
use PDO;

class ConexaoMySql implements Conexao 
{
    private $_conexao;
    public function getConexao()
    {
        try {

            $this->_conexao =  !empty($this->_conexao) 
                                ?
                                    $this->_conexao 
                                :
                                    $this->newConexao();

            return $this->_conexao;
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function newConexao()
    {
        try {
            return new PDO("mysql:host=143.106.241.3;dbname=cl200275", "cl200275", "cl*15042004", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
            date_default_timezone_set('America/Sao_Paulo');
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}