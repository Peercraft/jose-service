<?php

namespace SpomkyLabs\Service;

use Jose\JWKManagerInterface;
use Jose\JWAManagerInterface;
use Jose\JWTManagerInterface;
use Jose\JWKSetManagerInterface;
use SpomkyLabs\Jose\Signer as Base;

class Signer extends Base
{
    /**
     * @var \Jose\JWTManagerInterface
     */
    protected $jwt_manager;

    /**
     * @var \Jose\JWAManagerInterface
     */
    protected $jwa_manager;

    /**
     * @var \Jose\JWKManagerInterface
     */
    protected $jwk_manager;

    /**
     * @var \Jose\JWKSetManagerInterface
     */
    protected $jwkset_manager;

    /**
     * @param \Jose\JWAManagerInterface    $jwa_manager
     * @param \Jose\JWTManagerInterface    $jwt_manager
     * @param \Jose\JWKManagerInterface    $jwk_manager
     * @param \Jose\JWKSetManagerInterface $jwkset_manager
     */
    public function __construct(
        JWAManagerInterface    $jwa_manager,
        JWTManagerInterface    $jwt_manager,
        JWKManagerInterface    $jwk_manager,
        JWKSetManagerInterface $jwkset_manager
    ) {
        $this->jwt_manager = $jwt_manager;
        $this->jwa_manager = $jwa_manager;
        $this->jwk_manager = $jwk_manager;
        $this->jwkset_manager = $jwkset_manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getJWTManager()
    {
        return $this->jwt_manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getJWAManager()
    {
        return $this->jwa_manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getJWKManager()
    {
        return $this->jwk_manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getJWKSetManager()
    {
        return $this->jwkset_manager;
    }
}
