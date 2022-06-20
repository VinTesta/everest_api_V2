<?php
namespace App\Domain\Perfil;

use Firebase\JWT\JWT;
use App\Domain\Token\TokenService;
use App\Domain\Config\Conexao;
use PDO;
use App\Domain\Usuario\Usuario;

class PerfilRepository
{

    private $_conn;

    public function __construct(Conexao $conexao)
    {
        $this->_conn = $conexao;
    }

    public function select()
    {
        return "SELECT * FROM perfil";
    }

    public function insert(Perfil $p)
    {
        $query = "INSERT INTO perfil (nomeperfil, setorperfil) VALUES (:nomeperfil, :setorperfil)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":nomeperfil", $p->nomeperfil);
        $stmt->bindValue(":setorperfil", $p->setorperfil);
        
        $stmt->execute();
        $id_perfil = $conexao->lastInsertId();

        $p->idperfil = $id_perfil;
        return $p;
    }

    public function atualizaPerfilUsuario($arrayPerfil, Usuario $u)
    {
        $query = "INSERT INTO perfilusuario (usuario_idusuario, perfil_idperfil) VALUES (:idusuario, :perfil)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);

        $insert_perfil = [];

        foreach($arrayPerfil as $perfil)
        {
            $stmt->bindValue(":idusuario", $u->idusuario);
            $stmt->bindValue(":perfil", $perfil["idperfil"]);
            $stmt->execute();

            $insert_perfil[] = $conexao->lastInsertId();
        }

        return $insert_perfil;
    }

    public function buscaPerfil($params)
    {
        $query =  str_replace("perfil", 
                "perfil p 
            WHERE 
                p.nomeperfil = :nomeperfil
                AND p.status = 1
            LIMIT 1",
            $this->select());

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":nomeperfil", $params["nomeperfil"]);

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $arrayPerfil = [];
        foreach($resultado as $perfil)
        {
            $arrayPerfil[] = $perfil;
        }

        return $arrayPerfil;
    }

    public function buscaPerfilUsuario(Usuario $u)
    {

        $query = str_replace("perfil", 
                "perfil p 
                INNER JOIN perfilusuario AS pu
                ON p.idperfil = pu.perfil_idperfil
            WHERE 
                pu.usuario_idusuario = :idusuario
                AND p.status = 1",
            $this->select());

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":idusuario", $u->idusuario);
        $stmt->execute();
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $error = $stmt->errorInfo();

        $pf = new PerfilFactory();
        $perfis = [];
        foreach($resultado as $res)
        {
            $perfis[] = $pf->geraPerfil($res);
        }

        return $perfis;
    }

    public function validaPermissaoUsuario($idusuario, $permissao)
    {
        $busca_permissao = "('". implode("', '", $permissao)."')";
        $query = str_replace("perfil", 
                "perfil p 
                INNER JOIN perfilusuario AS pu
                ON p.idperfil = pu.perfil_idperfil
                INNER JOIN permissaoperfil AS pp
                ON p.idperfil = pp.perfil_idperfil
                INNER JOIN permissao AS pe
                ON pe.idpermissao = pp.permissao_idpermissao
            WHERE 
                pu.usuario_idusuario = $idusuario
                AND pe.codPermissao IN $busca_permissao
                AND p.status = 1",
            $this->select());

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
}