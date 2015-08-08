<?php

namespace SpomkyLabs\Service;

use Jose\JWAManagerInterface;
use Jose\JWTManagerInterface;
use Jose\JWKManagerInterface;
use Jose\JWKSetManagerInterface;
use Jose\Compression\CompressionManagerInterface;
use SpomkyLabs\Jose\Checker\CheckerManagerInterface;
use SpomkyLabs\Jose\Loader as Base;
use SpomkyLabs\Jose\Payload\PayloadConverterManagerInterface;

class Loader extends Base
{
    /**
     * @var string
     */
    protected $audience;

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
     * @var \Jose\Compression\CompressionManagerInterface
     */
    protected $compression_manager;

    /**
     * @var \SpomkyLabs\Jose\Checker\CheckerManagerInterface
     */
    protected $checker_manager;

    /**
     * @var \SpomkyLabs\Jose\Payload\PayloadConverterManagerInterface
     */
    protected $payload_converter_manager;

    /**
     * @param \Jose\JWAManagerInterface                     $jwa_manager
     * @param \Jose\JWTManagerInterface                     $jwt_manager
     * @param \Jose\JWKManagerInterface                     $jwk_manager
     * @param \Jose\JWKSetManagerInterface                  $jwkset_manager
     * @param \Jose\Compression\CompressionManagerInterface $compression_manager
     * @param \SpomkyLabs\Jose\Checker\CheckerManagerInterface $checker_manager
     * @param \SpomkyLabs\Jose\Payload\PayloadConverterManagerInterface $payload_converter_manager
     */
    public function __construct(
        JWAManagerInterface    $jwa_manager,
        JWTManagerInterface    $jwt_manager,
        JWKManagerInterface    $jwk_manager,
        JWKSetManagerInterface $jwkset_manager,
        CompressionManagerInterface $compression_manager,
        CheckerManagerInterface $checker_manager,
        PayloadConverterManagerInterface $payload_converter_manager
    ) {
        $this->jwt_manager = $jwt_manager;
        $this->jwa_manager = $jwa_manager;
        $this->jwk_manager = $jwk_manager;
        $this->jwkset_manager = $jwkset_manager;
        $this->compression_manager = $compression_manager;
        $this->checker_manager = $checker_manager;
        $this->payload_converter_manager = $payload_converter_manager;
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

    /**
     * {@inheritdoc}
     */
    protected function getCompressionManager()
    {
        return $this->compression_manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCheckerManager()
    {
        return $this->checker_manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPayloadConverter()
    {
        return $this->payload_converter_manager;
    }
}
