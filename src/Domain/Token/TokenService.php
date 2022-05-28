<?php
namespace App\Domain\Token;

use Firebase\JWT\JWT;

class TokenService
{
    private $_key = "hm-iYsaWRkSdySW3U25wZg";
    private $_ivkey = "_9fqncCuh0q8l5WuZt_mSw";
    private $_encrypt_method = "AES-256-CBC";

    public function getKey()
    {
        return $this->_key;
    }

    public function getTokenBody()
    {
        $headers = apache_request_headers();
        if(!isset(apache_request_headers()["Authorization"])) return "No Authenticate";
        
        if(!empty(explode(",", apache_request_headers()["Authorization"])[0]) != "Bearer")
        {
            //PARA PROD USAR ESSE
            $infoUsuario = JWT::decode(explode(",", apache_request_headers()["Authorization"])[0], $this->_key, array_keys(JWT::$supported_algs));
        }
        else
        {
            //PARA TESTE USAR ESSE
            $infoUsuario = JWT::decode(explode(" ", $headers["Authorization"])[1], $this->_key, array_keys(JWT::$supported_algs));
        }

        return $infoUsuario;
    }

    public function criptString($string, $action)
    {
        $output = false;

        // hash
        $key = hash('sha256', $this->_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $this->_ivkey), 0, 16);
        if ($action == 'encrypt') {
            $output = base64_encode(openssl_encrypt($string, $this->_encrypt_method, $key, 0, $iv));
        } else {
            if ($action == 'decrypt') {
                $output = openssl_decrypt(base64_decode($string), $this->_encrypt_method, $key, 0, $iv);
            }
        }

        return str_replace('"', "", $output);
    }

    public function geraTokenAcesso($body)
    {
        return JWT::encode(
            $body,
            $this->getKey(),
            "HS256"
        );
    }
}