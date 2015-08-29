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

use Jose\JSONSerializationModes;
use Jose\JWEInterface;
use Jose\JWKInterface;
use Jose\JWKSetInterface;
use Jose\JWSInterface;
use Pimple\Container;
use SpomkyLabs\Jose\Checker\AudienceChecker;
use SpomkyLabs\Jose\Checker\CheckerManager;
use SpomkyLabs\Jose\Encrypter;
use SpomkyLabs\Jose\EncryptionInstruction;
use SpomkyLabs\Jose\Loader;
use SpomkyLabs\Jose\Payload\JWKConverter;
use SpomkyLabs\Jose\Payload\JWKSetConverter;
use SpomkyLabs\Jose\Payload\PayloadConverterManager;
use SpomkyLabs\Jose\SignatureInstruction;
use SpomkyLabs\Jose\Signer;

class Jose
{
    /**
     * @var null|\SpomkyLabs\Service\Jose
     */
    private static $_instance = null;

    /**
     * @var \Pimple\Container
     */
    private $container;

    /**
     *
     */
    private function __construct()
    {
        $this->container = new Container();

        $this->setConfiguration();
        $this->setJWAManager();
        $this->setJWTManager();
        $this->setJWKManager();
        $this->setJWKSetManager();
        $this->loadServices();
        $this->setCompressionManager();
        $this->setCheckerManager();
        $this->setPayloadConverterManager();
        $this->setLoader();
        $this->setSigner();
        $this->setEncrypter();
    }

    /**
     * @return \SpomkyLabs\Service\Jose
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     *
     */
    private function setConfiguration()
    {
        $this->container['Configuration'] = function () {
            $config = new Configuration();
            $config
                ->set('compression', ['DEF'])
                ->set('payload-converter.jwk', true)
                ->set('payload-converter.jwkset', true)
                ->set('checker.aud', true)
                ->set('checker.exp', true)
                ->set('checker.nbf', true)
                ->set('checker.iat', true)
                ->set('checker.crit', true)
                ->set('checker.iss', true);

            return $config;
        };
    }

    /**
     *
     */
    private function setJWAManager()
    {
        $this->container['JWAManager'] = function ($c) {
            return new JWAManager(
                $c['Configuration']
            );
        };
    }

    /**
     *
     */
    private function setJWTManager()
    {
        $this->container['JWTManager'] = function () {
            return new JWTManager();
        };
    }

    /**
     *
     */
    private function setJWKManager()
    {
        $this->container['JWKManager'] = function () {
            return new JWKManager();
        };
    }

    /**
     *
     */
    private function setJWKSetManager()
    {
        $this->container['JWKSetManager'] = function ($c) {
            return new JWKSetManager(
                $c['JWKManager']
            );
        };
    }

    /**
     *
     */
    private function setCompressionManager()
    {
        $this->container['CompressionManager'] = function ($c) {
            return new CompressionManager(
                $c['Configuration']
            );
        };
    }

    /**
     * @return \SpomkyLabs\Jose\Payload\PayloadConverterManagerInterface
     */
    public function loadServices()
    {
        $this->container['Checker.Audience'] = function ($c) {
            $audience = $c['Configuration']->get('audience');
            if (is_null($audience)) {
                throw new \RuntimeException('Audience not defined in the configuration.');
            }

            return new AudienceChecker($audience);
        };
        $this->container['PayloadConverter.JWK'] = function ($c) {
            return new JWKConverter($c['JWKManager']);
        };
        $this->container['PayloadConverter.JWKSet'] = function ($c) {
            return new JWKSetConverter($c['JWKSetManager']);
        };
        $checkers = [
            'Checker.IssuedAt'   => 'SpomkyLabs\Jose\Checker\IssuedAtChecker',
            'Checker.NotBefore'  => 'SpomkyLabs\Jose\Checker\NotBeforeChecker',
            'Checker.Expiration' => 'SpomkyLabs\Jose\Checker\ExpirationChecker',
            'Checker.Critical'   => 'SpomkyLabs\Jose\Checker\CriticalChecker',
        ];
        foreach ($checkers as $service => $class) {
            $this->container[$service] = function () use ($class) {
                return new $class();
            };
        }
    }

    /**
     * @return \SpomkyLabs\Jose\Payload\PayloadConverterManagerInterface
     */
    public function getPayloadConverterManager()
    {
        return $this->container['PayloadConverterManager'];
    }

    /**
     *
     */
    private function setPayloadConverterManager()
    {
        $this->container['PayloadConverterManager'] = function ($c) {
            $payload_converter_manager = new PayloadConverterManager();
            $converters = [
                'jwk'    => 'PayloadConverter.JWK',
                'jwkset' => 'PayloadConverter.JWKSet',
            ];
            foreach ($converters as $converter => $service) {
                if (true === $c['Configuration']->get("payload-converter.$converter")) {
                    $payload_converter_manager->addConverter($c[$service]);
                }
            }

            return $payload_converter_manager;
        };
    }

    /**
     *
     */
    private function setCheckerManager()
    {
        $this->container['CheckerManager'] = function ($c) {
            $checker_manager = new CheckerManager();
            $checkers = [
                'aud'  => 'Checker.Audience',
                'exp'  => 'Checker.IssuedAt',
                'iat'  => 'Checker.NotBefore',
                'crit' => 'Checker.Expiration',
                'iss'  => 'Checker.Critical',
            ];
            foreach ($checkers as $checker => $service) {
                if (true === $c['Configuration']->get("checker.$checker")) {
                    $checker_manager->addChecker($c[$service]);
                }
            }

            return $checker_manager;
        };

        return $this;
    }

