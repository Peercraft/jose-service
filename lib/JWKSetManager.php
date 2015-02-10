<?php

namespace SpomkyLabs\Service;

use SpomkyLabs\Jose\JWKSet;
use Jose\JWKManagerInterface;
use Jose\JWKSetManager as Base;
use Base64Url\Base64Url;

/**
 */
class JWKSetManager extends Base
{
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
            $key = $this->createJWK($value);
            $key_set->addKey($key);
        }

        return $key_set;
    }
}
