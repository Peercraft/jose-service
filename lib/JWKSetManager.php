<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\Service;

use Jose\JWKManagerInterface;
use Jose\JWKSetInterface;
use Jose\JWKSetManager as Base;
use SpomkyLabs\Jose\JWKSet;

/**
 */
class JWKSetManager extends Base
{
    /**
     * @var array
     */
    private $key_sets = [];

    /**
     * @var \Jose\JWKManagerInterface
     */
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
    public function createJWKSet(array $values = [])
    {
        $key_set = new JWKSet();
        foreach ($values as $value) {
            $key = $this->getJWKManager()->createJWK($value);
            $key_set->addKey($key);
        }

        return $key_set;
    }

    /**
     * @param \Jose\JWKSetInterface $key_set
     *
     * @return $this
     */
    public function addKeySet(JWKSetInterface $key_set)
    {
        $this->key_sets[] = $key_set;

        return $this;
    }
}
