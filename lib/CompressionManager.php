<?php

namespace SpomkyLabs\Service;

use SpomkyLabs\Jose\Compression\GZip;
use SpomkyLabs\Jose\Compression\ZLib;
use SpomkyLabs\Jose\Compression\Deflate;
use SpomkyLabs\Jose\Compression\CompressionManager as Base;

class CompressionManager extends Base
{
    public function __construct()
    {
        $this->addCompressionAlgorithm(new Deflate())
             ->addCompressionAlgorithm(new GZip())
             ->addCompressionAlgorithm(new ZLib());
    }
}
