<?php

namespace Fesor\RequestObject;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class BindedRequest
{
    private $requestObject;

    private $errors;

    /**
     * BindedRequest constructor.
     * @param RequestObject $requestObject
     * @param ConstraintViolationListInterface $errors
     */
    public function __construct(RequestObject $requestObject, ConstraintViolationListInterface $errors)
    {
        $this->requestObject = $requestObject;
        $this->errors = $errors;
    }

    /**
     * @return RequestObject
     */
    public function getRequestObject()
    {
        return $this->requestObject;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @throws InvalidRequestPayloadException
     */
    public function failOnInvalid()
    {
        if (0 === count($this->errors)) {
            return;
        }

        throw new InvalidRequestPayloadException($this->requestObject, $this->errors);
    }
}
