<?php

use Fesor\RequestObject\Examples\App;
use Fesor\RequestObject\InvalidRequestPayloadException;
use Symfony\Component\HttpFoundation\Request;

class BundleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var App\AppKernel
     */
    private $kernel;

    public function setUp()
    {
        $kernel = new App\AppKernel('test', true);
        $kernel->boot();

        $this->kernel = $kernel;
    }

    public function testRequest()
    {
        $payload = [
            'email' => 'user@example.com',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];
        $response = $this->kernel->handle(Request::create('/users', 'POST', $payload));

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals($payload, json_decode($response->getContent(), true));
    }

    /**
     * @expectedException \Fesor\RequestObject\InvalidRequestPayloadException
     */
    public function testInvalidRequestData()
    {
        $payload = [
            'email' => 'invalid',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $this->kernel->handle(Request::create('/users', 'POST', $payload));
    }

    public function testExtendedRequestObject()
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
        self::assertEquals(400, $response->getStatusCode());
        self::assertCount(1, $responseBody['errors']);
    }

    public function testErrorResponseProvidingRequest()
    {
        $payload = [];
        $response = $this->kernel->handle(Request::create('/error_response', 'POST', $payload));
        $responseBody = json_decode($response->getContent(), true);
        self::assertEquals(400, $response->getStatusCode());
        self::assertCount(1, $responseBody['errors']);
    }

    /**
     * @dataProvider requestPayloadContextsProvider
     */
    public function testContextDependingRequest($payload, $isPayloadValid)
    {
        if (!$isPayloadValid) {
            $this->expectException(InvalidRequestPayloadException::class);
        }

        $response = $this->kernel->handle(Request::create('/context_depending', 'POST', $payload));
        if ($isPayloadValid) {
            self::assertEquals(201, $response->getStatusCode());
        }
    }

    public function requestPayloadContextsProvider()
    {
        return [
            [['context' => 'first', 'foo' => 'test', 'buz' => 'test'], true],
            [['context' => 'first', 'foo' => 'test'], false],
            [['context' => 'first', 'buz' => 'test1'], false],
            [['context' => 'second', 'bar' => 'test', 'buz' => 'test'], true],
            [['context' => 'second', 'bar' => 'test'], false],
            [['context' => 'second', 'buz' => 'test'], false],
            [['buz' => 'test'], true],
        ];
    }

    public function testNoCustomRequest()
    {
        $response = $this->kernel->handle(Request::create('/no_request', 'POST', []));
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandlingValidationErrorsInAction()
    {
        $response = $this->kernel->handle(Request::create('/validation_results', 'POST', []));
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(4, $response->getContent());
    }
}
