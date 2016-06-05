<?php

use \Fesor\RequestObject\RequestBinder;
use \Symfony\Component\Validator\ValidatorBuilder;
use \Symfony\Component\HttpFoundation\Request as HttpRequest;
use \Fesor\RequestObject\HttpPayloadResolver;
use Fesor\RequestObject\Examples;

class ExamplesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RequestBinder
     */
    private $requestBinder;

    function setUp() {
        $this->requestBinder = new RequestBinder(
            new HttpPayloadResolver(),
            (new ValidatorBuilder())->getValidator()
        );
    }

    function testSimpleRequestValidation()
    {
        $requestClassName = Examples\Request\RegisterUserRequest::class;
        $httpRequest = HttpRequest::create('/users', 'POST', [
            'email' => 'user@example.com',
            'password' => 'example',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        $bindedRequest = $this->requestBinder->bind($requestClassName, $httpRequest);
        $request = $bindedRequest->getRequestObject();

        $this->assertEquals('user@example.com', $request->get('email'));
        $this->assertEquals('example', $request->get('password'));
        $this->assertEquals(true, $request->has('first_name'));
        $this->assertEquals(false, $request->has('not-existing'));
        $this->assertEquals('default', $request->get('not-existing', 'default'));
    }
}
