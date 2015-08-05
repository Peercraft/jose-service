# How to use #

## Load a JWS or JWE ##

```php

    use SpomkyLabs\Service\Jose;

    $jose = Jose::getInstance();
    
    //We declare the algorithms we want to use
    $jose->getConfiguration->set('Algorithms', array('ES256'));
    
    //We add a key. Its ID (kid) is 'e9bc097a-ce51-4036-9562-d2ade882db0d'
    $jose->getJWKManager()->addKeyFromValues(
        'e9bc097a-ce51-4036-9562-d2ade882db0d',
        array(
            "kty" => "EC",
            "crv" => "P-256",
            "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
            "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
            "d"   => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI",
        )
    );

    //We load the data we received
    $jwt = $jose->load('eyJhbGciOiJFUzUxMiJ9.UGF5bG9hZA.AdwMgeerwtHoh-l192l60hp9wAHZFVJbLfD_UxMi70cwnZOYaRI1bKPWROc-mZZqwqT2SI-KGDKB34XO0aw_7XdtAG8GaSwFKdCAPZgoXD2YBJZCPEX3xKpRwcdOO8KpEHwJjyqOgzDO7iKvU8vcnwNrmxYbSW9ERBXukOXolLzeO_Jn');

    //$jwt is an object that implements Jose\JWSInterface or Jose\JWSInterface
    
    //The $jwt has not yet been verified (expiration time, audience...).
    //The following method returns true or throws an exception.
    $result = $jose->verify($jwt);

```

## Signature (JWS) ##

```php

    use SpomkyLabs\Service\Jose;

    $jose = Jose::getInstance();
    
    $jose->getConfiguration->set('Algorithms', array('ES256'));
    
    $jose->getJWKManager()->addKeyFromValues(
        'e9bc097a-ce51-4036-9562-d2ade882db0d',
        array(
            "kty" => "EC",
            "crv" => "P-256",
            "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
            "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
            "d"   => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI",
        )
    );

    $jws = $jose->sign(
        "Message to sign",
        array(
            "alg" => "ES256",
            "kid" => "e9bc097a-ce51-4036-9562-d2ade882db0d",
        )
    );

    //$jws is a string that represents a JWS JSON Compact Serialization

```

## Encryption (JWE) ##

```php

    use SpomkyLabs\Service\Jose;
    

    $jose = Jose::getInstance();
    
    //We want to use encryption/decryption algorithms in this case
    $jose->getConfiguration->set('Algorithms', array('A128KW', 'A128CBC-HS256'));
    
    //We also want to compress the data
    $jose->getConfiguration()->set('Compression', array('DEF'));
    
    $jose->getJWKManager()->addKeyFromValues(
        '7',
        array(
            "kty" => "oct",
            "k"   => "GawgguFyGrWKav7AX4VKUg",
        )
    );

    $jwe = $jose->encrypt(
        "Je suis Charlie",
        array(
            "alg" => "A128KW",
            "enc" => "A128CBC-HS256",
            "kid" => "7",
            'zip' => 'DEF',
        )
    );

    //$jwe is a string that represents a JWE JSON Compact Serialization

```
