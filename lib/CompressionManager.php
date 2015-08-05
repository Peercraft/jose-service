<?php

namespace SpomkyLabs\Service;

use SpomkyLabs\Jose\Compression\CompressionManager as Base;

class CompressionManager extends Base
{
    /**
     * @param \SpomkyLabs\Service\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        if (! is_array($config->get('Compression'))) {
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
        return array(
            'DEF' => 'SpomkyLabs\Jose\Compression\Deflate',
            'GZ' => 'SpomkyLabs\Jose\Compression\GZip',
            'ZLIB' => 'SpomkyLabs\Jose\Compression\ZLib',
        );
    }
}
