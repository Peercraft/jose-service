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

use Base64Url\Base64Url;
use Jose\JWKInterface;
use Jose\JWKManager as Base;
use SpomkyLabs\Jose\JWK;
use SpomkyLabs\Jose\Util\ECConverter;
use SpomkyLabs\Jose\Util\RSAConverter;

/**
 */
class JWKManager extends Base
{
    /**
     * @var array
     */
    private $keys = [];

    /**
     * {@inheritdoc}
     */
    public function createJWK(array $values = [])
    {
        $jwk = new JWK();
        $jwk->setValues($values);

        return $jwk;
    }

    /**
     * @param $id
     *
     * @return null|\Jose\JWKInterface
     */
    public function getByKid($id)
    {
        return $this->findByKid(['kid' => $id]);
    }

    /**
     * @param $header
     *
     * @return null|\Jose\JWKInterface
     */
    protected function findByKid($header)
    {
        if (!isset($header['kid'])) {
            return;
        }

        return array_key_exists($header['kid'], $this->keys) ? $this->keys[$header['kid']] : null;
    }

    /**
     * @return array
     */
    protected function getSupportedMethods()
    {
        return array_merge(
            [
                'findByKid',
            ],
            parent::getSupportedMethods()
        );
    }

    /**
     * @param string             $id
     * @param \Jose\JWKInterface $key
     *
     * @return $this
     */
    public function addJWKKey($id, JWKInterface $key)
    {
        $this->checkId($id);
        $this->keys[$id] = $key;

        return $this;
    }

    /**
     * @param string $id
     * @param string $value
     *
     * @return $this
     */
    public function addSymmetricKey($id, $value)
    {
        $values = [
            'kty' => 'oct',
            'k'   => Base64Url::encode($value),
        ];

        return $this->addKeyFromValues($id, $values);
    }

    /**
     * @param string      $id
     * @param string      $rsa
     * @param null|string $passphrase
     *
     * @return $this
     */
    public function addRSAKeyFromFile($id, $rsa, $passphrase = null)
    {
        $values = RSAConverter::loadKeyFromFile($rsa, $passphrase);

        return $this->addKeyFromValues($id, $values);
    }

    /**
     * @param string $id
     * @param string $resource
     *
     * @return $this
     */
    public function addRSAKeyFromOpenSSLResource($id, $resource)
    {
        $values = RSAConverter::loadKeyFromOpenSSLResource($resource);

        return $this->addKeyFromValues($id, $values);
    }

    /**
     * @param string $id
     * @param string $ec
     *
     * @return $this|\SpomkyLabs\Service\JWKManager
     */
    public function addECKeyFromFile($id, $ec)
    {
        $values = ECConverter::loadKeyFromFile($ec);

        return $this->addKeyFromValues($id, $values);
    }

    /**
     * @param string $id
     */
    private function checkId($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('ID must be a string.');
        }
    }

    /**
     * @param string $id
     * @param array  $values
     *
     * @return $this
     */
    public function addKeyFromValues($id, array $values)
    {
        $this->checkId($id);

        if (!empty($values)) {
            $key = $this->createJWK($values);
            $this->keys[$id] = $key;
        }

        return $this;
    }
}
