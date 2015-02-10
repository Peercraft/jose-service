<?php

namespace SpomkyLabs\Service;

use Pimple\Container;
use Jose\JWKInterface;
use Jose\JWSInterface;
use SpomkyLabs\Jose\SignatureInstruction;
use SpomkyLabs\Jose\EncryptionInstruction;

class Jose
{
    private static $_instance = null;
    private $container;

    private function __construct()
    {
        $this->container = new Container();

        $this->setJWAManager();
        $this->setJWTManager();
        $this->setJWKManager();
        $this->setJWKSetManager();
        $this->setCompressionManager();
        $this->setLoader();
        $this->setSigner();
        $this->setEncrypter();
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Jose();
        }

        return self::$_instance;
    }

    private function setJWAManager()
    {
        $this->container['JWAManager'] = function ($c) {
            return new JWAManager();
        };
    }

    private function setJWTManager()
    {
        $this->container['JWTManager'] = function ($c) {
            return new JWTManager();
        };
    }

    private function setJWKManager()
    {
        $this->container['JWKManager'] = function ($c) {
            return new JWKManager();
        };
    }

    private function setJWKSetManager()
    {
        $this->container['JWKSetManager'] = function ($c) {
            return new JWKSetManager(
                $c['JWKManager']
            );
        };
    }

    private function setCompressionManager()
    {
        $this->container['CompressionManager'] = function ($c) {
            return new CompressionManager();
        };
    }

    private function setSigner()
    {
        $this->container['Signer'] = function ($c) {
            return new Signer(
                $c['JWAManager'],
                $c['JWTManager'],
                $c['JWKManager'],
                $c['JWKSetManager']
            );
        };
    }

    private function setLoader()
    {
        $this->container['Loader'] = function ($c) {
            return new Loader(
                $c['JWAManager'],
                $c['JWTManager'],
                $c['JWKManager'],
                $c['JWKSetManager'],
                $c['CompressionManager']
            );
        };
    }

    private function setEncrypter()
    {
        $this->container['Encrypter'] = function ($c) {
            return new Encrypter(
                $c['JWAManager'],
                $c['JWTManager'],
                $c['JWKManager'],
                $c['JWKSetManager'],
                $c['CompressionManager']
            );
        };
    }

    private function getSigner()
    {
        return $this->container['Signer'];
    }

    private function getEncrypter()
    {
        return $this->container['Encrypter'];
    }

    private function getLoader()
    {
        return $this->container['Loader'];
    }

    private function getJWKManager()
    {
        return $this->container['JWKManager'];
    }

    private function getJWKSetManager()
    {
        return $this->container['JWKSetManager'];
    }

    public function load($data)
    {
        return $this->getLoader()->load($data);
    }

    public function sign($payload, array $protected_header)
    {
        $key = $this->getJWKManager()->findByHeader($protected_header);
        if (null === $key) {
            throw new \Exception("Unable to determine the key used to sign the payload.");
        }
        $instruction = new SignatureInstruction();
        $instruction->setKey($key)
                    ->setProtectedHeader($protected_header);

        return $this->getSigner()->sign($payload, array($instruction));
    }

    public function encrypt($payload, array $protected_header)
    {
        $key = $this->getJWKManager()->findByHeader($protected_header);
        if (null === $key) {
            throw new \Exception("Unable to determine the key used to sign the payload.");
        }
        $instruction = new EncryptionInstruction();
        $instruction->setRecipientKey($key);

        return $this->getEncrypter()->encrypt($payload, array($instruction), $protected_header);
    }

    public function verify($jwt)
    {
        if (false === $this->getLoader()->verify($jwt)) {
            return false;
        }

        return $jwt instanceof JWSInterface ? $this->getLoader()->verifySignature($jwt) : true;
    }

    public function addJWKKey($id, JWKInterface $key)
    {
        return $this->getJWKManager()->addJWKKey($id, $key);
    }

    public function addBinaryKey($id, $value)
    {
        return $this->getJWKManager()->addBinaryKey($id, $value);
    }

    public function addRSAKey($id, $rsa, $passphrase = null)
    {
        return $this->getJWKManager()->addRSAKey($id, $rsa, $passphrase);
    }

    public function addECKey($id, $ec)
    {
        return $this->getJWKManager()->addECKey($id, $ec);
    }

    public function addKeyFromValues($id, array $values)
    {
        return $this->getJWKManager()->addKeyFromValues($id, $values);
    }
}
