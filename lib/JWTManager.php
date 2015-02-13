<?php

namespace SpomkyLabs\Service;

use SpomkyLabs\Jose\JWT;
use SpomkyLabs\Jose\JWS;
use SpomkyLabs\Jose\JWE;
use Jose\JWTManagerInterface;

class JWTManager implements JWTManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createJWT()
    {
        return new JWT();
    }

    /**
     * {@inheritdoc}
     */
    public function createJWS()
    {
        return new JWS();
    }

    /**
     * {@inheritdoc}
     */
    public function createJWE()
    {
        return new JWE();
    }
}
