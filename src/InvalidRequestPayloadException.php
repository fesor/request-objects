<?php

namespace Fesor\RequestObject;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidRequestPayloadException extends \Exception
{
    private $requestObject;

    private $errors;

    /**
     * InvalidRequestPayloadException constructor.
     * @param ValidationRequiredRequest $requestObject
     * @param ConstraintViolationListInterface $errors
     */
    public function __construct(ValidationRequiredRequest $requestObject, ConstraintViolationListInterface $errors)
    {
        $this->requestObject = $requestObject;
        $this->errors = $errors;
    }
}