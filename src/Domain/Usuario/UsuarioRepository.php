<?php
namespace App\Domain\Usuario;

use App\Domain\Usuario\Usuario; 
use Firebase\JWT\JWT;
use App\Domain\Token\TokenService;

class UsuarioRepository
{
    public function __construct()
    {
        
    }

    public function select()
    {
        return "SELECT * FROM usuario";
    }

    public function buscaUsuarioEmail($emailusuario)
    {
        $query = str_replace("usuario WHERE emailusuario = ?", "usuario", $this->select());

        return new Usuario(["emailusuario" => "vinicius@gmail.com", "senhausuario" => "logar123"]);
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