<?php

use \Symfony\Component\HttpFoundation\Request;
use Fesor\RequestObject;
use \Fesor\RequestObject\RequestObjectBinder;
use \Fesor\RequestObject\Examples\Request\RegisterUserRequest;
use \Fesor\RequestObject\Examples\Request\CustomizedPayloadRequest;
use Fesor\RequestObject\Examples\Request\ResponseProvidingRequest;
use \Symfony\Component\Validator\ConstraintViolationList;
use \Symfony\Component\Validator\Validator\ValidatorInterface;
use \Symfony\Component\Validator\ConstraintViolation;
use \Symfony\Component\HttpFoundation\Response;

class RequestBinderTest extends PHPUnit_Framework_TestCase
{
    /** @var  Request */
    private $request;

    /** @var  RequestObject\PayloadResolver */
    private $payloadResolver;

    /** @var  ValidatorInterface */
    private $validator;

    function setUp()
    {
        $this->request = Request::create('/');

        $this->payloadResolver = $this->getMockForAbstractClass(\Fesor\RequestObject\PayloadResolver::class);
        $this->payloadResolver->method('resolvePayload')->willReturn([]);

        $this->validator = $this->getMockForAbstractClass(ValidatorInterface::class);
    }

    function testRequestObjectBinding()
    {
        $this->validRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, [$this, 'action']);

        $this->assertTrue($this->request->attributes->has('requestObj'));
        $this->assertInstanceOf(RequestObject\RequestObject::class, $this->request->attributes->get('requestObj'));
    }


    function testRequestObjectBindingOnClosure()
    {
        $this->validRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, function (RegisterUserRequest $requestObj) {});

        $this->assertTrue($this->request->attributes->has('requestObj'));
        $this->assertInstanceOf(RequestObject\RequestObject::class, $this->request->attributes->get('requestObj'));
    }

    function testPassErrorsToAction()
    {
        $this->validRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, function (RegisterUserRequest $requestObj, ConstraintViolationList $errors) {});

        $this->assertTrue($this->request->attributes->has('errors'));
        $this->assertInstanceOf(ConstraintViolationList::class, $this->request->attributes->get('errors'));
    }

    function testPassErrorsToActionOnInvalidRequest()
    {
        $this->invalidRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, function (RegisterUserRequest $requestObj, ConstraintViolationList $errors) {});

        $this->assertTrue($this->request->attributes->has('errors'));
        $this->assertInstanceOf(ConstraintViolationList::class, $this->request->attributes->get('errors'));
    }

    function testFailIfNoErrorResponseProviderFound()
    {
        $this->expectException(RequestObject\InvalidRequestPayloadException::class);
        $this->invalidRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, function (RegisterUserRequest $requestObj) {});

        $this->assertTrue($this->request->attributes->has('errors'));
        $this->assertInstanceOf(ConstraintViolationList::class, $this->request->attributes->get('errors'));
    }

    function testErrorResponseProvider()
    {
        $this->invalidRequest();
        $response = (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, function (ResponseProvidingRequest $requestObj) {});

        $this->assertInstanceOf(Response::class, $response);
    }

    function testErrorResponseProviderAsDependency()
    {
        $errorProvider = $this->getMockForAbstractClass(RequestObject\ErrorResponseProvider::class);
        $errorProvider->expects($this->once())->method('getErrorResponse')->willReturn(new Response());

        $this->invalidRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator, $errorProvider))
            ->bind($this->request, function (RegisterUserRequest $requestObj) {});
    }

    function testRequestWithPayloadResolver()
    {
        $this->payloadResolver->expects($this->never())->method('resolvePayload');
        $this->validRequest();
        (new RequestObjectBinder($this->payloadResolver, $this->validator))
            ->bind($this->request, function (CustomizedPayloadRequest $requestObj) {});
    }

    private function validRequest()
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([]));
    }

    private function invalidRequest()
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('test', 'test', [], [], 'test', null)
        ]));
    }

    // fake
    function action(RegisterUserRequest $requestObj)
    {
    }
}