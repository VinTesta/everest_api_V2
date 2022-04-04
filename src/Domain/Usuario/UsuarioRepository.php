<?php
namespace App\Domain\Usuario;

use App\Domain\Usuario\Usuario; 
use Firebase\JWT\JWT;
use App\Domain\Token\TokenService;
use App\Domain\Config\Conexao;
use PDO;
use App\Domain\Usuario\UsuarioFactory;

class UsuarioRepository
{

    private $_conn;

    public function __construct(Conexao $conexao)
    {
        $this->_conn = $conexao->getConexao();
    }

    public function select()
    {
        return "SELECT * FROM usuario";
    }

    public function buscaUsuarioEmail($emailusuario)
    {
        $query = str_replace("usuario", "usuario WHERE emailusuario = :emailcliente", $this->select());

        $stmt = $this->_conn->prepare($query);
        $stmt->bindValue(":emailcliente", $emailusuario);

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $error = $stmt->errorInfo();

        $uf = new UsuarioFactory();
        return $uf->geraUsuario($resultado[0]);
    }

    public function logar($u)
    {
        $usuarioDb = $this->buscaUsuarioEmail($u["emailusuario"]);
        $tokenService = new TokenService();

        if(!is_null($usuarioDb->_emailusuario) && $usuarioDb->_senhausuario == $u["senhausuario"])
        {
            $token = JWT::encode(
                        ["id" => $usuarioDb->_idusuario, "email" => $usuarioDb->_emailusuario, "nome" => $usuarioDb->_nomeusuario],
                        $tokenService->getKey(),
                        "HS256"
            );

            return $token;
        }
        else
        {
            return false;
        }
    }
}