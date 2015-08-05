<?php

namespace SpomkyLabs\Service;

use Pimple\Container;
use Jose\JWSInterface;
use Jose\JSONSerializationModes;
use SpomkyLabs\Jose\SignatureInstruction;
use SpomkyLabs\Jose\EncryptionInstruction;

class Jose
{
    private static $_instance = null;
    private $container;

    private function __construct()
    {
        $this->container = new Container();

        $this->setConfiguration();
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

    private function setConfiguration()
    {
        $this->container['Configuration'] = function () {
            return new Configuration();
        };
    }

    private function setJWAManager()
    {
        $this->container['JWAManager'] = function ($c) {
            return new JWAManager(
                $c['Configuration']
            );
        };
    }

    private function setJWTManager()
    {
        $this->container['JWTManager'] = function () {
            return new JWTManager();
        };
    }

    private function setJWKManager()
    {
        $this->container['JWKManager'] = function () {
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
            return new CompressionManager(
                $c['Configuration']
            );
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

    /**
     * @return \SpomkyLabs\Service\Configuration
     */
    public function getConfiguration()
    {
        return $this->container['Configuration'];
    }

    /**
     * @return \SpomkyLabs\Service\Signer
     */
    public function getSigner()
    {
        return $this->container['Signer'];
    }

    /**
     * @return \SpomkyLabs\Service\Encrypter
     */
    public function getEncrypter()
    {
        return $this->container['Encrypter'];
    }

    /**
     * @return \SpomkyLabs\Service\Loader
     */
    public function getLoader()
    {
        return $this->container['Loader'];
    }

    /**
     * @return \SpomkyLabs\Service\JWKManager
     */
    public function getJWKManager()
    {
        return $this->container['JWKManager'];
    }

    /**
     * @return \SpomkyLabs\Service\JWKSetManager
     */
    public function getJWKSetManager()
    {
        return $this->container['JWKSetManager'];
    }

    /**
     * @param string $data
     *
     * @return array|\Jose\JWEInterface|\Jose\JWEInterface[]|\Jose\JWSInterface|\Jose\JWSInterface[]|null
     */
    public function load($data)
    {
        return $this->getLoader()->load($data);
    }

    /**
     * @param string $kid
     * @param mixed  $payload
     * @param array  $protected_header
     * @param array  $unprotected_header
     * @param string $mode
     *
     * @return string
     *
     * @throws \Exception
     */
    public function sign($kid, $payload, array $protected_header, array $unprotected_header = array(), $mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION)
    {
        $key = $this->getJWKManager()->getByKid($kid);
        if (null === $key) {
            throw new \Exception('Unable to determine the key used to sign the payload.');
        }
        $instruction = new SignatureInstruction();
        $instruction->setKey($key)
                    ->setProtectedHeader($protected_header)
                    ->setUnprotectedHeader($unprotected_header);

        return $this->getSigner()->sign($payload, array($instruction), $mode);
    }

    /**
     * @param string $kid
     * @param mixed  $payload
     * @param array  $protected_header
     * @param array  $shared_unprotected_header
     * @param string $mode
     * @param null   $aad
     *
     * @return string
     *
     * @throws \Exception
     */
    public function encrypt($kid, $payload, array $protected_header, array $shared_unprotected_header = array(), $mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION, $aad = null)
    {
        $key = $this->getJWKManager()->getByKid($kid);
        if (null === $key) {
            throw new \Exception('Unable to determine the key used to encrypt the payload.');
        }
        $instruction = new EncryptionInstruction();
        $instruction->setRecipientKey($key);

        return $this->getEncrypter()->encrypt($payload, array($instruction), $protected_header, $shared_unprotected_header, $mode, $aad);
    }

    /**
     * @param mixed $jwt
     *
     * @return bool
     */
    public function verify($jwt)
    {
        if (false === $this->getLoader()->verify($jwt)) {
            return false;
        }

        return $jwt instanceof JWSInterface ? $this->getLoader()->verifySignature($jwt) : true;
    }
}
