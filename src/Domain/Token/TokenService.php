<?php
namespace App\Domain\Token;

class TokenService
{
    private $_key = "5a7aa81048264b99b9108a32887a9a32";

    public function getKey()
    {
        return $this->_key;
    }
}