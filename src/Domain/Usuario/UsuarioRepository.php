<?php
namespace App\Domain\Usuario;

use App\Domain\Usuario\Usuario; 
use Firebase\JWT\JWT;
use App\Domain\Token\TokenService;
use App\Domain\Config\Conexao;
use PDO;
use App\Domain\Usuario\UsuarioFactory;
use App\Domain\Perfil\PerfilRepository;
use App\Domain\Perfil\PerfilFactory;
use Exception;

class UsuarioRepository
{

    public $_conn;

    public function __construct(Conexao $conexao)
    {
        $this->_conn = $conexao;
    }

    public function select()
    {
        return "SELECT * FROM usuario";
    }

    public function insert(Usuario $u)
    {
        $query = "INSERT INTO usuario (nomeusuario, emailusuario, senhausuario) VALUES (:nomeusuario, :emailusuario, :senhausuario)";

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":nomeusuario", $u->nomeusuario);
        $stmt->bindValue(":emailusuario", $u->emailusuario);
        $stmt->bindValue(":senhausuario", password_hash($u->senhausuario, PASSWORD_DEFAULT));

        $stmt->execute();
        $id_usuario = $conexao->lastInsertId();
        
        $u->idusuario = $id_usuario;

        return $u;
                    
    }

    public function buscaUsuarioEmail($emailusuario)
    {
        $query = str_replace("usuario", 
            "usuario u 
            WHERE 
            emailusuario = :emailcliente", 
            $this->select());

        $conexao = $this->_conn->getConexao();
        $stmt = $conexao->prepare($query);
        $stmt->bindValue(":emailcliente", $emailusuario);

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $error = $stmt->errorInfo();

        $uf = new UsuarioFactory();
        return $uf->geraUsuario(!empty($resultado) ? $resultado[0] : []);
    }

    public function logar($u)
    {
        try {
            $usuarioDb = $this->buscaUsuarioEmail($u["emailusuario"]);
            $tokenService = new TokenService();

            if(!is_null($usuarioDb->emailusuario) && password_verify($u["senhausuario"], $usuarioDb->senhausuario))
            {
                $p = new PerfilRepository($this->_conn);
                $perfilUsuario = $p->buscaPerfilUsuario($usuarioDb);
                if(!empty($perfilUsuario))
                {
                    $token = JWT::encode(
                                ["id" => $usuarioDb->idusuario, 
                                "email" => $usuarioDb->emailusuario, 
                                "nome" => $usuarioDb->nomeusuario, 
                                "perfil" => $tokenService->criptString(json_encode($perfilUsuario), "encrypt")],
                                $tokenService->getKey(),
                                "HS256"
                    );
                }
                else
                {
                    throw new Exception("O usuario se encontra sem perfil ou não tem acesso ao sistema!");
                }

                return array( 
                            "token" => $token,
                            "usuario" => [
                                "idusuario" => $usuarioDb->idusuario,
                                "emailusuario" => $usuarioDb->emailusuario,
                                "perfil" => $tokenService->criptString(json_encode($perfilUsuario), "encrypt")
                            ],
                            "status" => 200,
                            "mensagem" => "Usuário logado com sucesso!");
            }
            else
            {//O login ou senha estão incorretos
                throw new Exception('Login e/ou senha incorretos! (O usuario pode estar desativado!)');
            }
        } catch (Exception $ex) {//Houve um erro no servidor
            $uf = new UsuarioFactory();
                
            return array(
                "usuario" => $uf->geraUsuario([]),
                "status" => 201,
                "mensagem" => $ex->getMessage());
        }
        
    }

    public function cadastrarUsuario($usuario)
    {
        $usuarioDb = $this->buscaUsuarioEmail($usuario["emailusuario"]);
        $tokenService = new TokenService();
        $uf = new UsuarioFactory();
        try
        {
            if(!is_null($usuarioDb->emailusuario))
            {// O usuario já existe no banco de dados
                throw new Exception("Esse e-mail já está vinculado a uma conta!");
            }
            else
            {//O usuario não está cadastrado ainda
                
                    $usuarioDb = $this->insert($uf->geraUsuario($usuario));
                    $token = "";
                    if(!is_null($usuarioDb->idusuario))
                    {                        
                        return array( 
                            "usuario" => $usuarioDb,
                            "status" => 200,
                            "mensagem" => "Usuario cadastrado com sucesso!");
                    }
                    else
                    {
                        throw new Exception("Houve um erro ao cadastrar o usuário!");
                        
                    }
            } 
        }
        catch(Exception $ex)
        {
            return array( 
                "usuario" => $uf->geraUsuario([]),
                "status" => 500,
                "mensagem" => $ex->getMessage()
            );
        }
    }
}