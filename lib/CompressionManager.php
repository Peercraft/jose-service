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

use SpomkyLabs\Jose\Compression\CompressionManager as Base;

class CompressionManager extends Base
{
    /**
     * @param \SpomkyLabs\Service\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        if (!is_array($config->get('Compression'))) {
            return;
        }
        $algs = $this->getAvailableCompressionAlgorithms();
        foreach ($config->get('Compression') as $alg) {
            if (array_key_exists($alg, $algs)) {
                $class = $algs[$alg];
                try {
                    $this->addCompressionAlgorithm(new $class());
                } catch (\Exception $e) {
                    printf('Unable to load compression algorithm %s. Message is: %s\n', $alg, $e->getMessage());
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getAvailableCompressionAlgorithms()
    {
        return [
            'DEF'  => 'SpomkyLabs\Jose\Compression\Deflate',
            'GZ'   => 'SpomkyLabs\Jose\Compression\GZip',
            'ZLIB' => 'SpomkyLabs\Jose\Compression\ZLib',
        ];
    }
}
