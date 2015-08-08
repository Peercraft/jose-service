# PHP JOSE Service

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Build Status](https://travis-ci.org/Spomky-Labs/jose.svg?branch=master)](https://travis-ci.org/Spomky-Labs/jose)
[![HHVM Status](http://hhvm.h4cc.de/badge/Spomky-Labs/jose-service.png)](http://hhvm.h4cc.de/package/Spomky-Labs/jose-service)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369/big.png)](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369)

[![Latest Stable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/stable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Total Downloads](https://poser.pugx.org/Spomky-Labs/jose-service/downloads.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Latest Unstable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/unstable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![License](https://poser.pugx.org/Spomky-Labs/jose-service/license.png)](https://packagist.org/packages/Spomky-Labs/jose-service)

[![StyleCI](https://styleci.io/repos/30558405/shield)](https://styleci.io/repos/30558405)

This project uses [Spomky-Labs/jose](https://github.com/Spomky-Labs/jose) to ease encryption/decryption and signature/verification of JWS and JWE.

## The Release Process ##
The release process [is described here](doc/Release.md).

## Prerequisites ##

This library needs at least

* `PHP 5.4`.

It has been successfully tested using `PHP 5.4` to `PHP 5.6`.

Tests with `PHP 7` and `HHVM` are incomplete because of some optional dependencies not available on these platforms.

## Installation ##

The preferred way to install this library is to rely on Composer:

    composer require spomky-labs/jose-service

## How to use ##

Your classes are ready to use? Have a look at [How to use](doc/Use.md) to create or load your first JWT objects.

## Contributing

Requests for new features, bug fixed and all other ideas to make this library usefull are welcome. [Please follow these best practices](doc/Contributing.md).

## Licence

This software is release under [MIT licence](LICENSE).
