<?php

namespace SpomkyLabs\Service;

use SpomkyLabs\Jose\JWAManager as Base;

class JWAManager extends Base
{
    public function __construct()
    {
        $algorithms = array(
            "Signature\HS256",
            "Signature\HS384",
            "Signature\HS512",
            "Signature\ES256",
            "Signature\ES384",
            "Signature\ES512",
            "Signature\None",
            "Signature\RS256",
            "Signature\RS384",
            "Signature\RS512",
            "Signature\PS256",
            "Signature\PS384",
            "Signature\PS512",
            "ContentEncryption\A128GCM",
            "ContentEncryption\A192GCM",
            "ContentEncryption\A256GCM",
            "ContentEncryption\A128CBCHS256",
            "ContentEncryption\A192CBCHS384",
            "ContentEncryption\A256CBCHS512",
            "KeyEncryption\A128KW",
            "KeyEncryption\A192KW",
            "KeyEncryption\A256KW",
            "KeyEncryption\A128GCMKW",
            "KeyEncryption\A192GCMKW",
            "KeyEncryption\A256GCMKW",
            "KeyEncryption\Dir",
            "KeyEncryption\ECDHES",
            "KeyEncryption\ECDHESA128KW",
            "KeyEncryption\ECDHESA192KW",
            "KeyEncryption\ECDHESA256KW",
            "KeyEncryption\PBES2HS256A128KW",
            "KeyEncryption\PBES2HS384A192KW",
            "KeyEncryption\PBES2HS512A256KW",
            "KeyEncryption\RSA15",
            "KeyEncryption\RSAOAEP",
            "KeyEncryption\RSAOAEP256",
        );
        foreach ($algorithms as $algorithm) {
            $alg = "SpomkyLabs\Jose\Algorithm\\".$algorithm;
            try {
                $this->addAlgorithm(new $alg());
            } catch (\Exception $e) {
                printf("Unable to load algorithm %s. Message is: %s\n", $alg, $e->getMessage());
            }
        }

    }
}
