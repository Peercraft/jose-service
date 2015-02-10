<?php

namespace SpomkyLabs\Service\Tests;

use SpomkyLabs\Service\Jose;

class JoseServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadFlattenedJWS()
    {
        $jose = Jose::getInstance();
        $jose->addKeyFromValues(
        	'e9bc097a-ce51-4036-9562-d2ade882db0d',
            array(
                "kty" => "EC",
                "crv" => "P-256",
                "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
                "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
                "d"   => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI",
            )
        );

        $result = $jose->load('{"payload":"eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ","protected":"eyJhbGciOiJFUzI1NiJ9","header":{"kid":"e9bc097a-ce51-4036-9562-d2ade882db0d"},"signature":"DtEhU3ljbEg8L38VWAfUAqOyKAM6-Xx-F4GawxaepmXFCgfTjDxw5djxLa8ISlSApmWQxfKTUJqPP3-Kg6NU1Q"}');

        $this->assertInstanceOf("Jose\JWSInterface", $result);
        $this->assertEquals(array("iss" => "joe", "exp" => 1300819380, "http://example.com/is_root" => true), $result->getPayload());
        $this->assertEquals("ES256", $result->getAlgorithm());
        $this->assertTrue($jose->verify($result));
    }

    public function testLoadFlattenedJWE()
    {
        $jose = Jose::getInstance();
        $jose->addKeyFromValues(
        	'7',
        	array(
                "kty" => "oct",
                "k"   => "GawgguFyGrWKav7AX4VKUg",
            )
        );

        $result = $jose->load('{"protected":"eyJlbmMiOiJBMTI4Q0JDLUhTMjU2In0","unprotected":{"jku":"https://server.example.com/keys.jwks"},"header":{"alg":"A128KW","kid":"7"},"encrypted_key":"6KB707dM9YTIgHtLvtgWQ8mKwboJW3of9locizkDTHzBC2IlrT1oOQ","iv":"AxY8DCtDaGlsbGljb3RoZQ","ciphertext":"KDlTtXchhZTGufMYmOYGS4HffxPSUrfmqCHXaI9wOGY","tag":"Mz-VPPyU4RlcuYv1IwIvzw"}');

        $this->assertInstanceOf("Jose\JWEInterface", $result);
        $this->assertEquals("Live long and prosper.", $result->getPayload());
        $this->assertEquals("A128KW", $result->getAlgorithm());
        $this->assertEquals("A128CBC-HS256", $result->getEncryptionAlgorithm());
    }
}
