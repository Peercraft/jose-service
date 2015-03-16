<?php

namespace SpomkyLabs\Service\tests;

use SpomkyLabs\Service\Jose;
use Jose\JSONSerializationModes;

class JoseServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $jose = Jose::getInstance();
        $jose->getJWKManager()->addKeyFromValues(
            'e9bc097a-ce51-4036-9562-d2ade882db0d',
            array(
                "kty" => "EC",
                "crv" => "P-256",
                "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
                "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
            )
        );
        $jose->getJWKManager()->addKeyFromValues(
            'PRIVATE_EC',
            array(
                "kty" => "EC",
                "crv" => "P-256",
                "x"   => "f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU",
                "y"   => "x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0",
                "d"   => "jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI",
            )
        );
        $jose->getJWKManager()->addKeyFromValues(
            '7',
            array(
                "kty" => "oct",
                "k"   => "GawgguFyGrWKav7AX4VKUg",
            )
        );
        $jose->getJWKManager()->addRSAKey("PRIVATE_RSA", file_get_contents(__DIR__."/Keys/RSA/private.key"), "tests");
        $jose->getJWKManager()->addRSAKey("PUBLIC_RSA", file_get_contents(__DIR__."/Keys/RSA/public.key"));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The JWT has expired.
     */
    public function testLoadFlattenedJWS()
    {
        $jose = Jose::getInstance();

        $result = $jose->load('{"payload":"eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ","protected":"eyJhbGciOiJFUzI1NiJ9","header":{"kid":"e9bc097a-ce51-4036-9562-d2ade882db0d"},"signature":"DtEhU3ljbEg8L38VWAfUAqOyKAM6-Xx-F4GawxaepmXFCgfTjDxw5djxLa8ISlSApmWQxfKTUJqPP3-Kg6NU1Q"}');

        $this->assertInstanceOf("Jose\JWSInterface", $result);
        $this->assertEquals(array("iss" => "joe", "exp" => 1300819380, "http://example.com/is_root" => true), $result->getPayload());
        $this->assertEquals("ES256", $result->getAlgorithm());
        $jose->verify($result);
    }

    public function testLoadFlattenedJWE()
    {
        $jose = Jose::getInstance();

        $result = $jose->load('{"protected":"eyJlbmMiOiJBMTI4Q0JDLUhTMjU2In0","unprotected":{"jku":"https://server.example.com/keys.jwks"},"header":{"alg":"A128KW","kid":"7"},"encrypted_key":"6KB707dM9YTIgHtLvtgWQ8mKwboJW3of9locizkDTHzBC2IlrT1oOQ","iv":"AxY8DCtDaGlsbGljb3RoZQ","ciphertext":"KDlTtXchhZTGufMYmOYGS4HffxPSUrfmqCHXaI9wOGY","tag":"Mz-VPPyU4RlcuYv1IwIvzw"}');

        $this->assertInstanceOf("Jose\JWEInterface", $result);
        $this->assertEquals("Live long and prosper.", $result->getPayload());
        $this->assertEquals("A128KW", $result->getAlgorithm());
        $this->assertEquals("A128CBC-HS256", $result->getEncryptionAlgorithm());
        $jose->verify($result);
    }

    /**
     * @see https://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39#appendix-A.1
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The JWT has expired.
     */
    public function testLoadIETFExample1()
    {
        $jose = Jose::getInstance();

        $result = $jose->load('eyJ0eXAiOiJKV1QiLA0KICJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ.dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk');

        $this->assertInstanceOf("Jose\JWSInterface", $result);
        $this->assertEquals(array("iss" => "joe", "exp" => 1300819380, "http://example.com/is_root" => true), $result->getPayload());
        $this->assertEquals("HS256", $result->getAlgorithm());
        $jose->verify($result);
    }

    /**
     * @see https://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39#appendix-A.2
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The JWT has expired.
     */
    public function testLoadIETFExample2()
    {
        $jose = Jose::getInstance();

        $result = $jose->load('eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ.cC4hiUPoj9Eetdgtv3hF80EGrhuB__dzERat0XF9g2VtQgr9PJbu3XOiZj5RZmh7AAuHIm4Bh-0Qc_lF5YKt_O8W2Fp5jujGbds9uJdbF9CUAr7t1dnZcAcQjbKBYNX4BAynRFdiuB--f_nZLgrnbyTyWzO75vRK5h6xBArLIARNPvkSjtQBMHlb1L07Qe7K0GarZRmB_eSN9383LcOLn6_dO--xi12jzDwusC-eOkHWEsqtFZESc6BfI7noOPqvhJ1phCnvWh6IeYI2w9QOYEUipUTI8np6LbgGY9Fs98rqVt5AXLIhWkWywlVmtVrBp0igcN_IoypGlUPQGe77Rw');

        $this->assertInstanceOf("Jose\JWSInterface", $result);
        $this->assertEquals(array("iss" => "joe", "exp" => 1300819380, "http://example.com/is_root" => true), $result->getPayload());
        $this->assertEquals("RS256", $result->getAlgorithm());
        $jose->verify($result);
    }

    /**
     * @see https://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39#appendix-A.3
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The JWT has expired.
     */
    public function testLoadIETFExample3()
    {
        $jose = Jose::getInstance();

        $result = $jose->load('eyJhbGciOiJFUzI1NiJ9.eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ.DtEhU3ljbEg8L38VWAfUAqOyKAM6-Xx-F4GawxaepmXFCgfTjDxw5djxLa8ISlSApmWQxfKTUJqPP3-Kg6NU1Q');

        $this->assertInstanceOf("Jose\JWSInterface", $result);
        $this->assertEquals(array("iss" => "joe", "exp" => 1300819380, "http://example.com/is_root" => true), $result->getPayload());
        $this->assertEquals("ES256", $result->getAlgorithm());
        $jose->verify($result);
    }

    /**
     * @see https://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39#appendix-A.4
     */
    public function testLoadIETFExample4()
    {
        $jose = Jose::getInstance();

        $result = $jose->load('eyJhbGciOiJFUzUxMiJ9.UGF5bG9hZA.AdwMgeerwtHoh-l192l60hp9wAHZFVJbLfD_UxMi70cwnZOYaRI1bKPWROc-mZZqwqT2SI-KGDKB34XO0aw_7XdtAG8GaSwFKdCAPZgoXD2YBJZCPEX3xKpRwcdOO8KpEHwJjyqOgzDO7iKvU8vcnwNrmxYbSW9ERBXukOXolLzeO_Jn');

        $this->assertInstanceOf("Jose\JWSInterface", $result);
        $this->assertEquals("Payload", $result->getPayload());
        $this->assertEquals("ES512", $result->getAlgorithm());
    }

    /**
     */
    public function testCreateCompactJWS()
    {
        $jose = Jose::getInstance();

        $jws = $jose->sign(
            "PRIVATE_EC",
            "Je suis Charlie",
            array(
                "alg" => "ES256",
                "kid" => "e9bc097a-ce51-4036-9562-d2ade882db0d",
            )
        );
        $this->assertTrue(is_string($jws));
    }

    /**
     */
    public function testCreateFlattenedJWS()
    {
        $jose = Jose::getInstance();

        $jws = $jose->sign(
            "PRIVATE_EC",
            "Je suis Charlie",
            array(
                "alg" => "ES256",
                "kid" => "e9bc097a-ce51-4036-9562-d2ade882db0d",
            ),
            array(
                "foo" => "bar",
            ),
            JSONSerializationModes::JSON_FLATTENED_SERIALIZATION
        );
        $this->assertTrue(is_string($jws));
    }

    /**
     */
    public function testCreateCompactJWE()
    {
        $jose = Jose::getInstance();

        $jwe = $jose->encrypt(
            "7",
            "Je suis Charlie",
            array(
                "alg" => "A128KW",
                "enc" => "A128CBC-HS256",
                "kid" => "7",
                "zip" => "DEF",
            )
        );

        $result = $jose->load($jwe);

        $this->assertTrue(is_string($jwe));
        $this->assertEquals("A128KW", $result->getAlgorithm());
        $this->assertEquals("A128CBC-HS256", $result->getEncryptionAlgorithm());
        $this->assertEquals("DEF", $result->getZip());
        $this->assertEquals("Je suis Charlie", $result->getPayload());
    }
}
