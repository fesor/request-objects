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
     * @return Request
     */
    public function bind($requestClassName, HttpRequest $httpRequest)
    {
        $payload = $this->payloadResolver->resolvePayload($httpRequest);
        $requestObject = new $requestClassName($payload);

        if ($requestObject instanceof ValidationRequiredRequest) {
            $this->validateRequest($payload, $requestObject);
        }

        return $requestObject;
    }

    private function validateRequest(array $payload, ValidationRequiredRequest $requestObject)
    {
        $errors = $this->validator->validate(
            $payload,
            $requestObject->rules(),
            $requestObject->validationGroup()
        );

        if (0 === count($errors)) {
            return;
        }

        throw new InvalidRequestPayloadException($requestObject, $errors);
    }
}
