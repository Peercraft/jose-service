Create your own converter
=========================

If you want to use classes as payload of a JWE or JWS, you have to options:
* Your class implements `JsonSerializable`
* You have a custom converter and it is enabled

In most cases, you will prefer to implement `JsonSerializable` because it is easy to implement and reliable.
But you may need a converter because
* You cannot modify the class
* You want to perform actions during conversion (database queries, log...).
* You want to set protected header parameters.

This page will show you how to create a custom converter.

# Our class

We create a small class that represents a user.

```php
<?php

namespace Acme;

class User
{
    private $username;
    private $password;
    
    public setUsername($username)
    {
        $this->username = $username;
        
        return $this;
    }
    
    public setPassword($password)
    {
        $this->password = $password;
        
        return $this;
    }
    
    public getUsername()
    {
        return $this->username;
    }
    
    public getPassword()
    {
        return $this->password;
    }
}
```

# Our converter

We will create a converter that accepts `User` class. This converter will add content type header (`cty`) to `acme-user+json`.

```php
<?php

namespace Acme;

use SpomkyLabs\Jose\Payload\PayloadConverterInterface;

class UserConverter implements PayloadConverterInterface
{
    private $database;
    
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    public function isPayloadToStringSupported(array $header, $payload)
    {
        return $payload instanceof User;
    }

    public function isStringToPayloadSupported(array $header, $content)
    {
        return array_key_exists('cty', $header) && $header['cty'] === 'acme-user+json';
    }
    
    public function convertPayloadToString(array &$header, $payload)
    {
        $header['cty'] = 'acme-user+json';
        
        $values = array(
            'username' => $payload->getUsername(),
            'password' => $payload->getPassword(),
            'foo' => 'bar',
            'baz' => $this->database->query('...'),
        );

        return json_encode($values);
    }

    public function convertStringToPayload(array $header, $content)
    {
        $values = json_decode($content, true);
        if (!is_array($values)) {
            throw \InvalidArgumentException('Unable to load the payload.');
        }
        $user = new User();
        //You have to verify the keys exist.
        $user->setUsername($values['username'])
             ->setPassword($values['password']);
             
         //You also have keys 'foo' and 'baz'

        return $user;
    }
}
```

# The converter manager

This library provides a converter manager: `SpomkyLabs\Jose\Payload\PayloadConverterManager`.

Some converters are already enabled:
* JWK converter
* JWKSet converter

To add your converter to this manager, just instantiate your converter and call method `addConverter`:

```php
<?php 

use Acme\UserConverter;
$my_converter = new UserConverter($database);

//$converter_manager is an instance of the payload converter manager
$converter_manager->addConverter($my_converter);
```

Now, all JWS/JWE created or loaded will support `User` object as payload.
