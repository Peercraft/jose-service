# PHP JOSE Service

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Build Status](https://travis-ci.org/Spomky-Labs/jose.svg?branch=master)](https://travis-ci.org/Spomky-Labs/jose)
[![HHVM Status](http://hhvm.h4cc.de/badge/Spomky-Labs/jose-service.png)](http://hhvm.h4cc.de/package/Spomky-Labs/jose-service)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369/big.png)](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369)

[![Latest Stable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/stable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Total Downloads](https://poser.pugx.org/Spomky-Labs/jose-service/downloads.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Latest Unstable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/unstable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![License](https://poser.pugx.org/Spomky-Labs/jose-service/license.png)](https://packagist.org/packages/Spomky-Labs/jose-service)

[![StyleCI](https://styleci.io/repos/30558405/shield)](https://styleci.io/repos/30558405)

This project uses [Spomky-Labs/jose](https://github.com/Spomky-Labs/jose) to ease encryption/decryption and signature/verification of JWS and JWE.

## The Release Process
The release process [is described here](doc/Release.md).

## Prerequisites

This library needs at least

* `PHP 5.4`.

Depending on algorithms you want to use, please consider the following optional requirements:
* Elliptic Curves based algorithms (`ESxxx` signatures, `ECDHES` encryptions):
    * [`mdanter/ecc`](https://github.com/mdanter/phpecc) (v0.3) library.
* RSA based algorithms (`RSxxx` or `PSxxx` signatures, `RSA1_5`, `RSA_OAEP`, `RSA_OAEP-256`...):
    * `phpseclib/phpseclib` (v2.0.x).
* Password Based Key Derivation Function 2 (PBKDF2) based algorithms (`PBES2-*`):
    * [`spomky-labs/pbkdf2`](https://github.com/spomky-labs/pbkdf2).
* Key Wrapped based algorithms (`A128KW`, `PBES2-HS256+A128KW`...):
    * [`spomky-labs/aes-key-wrap`](https://github.com/spomky-labs/aes-key-wrap).
* AES based algorithms (excluding `AES-GCM`):
    * `OpenSSL` library for AES algorithms.
    * or `MCrypt` library for AES algorithms.
    * or `phpseclib/phpseclib` (v2.0.x).
* AES-GCM based algorithms:
    * [PHP Crypto](https://github.com/bukka/php-crypto) Extension for AES GCM algorithms (not available on `PHP 7` and `HHVM`).

It has been successfully tested using `PHP 5.4` to `PHP 5.6`.

Tests with `PHP 7` and `HHVM` are incomplete because of some optional dependencies not available on these platforms.

## Installation

The preferred way to install this library is to rely on Composer:

    composer require spomky-labs/jose-service

## How to use

Your classes are ready to use? Have a look at [How to use](doc/Use.md) to create or load your first JWT objects.

## Contributing

Requests for new features, bug fixed and all other ideas to make this library useful are welcome. [Please follow these best practices](doc/Contributing.md).

## Licence

This software is release under [MIT licence](LICENSE).
