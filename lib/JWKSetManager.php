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

use Jose\JWKInterface;
use Jose\JWKManagerInterface;
use Jose\JWKSetManager as Base;
use SpomkyLabs\Jose\JWKSet;
use SpomkyLabs\Jose\KeyConverter\ECKey;
use SpomkyLabs\Jose\KeyConverter\KeyConverter;
use SpomkyLabs\Jose\KeyConverter\RSAKey;

/**
 */
class JWKSetManager extends Base
{
    /**
     * @var \Jose\JWKSetInterface[]
     */
    private $key_sets = [];

    /**
     * @var \Jose\JWKManagerInterface
     */
    protected $jwk_manager;

    /**
     * @param \Jose\JWKManagerInterface $jwk_manager
     */
    public function __construct(JWKManagerInterface $jwk_manager)
    {
        $this->jwk_manager = $jwk_manager;
        foreach (['private', 'shared', 'public', 'asymmetric', 'direct'] as $name) {
            $this->key_sets[$name] = $this->createJWKSet();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getJWKManager()
    {
        return $this->jwk_manager;
    }

    protected function getSupportedMethods()
    {
        return array_merge(
            [
                'findByKeyID',
                'findByKeyAlgorithm',
                'findByKeyUsage',
                'findByKeyOperation',
            ],
            parent::getSupportedMethods()
        );
    }

    /**
     * @param $header
     *
     * @return null|\Jose\JWKInterface
     */
    protected function findByKeyID($header)
    {
        if (!isset($header['kid'])) {
            return;
        }
        foreach ($this->key_sets as $name => $key_set) {
            foreach ($key_set->getKeys() as $key) {
                if ($header['kid'] === $key->getKeyID()) {
                    return $key;
                }
            }
        }
    }

    /**
     * @param $header
     *
     * @return null|\Jose\JWKInterface
     */
    protected function findByKeyAlgorithm($header)
    {
        if (!isset($header['alg'])) {
            return;
        }
        foreach ($this->key_sets as $name => $key_set) {
            foreach ($key_set->getKeys() as $key) {
                if ($header['alg'] === $key->getAlgorithm()) {
                    return $key;
                }
            }
        }
    }

    /**
     * @param $header
     *
     * @return null|\Jose\JWKInterface
     */
    protected function findByKeyUsage($header)
    {
        if (!isset($header['use'])) {
            return;
        }
        foreach ($this->key_sets as $name => $key_set) {
            foreach ($key_set->getKeys() as $key) {
                if ($header['use'] === $key->getPublicKeyUse()) {
                    return $key;
                }
            }
        }
    }

    /**
     * @param $header
     *
     * @return null|\Jose\JWKInterface
     */
    protected function findByKeyOperation($header)
    {
        if (!isset($header['key_ops'])) {
            return;
        }
        foreach ($this->key_sets as $name => $key_set) {
            foreach ($key_set->getKeys() as $key) {
                if ($header['key_ops'] === $key->getKeyOperations()) {
                    return $key;
                }
            }
        }
    }

    /**
     * @param string $id
     *
     * @return null|\Jose\JWKInterface
     */
    public function getKeyByKid($id)
    {
        return $this->findByKeyID(['kid' => $id]);
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
     * @param string             $name
     * @param \Jose\JWKInterface $key
     *
     * @throws \Exception
     *
     * @return self
     */
    private function addKeyInKeySet($name, JWKInterface $key)
    {
        if (!array_key_exists($name, $this->key_sets)) {
            $this->key_sets[$name] = $this->createJWKSet();
        }
        $this->key_sets[$name]->addKey($key);

        return $this;
    }

    /**
     * @param       $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $arguments);
        }

        if (0 === strpos($method, 'get') && strlen($method) - 6 === strpos($method, 'KeySet')) {
            $keyset = strtolower(substr($method, 3, strlen($method) - 9));
            if (array_key_exists($keyset, $this->key_sets)) {
                return $this->key_sets[$keyset];
            }
        }
        throw new \BadMethodCallException('Unknown method "'.$method.'""');
    }

    /**
     * @return \Jose\JWKSetInterface[]
     */
    public function getKeySets()
    {
        return $this->key_sets;
    }

    /**
     * @return string[]
     */
    public function getKeySetNames()
    {
        return array_keys($this->key_sets);
    }

    /**
     * @param string      $kid               The key ID
     * @param string      $file              File to load
     * @param null|string $secret            Secret (only for protected private keys
     * @param array       $additional_values Add additional parameters to your key ('alg'=>'RS256'...)
     *
     * @return $this
     */
    public function loadKeyFromFile($kid, $file, $secret = null, array $additional_values = [])
    {
        $values = KeyConverter::loadKeyFromFile($file, $secret);
        $this->loadKeyFromValues($kid, $values, $additional_values);

        return $this;
    }

    /**
     * @param string      $kid               The key ID
     * @param string      $data              PEM content
     * @param null|string $secret            Secret (only for protected private keys
     * @param array       $additional_values Add additional parameters to your key ('alg'=>'RS256'...)
     *
     * @return $this
     */
    public function loadKeyFromPEM($data, $kid, $secret = null, array $additional_values = [])
    {
        $values = KeyConverter::loadKeyFromPEM($data, $secret);
        $this->loadKeyFromValues($kid, $values, $additional_values);

        return $this;
    }

    /**
     * @param string   $kid               The key ID
     * @param resource $resource          OpenSSL resource
     * @param array    $additional_values Add additional parameters to your key ('alg'=>'RS256'...)
     *
     * @return $this
     */
    public function loadKeyFromResource($kid, $resource, array $additional_values = [])
    {
        $values = KeyConverter::loadKeyFromResource($resource);
        $this->loadKeyFromValues($kid, $values, $additional_values);

        return $this;
    }

    /**
     * @param string $kid               The key ID
     * @param array  $values            Array of values that represent a key
     * @param array  $additional_values Add additional parameters to your key ('alg'=>'RS256'...)
     *
     * @return $this
     */
    public function loadKeyFromValues($kid, array $values, array $additional_values = [])
    {
        if (!array_key_exists('kty', $values)) {
            throw new \InvalidArgumentException('Unable to determine the key type');
        }
        /*
         * @var \Jose\JWKInterface[]
         */
        $keys = [];
        switch ($values['kty']) {
            case 'RSA':
                $rsa = new RSAKey($values);
                if ($rsa->isPrivate()) {
                    $keys['private'] = $this->getJWKManager()->createJWK(array_merge($rsa->toArray(), $additional_values));
                }
                $keys['public'] = $this->getJWKManager()->createJWK(
                    array_merge(
                        RSAKey::toPublic($rsa)->toArray(),
                        $additional_values
                    )
                );
                break;
            case 'EC':
                $rsa = new ECKey($values);
                if ($rsa->isPrivate()) {
                    $keys['private'] = $this->getJWKManager()->createJWK(
                        array_merge(
                            $rsa->toArray(),
                            $additional_values
                        )
                    );
                }
                $keys['public'] = $this->getJWKManager()->createJWK(array_merge(ECKey::toPublic($rsa)->toArray(), $additional_values));
                break;
            case 'oct':
                $keys['asymmetric'] = $this->getJWKManager()->createJWK(array_merge($values, $additional_values));
                break;
            case 'dir':
                $keys['direct'] = $this->getJWKManager()->createJWK(array_merge($values, $additional_values));
                break;
            case 'none':
                break;
            default:
                throw new \InvalidArgumentException('Unsupported key type');
        }

        foreach ($keys as $name => $jwk) {
            if (is_null($jwk->getKeyID())) {
                $jwk->setValue('kid', $kid);
            }
            $this->addKeyInKeySet($name, $jwk);
        }

        return $this;
    }

    /**
     * @param string             $kid The key ID
     * @param \Jose\JWKInterface $jwk A JWK object
     *
     * @return $this
     */
    public function loadKeyFromJWK($kid, JWKInterface $jwk)
    {
        $this->loadKeyFromValues($kid, $jwk->getValues());

        return $this;
    }
}
