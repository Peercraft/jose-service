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

$jose->getConfiguration()->set('algorithms', ['HS512', 'A256GCM', 'A256GCMKW']);
$jose->getConfiguration()->set('audience', 'My service');

$jose->getKeysetManager()->loadKeyFromValues('SIGNATURE_KEY', [
    'alg' => 'HS512',
    'use' => 'sig',
    'kty' => 'oct',
    'k'   => 'GawgguFyGrWKav7AX4VKUg',
]);

$jose->getKeysetManager()->loadKeyFromValues('ENCRYPTION_KEY', [
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
$signature_header = [
    'alg' => 'HS512',
];
$encryption_header = [
    'alg' => 'A256GCMKW',
    'enc' => 'A256GCM',
];

$jwe = $jose->signAndEncrypt($payload, 'SIGNATURE_KEY', $signature_header, 'ENCRYPTION_KEY', $encryption_header);

print_r(sprintf("\n\nJWS+JWE\n---------------------------------------------\n%s\n---------------------------------------------\n", $jwe));

$jws = $jose->load($jwe);

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
    $jws->getJWTID(),
    $jws->getKeyID(),
    $jws->getSubject(),
    $jws->getAlgorithm(),
    $jws->getEncryptionAlgorithm(),
    $jws->getAudience(),
    $jws->getIssuer(),
    $jws->getContentType(),
    $jws->getExpirationTime(),
    $jws->getIssuedAt(),
    $jws->getNotBefore(),
    json_encode($jws->getPayload()),
    $jws->getType()
));

$loaded = $jose->load($jws->getPayload());

print_r(sprintf(
    "\n\nLoaded JWS
    \n---------------------------------------------\n
    JWT ID: %s
    Key ID: %s
    Subject: %s
    Algorithm: %s
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
    $loaded->getAudience(),
    $loaded->getIssuer(),
    $loaded->getContentType(),
    $loaded->getExpirationTime(),
    $loaded->getIssuedAt(),
    $loaded->getNotBefore(),
    json_encode($loaded->getPayload()),
    $loaded->getType()
));

$jose->verify($loaded);
