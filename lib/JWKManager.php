<?php

namespace SpomkyLabs\Service;

use SpomkyLabs\Jose\JWK;
use Jose\JWKInterface;
use Jose\JWKManager as Base;
use Base64Url\Base64Url;
use SpomkyLabs\Jose\Util\ECConverter;
use SpomkyLabs\Jose\Util\RSAConverter;

/**
 */
class JWKManager extends Base
{
    private $keys = array();

    /**
     * {@inheritdoc}
     */
    public function createJWK(array $values = array())
    {
        $jwk = new JWK();
        $jwk->setValues($values);

        return $jwk;
    }

    protected function findByKid($header)
    {
        if (!isset($header['kid'])) {
            return;
        }
        return array_key_exists($header['kid'], $this->keys)?$this->keys[$header['kid']]:null;
    }

    protected function getSupportedMethods()
    {
        return array_merge(
            array(
                'findByKid',
            ),
            parent::getSupportedMethods()
        );
    }

    public function addJWKKey($id, JWKInterface $key)
    {
        $this->checkId($id);
        $this->keys[$id] = $key;

        return $this;
    }

    public function addBinaryKey($id, $value)
    {
        $values = array(
            "kty" => "oct",
            "k"   => Base64Url::encode($value),
        );
        
        return $this->addKeyFromValues($id, $values);
    }

    public function addRSAKey($id, $rsa, $passphrase = null)
    {
        $values = RSAConverter::loadKeyFromFile($rsa, $passphrase);

        return $this->addKeyFromValues($id, $values);
    }

    public function addECKey($id, $ec)
    {
        $values = ECConverter::loadKey($ec, $passphrase);
        
        return $this->addKeyFromValues($id, $values);
    }

    private function checkId($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException("ID must be a string.");
        }
    }

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
