# How to use

## Under the hood

This Jose service uses Pimple to provides all services you need to create and load JWS and JWE.
It also uses [Spomky-Labs/jose](https://github.com/Spomky-Labs/jose) that implements almost all specifications concerning Jose.

## Service initialisation

Thanks to Pimple, you have only few lines of code to write to initialize services:

```php
use SpomkyLabs\Service\Jose;

$jose = Jose::getInstance();
```

That's it!
Before the creation of our first Jot, we have to configure our services.

## Configuration

Now we have access on the configuration service and we can set and get values using keys.

```php
$jose->getConfiguration()->set('foo', ['bar', 'baz']);
$jose->getConfiguration()->get('foo'); // Returns ['bar', 'baz']
$jose->getConfiguration()->get('unknown key'); // Returns null
$jose->getConfiguration()->get('unknown key', 'default value'); // Returns 'default value'
```

Hereafter, the full configuration keys:

```php
algorithms: an array of signature/content encryption/key encryption algorithms you want to use
compression: an array of compression algorithms you want to use.
             Default is ['DEF'] (deflate). Possible values are 'DEF' (deflate), 'GZ' (GZip) and 'ZLIB' (ZLib).
payload-converter.jwk: a boolean. True (default) will enable the JWK payload converter
payload-converter.jwkset: a boolean. True (default) will enable the JWKSet payload converter
checker.aud: a boolean. True (default) will enable the 'audience' checker
checker.exp: a boolean. True (default) will enable the 'expiration time' checker
checker.nbf: a boolean. True (default) will enable the 'not before' checker
checker.iat: a boolean. True (default) will enable the 'issued at' checker
checker.crit: a boolean. True (default) will enable the 'critical parameters' checker
audience: a string. Required if the 'audience' checker is enabled
```

You can find [here the complete list of supported algorithms](https://github.com/Spomky-Labs/jose/blob/master/doc/Status.md#supported-algorithms).

Most of options are already set. We must only declare algorithms we want to use and the audience (= the name of our service):

```php
$jose->getConfiguration()->set('algorithms', ['ES256', 'A128KW', 'A128CBC-HS256']);
$jose->getConfiguration()->set('audience', 'My service');
```

## Key storage

To encrypt/decrypt sign/verify data, we need to store keys.
These keys are identified with multiple attributes:
* The key ID (`kid` parameter)
* An URL (`jku` or `x5u`)
* A key in the header (`jwk`)
* A certificate chain (`x5c`)
* The algorithm authorized for the key (`alg`)
* The key usage (`use`)
* The key operations (`key_ops`)

For each key, the key ID is mandatory. But we recommend you to also define a key usage (`use`) and an algorithm (`alg`).

The key manager allows you to group your keys. We recommend you to store private keys in a dedicated key set and public keys in another one.
Each key set is identified by a name.

### How to load keys?

In general your keys are in a certificate file (Elliptic curves or RSA keys) or are store in a variable in binary format.

The Key Set manager will help you to import your keys.

#### From a key file

You can load RSA and EC keys from files.
Private encrypted keys are supported:

```php
//Load a private key from a file (password protected key).
$jose->getKeysetManager()->loadKeyFromFile('PRIVATE_KEY_1', '/Keys/RSA/private.key', 'password');
```

*Note that when you import a private RSA/EC key, the public key is automatically stored aat the same time.*

You can add additional key/value during the import:

```php
//Load a private key from a file (password protected key).
$jose->getKeysetManager()->loadKeyFromFile('KEY_1', '/Keys/RSA/private.key', 'password', ['kid'=>'PRIVATE_KEY_1', 'alg'=>'RS512', 'use'=>'sig']);
```

If you want to share the associated public key (e.g. allow external clients to verify your signatures or send you encrypted data), you just have to set last parameter to `true`.  

```php
//Load a private key from a file (password protected key).
$jose->getKeysetManager()->loadKeyFromFile('KEY_1', '/Keys/RSA/private.key', 'password', [], true);
```

#### From a key PEM encoded

You already have an OpenSSL resource that points to your key?

```php
$jose->getKeysetManager()->loadKeyFromPEM('KEY_1', -----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w...');
```

#### From a key an OpenSSL resource

You already have an OpenSSL resource that points to your key?

```php
$jose->getKeysetManager()->loadKeyFromResource('KEY_1', $my_openssl_resource, 'secret);
```

#### From values

```php
$jose->getKeysetManager()->loadKeyFromValues('KEY_1', [
    "kty" => "EC",
    "crv" => "P-256",
    "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
    "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
    "d"   => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI"
]);
```

*Note: if the parameter 'kid' is already set, the first parameter will be ignored*

#### From a JWK object

```php
// Variable $my_private_jwk is an instance of a JWK object
$jose->getKeysetManager()->loadKeyFromValues('KEY_1', $my_private_jwk);
```

*Note: if the parameter 'kid' is already set, the first parameter will be ignored*

### How to retrieve key sets and keys?

You can get all key set names from the key set manager.

```php
$jose->getKeysetManager()->getKeySetNames(); // Returns an array of string: ['private', 'public', 'asymmetric', 'direct']

$keyset = $jose->getKeysetManager()->getKeySet('private'); // Returns the key set that contains all 'private'
```

The key set returned by `getKeySet` is an instance of `JWKSetInterface`.
To get all keys stored in the key set: 

```php
$keyset->getKeys();
```

A key set has the same behaviours as an array:

```php
count($keyset); // Returns the number of keys in the key set
$keyset[2]; // Returns the third key
foreach($keyset as $key) {
   ....
}
```

### How to share keys?

Your public keys should be shared to allow signature verification or clients to encrypt content.

If you stored public keys in a dedicated key set, this operation is really easy.
Let say you want to create a web page with these public keys:

```php
// Load and configure Jose
...
header('Content-Type: application/jwk-set+json; charset=UTF-8');
$public_keys = $jose->getKeysetManager()->getKeySet('Public keys');
echo json_encode($public_keys);
```

This will show you a page with all keys [such as this one](https://www.googleapis.com/oauth2/v3/certs).

## Load a JWS or JWE

```php
//We load the data we received
$jwt = $jose->load('eyJhbGciOiJFUzUxMiJ9.UGF5bG9hZA.AdwMgeerwtHoh-l192l60hp9wAHZFVJbLfD_UxMi70cwnZOYaRI1bKPWROc-mZZqwqT2SI-KGDKB34XO0aw_7XdtAG8GaSwFKdCAPZgoXD2YBJZCPEX3xKpRwcdOO8KpEHwJjyqOgzDO7iKvU8vcnwNrmxYbSW9ERBXukOXolLzeO_Jn');

//$jwt is an object that implements Jose\JWSInterface or Jose\JWSInterface

//If the object is a JWS, you must check its claims (expiration time, audience...) and signature:
//The following method returns true or throws an exception.
$result = $jose->verify($jwt);

//If the object is a JWE, you must check its claims (expiration time, audience...) and decrypt the payload:
//The following method returns true or throws an exception.
$result = $jose->decrypt($jwe);
```

## Signature (JWS) ##

```php
$kid = 'KEY_1';
$message = "Message to sign";
$header = [
    "alg" => "ES256",
    "kid" => "KEY_1",
];

$jws = $jose->sign($kid, $message, $header);

//$jws is a string that represents a JWS JSON Compact Serialization
```

## Encryption (JWE) ##

```php
$kid = 'KEY_1';
$message = "Message to encrypt";
$header = [
    "alg" => "A128KW",
    "enc" => "A128CBC-HS256",
    "kid" => "KEY_1",
    'zip' => 'DEF',
];
$jwe = $jose->encrypt($kid, $message, $header);

//$jwe is a string that represents a JWE JSON Compact Serialization
```

## Signature and Encryption (JWE) ##

```php
$signature_kid = 'KEY_1';
$encryption_kid = 'KEY_2';
$message = "Message to sign and encrypt";
$signature_header = [
    "alg" => "ES256",
    "kid" => "KEY_1",
];
$encryption_header = [
    "alg" => "A128KW",
    "enc" => "A128CBC-HS256",
    "kid" => "KEY_2",
    'zip' => 'DEF',
];
$jwe = $jose->encrypt($message, $signature_kid, $signature_header, $encryption_kid, $encryption_header);

//$jwe is a string that represents a JWE JSON Compact Serialization
```
