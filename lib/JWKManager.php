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

use Jose\JWKManager as Base;
use SpomkyLabs\Jose\JWK;

/**
 */
class JWKManager extends Base
{
    /**
     * {@inheritdoc}
     */
    public function createJWK(array $values = [])
    {
        $jwk = new JWK();
        $jwk->setValues($values);

        return $jwk;
    }
}
