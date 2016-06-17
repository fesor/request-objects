<?php

use \Fesor\RequestObject\Examples;
use \Fesor\RequestObject\Examples\App;
use \Symfony\Component\HttpFoundation\Request;
use \Fesor\RequestObject\InvalidRequestPayloadException;

class BundleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var App\AppKernel
     */
    private $kernel;

    function setUp()
    {
        $kernel = new App\AppKernel('test', true);
        $kernel->boot();

        $this->kernel = $kernel;
    }
    
    function testRequest()
    {
        $payload = [
            'email' => 'user@example.com',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        $response = $this->kernel->handle(Request::create('/users', 'POST', $payload));

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($payload, json_decode($response->getContent(), true));
    }

    /**
     * @expectedException \Fesor\RequestObject\InvalidRequestPayloadException
     */
    function testInvalidRequestData()
    {
        $payload = [
            'email' => 'invalid',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $this->kernel->handle(Request::create('/users', 'POST', $payload));
    }

    function testExtendedRequestObject()
    {
        $this->expectException(InvalidRequestPayloadException::class);
        $payload = [
            'email' => 'invalid',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->kernel->handle(Request::create('/users_extended', 'POST', $payload));
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertCount(1, $responseBody['errors']);
    }

    function testErrorResponseProvidingRequest()
    {
        $payload = [];
        $response = $this->kernel->handle(Request::create('/error_response', 'POST', $payload));
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertCount(1, $responseBody['errors']);
    }

    /**
     * @dataProvider requestPayloadContextsProvider
     */
    function testContextDependingRequest($payload, $isPayloadValid)
    {
        if (!$isPayloadValid) {
            $this->expectException(InvalidRequestPayloadException::class);
        }

        $response = $this->kernel->handle(Request::create('/context_depending', 'POST', $payload));
        if ($isPayloadValid) {
            $this->assertEquals(201, $response->getStatusCode());
        }
    }

    public function requestPayloadContextsProvider()
    {
        return [
            [['context' => 'first','foo' => 'test','buz' => 'test'], true,],
            [['context' => 'first', 'foo' => 'test'], false,],
            [['context' => 'first', 'buz' => 'test1'], false,],
            [['context' => 'second', 'bar' => 'test', 'buz' => 'test'], true,],
            [['context' => 'second', 'bar' => 'test'], false,],
            [['context' => 'second', 'buz' => 'test'], false,],
            [['buz' => 'test'], true,],
        ];
    }

    function testNoCustomRequest()
    {
        $response = $this->kernel->handle(Request::create('/no_request', 'POST', []));
        $this->assertEquals(204, $response->getStatusCode());
    }

    function testHandlingValidationErrorsInAction()
    {
        $response = $this->kernel->handle(Request::create('/validation_results', 'POST', []));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(4, $response->getContent());
    }
}
