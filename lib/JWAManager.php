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

use SpomkyLabs\Jose\JWAManager as Base;

class JWAManager extends Base
{
    /**
     * @param \SpomkyLabs\Service\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        if (!is_array($config->get('Algorithms'))) {
            return;
        }
        $algs = $this->getAvailableAlgorithms();
        foreach ($config->get('Algorithms') as $alg) {
            if (array_key_exists($alg, $algs)) {
                $class = 'SpomkyLabs\Jose\Algorithm\\'.$algs[$alg];
                try {
                    $this->addAlgorithm(new $class());
                } catch (\Exception $e) {
                    printf('Unable to load algorithm %s. Message is: %s\n', $alg, $e->getMessage());
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getAvailableAlgorithms()
    {
        return [
            'HS256'              => 'Signature\HS256',
            'HS384'              => 'Signature\HS384',
            'HS512'              => 'Signature\HS512',
            'ES256'              => 'Signature\ES256',
            'ES384'              => 'Signature\ES384',
            'ES512'              => 'Signature\ES512',
            'none'               => 'Signature\None',
            'RS256'              => 'Signature\RS256',
            'RS384'              => 'Signature\RS384',
            'RS512'              => 'Signature\RS512',
            'PS256'              => 'Signature\PS256',
            'PS384'              => 'Signature\PS384',
            'PS512'              => 'Signature\PS512',
            'A128GCM'            => 'ContentEncryption\A128GCM',
            'A192GCM'            => 'ContentEncryption\A192GCM',
            'A256GCM'            => 'ContentEncryption\A256GCM',
            'A128CBC-HS256'      => 'ContentEncryption\A128CBCHS256',
            'A192CBC-HS384'      => 'ContentEncryption\A192CBCHS384',
            'A256CBC-HS512'      => 'ContentEncryption\A256CBCHS512',
            'A128KW'             => 'KeyEncryption\A128KW',
            'A192KW'             => 'KeyEncryption\A192KW',
            'A256KW'             => 'KeyEncryption\A256KW',
            'A128GCMKW'          => 'KeyEncryption\A128GCMKW',
            'A192GCMKW'          => 'KeyEncryption\A192GCMKW',
            'A256GCMKW'          => 'KeyEncryption\A256GCMKW',
            'dir'                => 'KeyEncryption\Dir',
            'ECDH-ES'            => 'KeyEncryption\ECDHES',
            'ECDH-ES+A128KW'     => 'KeyEncryption\ECDHESA128KW',
            'ECDH-ES+A192KW'     => 'KeyEncryption\ECDHESA192KW',
            'ECDH-ES+A256KW'     => 'KeyEncryption\ECDHESA256KW',
            'PBES2-HS256+A128KW' => 'KeyEncryption\PBES2HS256A128KW',
            'PBES2-HS384+A192KW' => 'KeyEncryption\PBES2HS384A192KW',
            'PBES2-HS512+A256KW' => 'KeyEncryption\PBES2HS512A256KW',
            'RSA1_5'             => 'KeyEncryption\RSA15',
            'RSA-OAEP'           => 'KeyEncryption\RSAOAEP',
            'RSA-OAEP-256'       => 'KeyEncryption\RSAOAEP256',
        ];
    }
}
