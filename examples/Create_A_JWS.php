<?php

include_once __DIR__.'/../vendor/autoload.php';

use SpomkyLabs\Service\Jose;

$jose = Jose::getInstance();

$jose->getConfiguration()->set('algorithms', ['HS512']);
$jose->getConfiguration()->set('audience', 'My service');

$jose->getKeysetManager()->loadKeyFromValues('SHARED_KEY',[
    'alg' => 'HS512',
    'use' => 'sig',
    'kty' => 'oct',
    'k'   => 'GawgguFyGrWKav7AX4VKUg'
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
    "alg" => "HS512",
];

$jws = $jose->sign('SHARED_KEY', $payload, $header);

print_r(sprintf("\n\nJWS\n---------------------------------------------\n%s\n---------------------------------------------\n",$jws));

$loaded = $jose->load($jws);

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
