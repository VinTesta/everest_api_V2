<?php
namespace App\Domain\Historico;

class HistoricoFactory
{
    public function __construct()
    {
        
    }

    public function geraHistorico($params)
    {
        $idhistorico = isset($params["idhistorico"]) ? $params["idhistorico"] : null;
        $data = isset($params["data"]) ? $params["data"] : null;
        $titulo = isset($params["titulo"]) ? $params["titulo"] : null;
        $descricao = isset($params["descricao"]) ? $params["descricao"] : null;
        $tipo = isset($params["tipo"]) ? $params["tipo"] : null;

        return new Historico(["idhistorico" => $idhistorico,
                            "data" => $data,
                            "titulo" => $this->geraTitulo($titulo),
                            "descricao" => $descricao,
                            "tipo" => $tipo
                            ]);
    }

    public function geraTitulo($id_titulo)
    {
        $msg_historico = "";
        switch($id_titulo)
        {
            case 1:
                $msg_historico = "Criação de conta de usuário";
                break;
            case 2:
                $msg_historico =  "Cadastro de novo usuário";
                break;
            case 3:
                $msg_historico =  "Acesso ao sistema";
                break;
            case 4: 
                $msg_historico =  "Logout do sistema";
                break;
            case 5:
                $msg_historico = "Alteração de dados do usuário";
                break;
            case 6:
                $msg_historico = "Cadastro de evento";
                break;
            case 7:
                $msg_historico = "Alteração de evento";
                break;
            case null:
                break;
            default:
                $msg_historico = "Log geral de consulta sistemica!";
                break;
        }

        return $msg_historico;
    }
}