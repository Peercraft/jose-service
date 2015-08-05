<?php

namespace SpomkyLabs\Service;

use Jose\JWAManagerInterface;
use Jose\JWTManagerInterface;
use Jose\JWKManagerInterface;
use Jose\JWKSetManagerInterface;
use Jose\Compression\CompressionManagerInterface;
use SpomkyLabs\Jose\Encrypter as Base;

class Encrypter extends Base
{
    /**
     * @var \Jose\JWTManagerInterface
     */
    private $jwt_manager;

    /**
     * @var \Jose\JWAManagerInterface
     */
    private $jwa_manager;

    /**
     * @var \Jose\JWKManagerInterface
     */
    private $jwk_manager;

    /**
     * @var \Jose\JWKSetManagerInterface
     */
    private $jwkset_manager;

    /**
     * @var \Jose\Compression\CompressionManagerInterface
     */
    private $compression_manager;

    /**
     * @param \Jose\JWAManagerInterface                     $jwa_manager
     * @param \Jose\JWTManagerInterface                     $jwt_manager
     * @param \Jose\JWKManagerInterface                     $jwk_manager
     * @param \Jose\JWKSetManagerInterface                  $jwkset_manager
     * @param \Jose\Compression\CompressionManagerInterface $compression_manager
     */
    public function __construct(
        JWAManagerInterface    $jwa_manager,
        JWTManagerInterface    $jwt_manager,
        JWKManagerInterface    $jwk_manager,
        JWKSetManagerInterface $jwkset_manager,
        CompressionManagerInterface $compression_manager
    ) {
        $this->jwt_manager = $jwt_manager;
        $this->jwa_manager = $jwa_manager;
        $this->jwk_manager = $jwk_manager;
        $this->jwkset_manager = $jwkset_manager;
        $this->compression_manager = $compression_manager;
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
     * @param int $size
     *
     * @return string
     * @throws \Exception
     */
    protected function createCEK($size)
    {
        return $this->generateRandomString($size / 8);
    }

    /**
     * @param int $size
     *
     * @return string
     * @throws \Exception
     */
    protected function createIV($size)
    {
        return $this->generateRandomString($size / 8);
    }

    /**
     * @param $length
     *
     * @return string
     * @throws \Exception
     */
    private function generateRandomString($length)
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length); //PHP 7
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length); // Library OpenSSL
        } elseif (function_exists('mcrypt_create_iv')) {
            return mcrypt_create_iv($length); // Extension MCrypt
        } elseif (class_exists('\phpseclib\Crypt\Random')) {
            return \phpseclib\Crypt\Random::string($length); // PHPSecLib
        } else {
            throw new \Exception('Unable to create a random string');
        }
    }
}