    /**
     * @return \SpomkyLabs\Jose\Checker\CheckerManagerInterface
     */
    public function getCheckerManager()
    {
        return $this->container['CheckerManager'];
    }

    /**
     *
     */
    private function setSigner()
    {
        $this->container['Signer'] = function ($c) {
            $signer = new Signer();
            $signer->setPayloadConverter($c['PayloadConverterManager'])
                   ->setJWAManager($c['JWAManager'])
                   ->setJWKManager($c['JWKManager'])
                   ->setJWKSetManager($c['JWKSetManager'])
                   ->setJWTManager($c['JWTManager']);

            return $signer;
        };
    }

    /**
     *
     */
    private function setLoader()
    {
        $this->container['Loader'] = function ($c) {
            $loader = new Loader();
            $loader->setPayloadConverter($c['PayloadConverterManager'])
                ->setCompressionManager($c['CompressionManager'])
                ->setCheckerManager($c['CheckerManager'])
                ->setJWAManager($c['JWAManager'])
                ->setJWKManager($c['JWKManager'])
                ->setJWKSetManager($c['JWKSetManager'])
                ->setJWTManager($c['JWTManager']);

            return $loader;
        };
    }

    /**
     *
     */
    private function setEncrypter()
    {
        $this->container['Encrypter'] = function ($c) {
            $encrypter = new Encrypter();
            $encrypter->setPayloadConverter($c['PayloadConverterManager'])
                ->setCompressionManager($c['CompressionManager'])
                ->setJWAManager($c['JWAManager'])
                ->setJWKManager($c['JWKManager'])
                ->setJWKSetManager($c['JWKSetManager'])
                ->setJWTManager($c['JWTManager']);

            return $encrypter;
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
     * @return \SpomkyLabs\Jose\Signer
     */
    public function getSigner()
    {
        return $this->container['Signer'];
    }

    /**
     * @return \SpomkyLabs\Jose\Encrypter
     */
    public function getEncrypter()
    {
        return $this->container['Encrypter'];
    }

    /**
     * @return \SpomkyLabs\Jose\Loader
     */
    public function getLoader()
    {
        return $this->container['Loader'];
    }

    /**
     * @return \SpomkyLabs\Service\JWKSetManager
     */
    public function getKeysetManager()
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
     * @throws \Exception
     *
     * @return string
     */
    public function sign($kid, $payload, array $protected_header, array $unprotected_header = [], $mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION)
    {
        $key = $this->getKeysetManager()->getKeyByKid($kid);
        if (!$key instanceof JWKInterface) {
            throw new \Exception('Unable to determine the key used to sign the payload.');
        }
        if (!array_key_exists('kid', $protected_header)) {
            $protected_header['kid'] = $kid;
        }

        $instruction = new SignatureInstruction();
        $instruction->setKey($key)
                    ->setProtectedHeader($protected_header)
                    ->setUnprotectedHeader($unprotected_header);

        return $this->getSigner()->sign($payload, [$instruction], $mode);
    }

    /**
     * @param string $kid
     * @param mixed  $payload
     * @param array  $protected_header
     * @param array  $shared_unprotected_header
     * @param string $mode
     * @param null   $aad
     *
     * @throws \Exception
     *
     * @return string
     */
    public function encrypt($kid, $payload, array $protected_header, array $shared_unprotected_header = [], $mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION, $aad = null)
    {
        $key = $this->getKeysetManager()->getKeyByKid($kid);
        if (!$key instanceof JWKInterface) {
            throw new \Exception('Unable to determine the key used to encrypt the payload.');
        }
        if (!array_key_exists('kid', $protected_header)) {
            $protected_header['kid'] = $kid;
        }
        $instruction = new EncryptionInstruction();
        $instruction->setRecipientKey($key);

        return $this->getEncrypter()->encrypt($payload, [$instruction], $protected_header, $shared_unprotected_header, $mode, $aad);
    }

    /**
     * @param \Jose\JWEInterface         $jwe
     * @param \Jose\JWKSetInterface|null $jwk_set
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function decrypt(JWEInterface $jwe, JWKSetInterface $jwk_set = null)
    {
        if (false === $this->getLoader()->verify($jwe) ) {
            return false;
        }

        if (false === $this->getLoader()->decrypt($jwe, $jwk_set)) {
            throw new \RuntimeException('Unable to decrypt.');
        }
        return true;
    }

    /**
     * @param \Jose\JWSInterface         $jws
     * @param \Jose\JWKSetInterface|null $jwk_set
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function verify(JWSInterface $jws, JWKSetInterface $jwk_set = null)
    {
        if (false === $this->getLoader()->verify($jws) ) {
            return false;
        }

        if (false === $this->getLoader()->verifySignature($jws, $jwk_set)) {
            throw new \RuntimeException('Bad signature.');
        }
        return true;
    }

    /**
     * @param mixed  $payload
     * @param        $signature_kid
     * @param array  $signature_protected_header
     * @param        $encryption_kid
     * @param array  $encryption_protected_header
     * @param array  $signature_unprotected_header
     * @param array  $encryption_shared_unprotected_header
     * @param string $mode
     * @param null   $aad
     *
     * @throws \Exception
     *
     * @return string
     */
    public function signAndEncrypt(
        $payload,
        $signature_kid,
        array $signature_protected_header,
        $encryption_kid,
        array $encryption_protected_header,
        array $signature_unprotected_header = [],
        array $encryption_shared_unprotected_header = [],
        $mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION,
        $aad = null
    ) {
        $jws = $this->sign($signature_kid, $payload, $signature_protected_header, $signature_unprotected_header, $mode);

        return $this->encrypt($encryption_kid, $jws, $encryption_protected_header, $encryption_shared_unprotected_header, $mode, $aad);
    }
}
