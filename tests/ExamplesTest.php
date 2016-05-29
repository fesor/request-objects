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
        $request = $this->requestBinder->bind($requestClassName, $httpRequest);

        $this->assertEquals($request->get('email'), 'user@example.com');
        $this->assertEquals($request->get('password'), 'example');
        $this->assertEquals($request->has('first_name'), true);
        $this->assertEquals($request->has('not-existing'), false);
        $this->assertEquals($request->get('not-existing', 'default'), 'default');
    }
}
