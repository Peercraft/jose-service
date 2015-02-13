<?php

namespace SpomkyLabs\Service;

use Jose\JWKSetInterface;
use SpomkyLabs\Jose\JWKSet;
use Jose\JWKManagerInterface;
use Jose\JWKSetManager as Base;

/**
 */
class JWKSetManager extends Base
{
    private $key_sets = array();

    protected $jwk_manager;

    public function __construct(JWKManagerInterface $jwk_manager)
    {
        $this->jwk_manager = $jwk_manager;
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
    public function createJWKSet(array $values = array())
    {
        $key_set = new JWKSet();
        foreach ($values as $value) {
            $key = $this->getJWKManager()->createJWK($value);
            $key_set->addKey($key);
        }

        return $key_set;
    }

    public function addKeySet(JWKSetInterface $key_set)
    {
        $this->key_sets[] = $key_set;

        return $this;
    }
}
