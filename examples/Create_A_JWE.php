<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

include_once __DIR__.'/../vendor/autoload.php';

use Base64Url\Base64Url;
use SpomkyLabs\Service\Jose;

$jose = Jose::getInstance();

$jose->getConfiguration()->set('algorithms', ['A256GCM', 'A256GCMKW']);
$jose->getConfiguration()->set('audience', 'My service');

$jose->getKeysetManager()->loadKeyFromValues('SHARED_KEY', [
    'alg' => 'A256GCMKW',
    'use' => 'enc',
    'kty' => 'oct',
    'k'   => Base64Url::encode(hex2bin('000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F')),
]);

$payload = [
    'iss' => 'My app',
    'exp' => time() + 3600,
    'iat' => time(),
    'nbf' => time(),
    'sub' => 'foo@bar',
    'jti' => '0123456789',
    'aud' => 'My service',
];
$header = [
    'alg' => 'A256GCMKW',
    'enc' => 'A256GCM',
];

$jwe = $jose->encrypt('SHARED_KEY', $payload, $header);

print_r(sprintf("\n\nJWE\n---------------------------------------------\n%s\n---------------------------------------------\n", $jwe));

$loaded = $jose->load($jwe);
if (!$loaded instanceof \Jose\JWEInterface) {
    die('error');
}
$jose->decrypt($loaded);

print_r(sprintf(
    "\n\nLoaded JWE
    \n---------------------------------------------\n
    JWT ID: %s
    Key ID: %s
    Subject: %s
    Algorithm: %s
    Encryption algorithm: %s
    Audience: %s
    Issuer: %s
    Content type: %s
    Expires at: %s
    Issued at: %s
    Not before: %s
    Payload: %s
    Type: %s
    \n---------------------------------------------\n",
    $loaded->getJWTID(),
    $loaded->getKeyID(),
    $loaded->getSubject(),
    $loaded->getAlgorithm(),
    $loaded->getEncryptionAlgorithm(),
    $loaded->getAudience(),
    $loaded->getIssuer(),
    $loaded->getContentType(),
    $loaded->getExpirationTime(),
    $loaded->getIssuedAt(),
    $loaded->getNotBefore(),
    json_encode($loaded->getPayload()),
    $loaded->getType()
));
