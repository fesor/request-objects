<?php

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBinder
{
    private $payloadResolver;

    private $validator;

    /**
     * RequestBinder constructor.
     * @param PayloadResolver $payloadResolver
     * @param ValidatorInterface $validator
     */
    public function __construct(PayloadResolver $payloadResolver, ValidatorInterface $validator)
    {
        $this->payloadResolver = $payloadResolver;
        $this->validator = $validator;
    }

    /**
     * @param $requestClassName
     * @param HttpRequest $httpRequest
     * @return BindedRequest
     */
    public function bind($requestClassName, HttpRequest $httpRequest)
    {
        $payload = $this->payloadResolver->resolvePayload($httpRequest);
        $requestObject = new $requestClassName($payload);

        return new BindedRequest($requestObject, $this->validateRequest($payload, $requestObject));
    }

    public function bindOrFail($requestClassName, HttpRequest $httpRequest)
    {
        $binded = $this->bind($requestClassName, $httpRequest);
        $binded->failOnInvalid();

        return $binded;
    }

    private function validateRequest(array $payload, Request $requestObject)
    {
        return $this->validator->validate(
            $payload,
            $requestObject->rules(),
            $requestObject->validationGroup()
        );
    }
}
