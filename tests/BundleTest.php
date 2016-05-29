<?php

use \Fesor\RequestObject\Examples;
use \Symfony\Component\HttpFoundation\Request;

class BundleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Examples\AppKernel
     */
    private $kernel;

    function setUp()
    {
        $kernel = new Examples\AppKernel('test', true);
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

        $this->assertEquals($response->getStatusCode(), 201);
        $this->assertEquals(json_decode($response->getContent(), true), $payload);
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
            'last_name' => 'Doe'
        ];
        
        $this->kernel->handle(Request::create('/users', 'POST', $payload));
    }

    function testCustomInvalidRequestResponse()
    {
        $payload = [
            'email' => 'invalid',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];

        $response = $this->kernel->handle(Request::create('/users_custom', 'POST', $payload));
        $this->assertEquals($response->getStatusCode(), 400);
    }

}