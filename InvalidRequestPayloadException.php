<?php

namespace Fesor\RequestObject;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidRequestPayloadException extends \Exception
{
    private $requestObject;

    private $errors;

    /**
     * InvalidRequestPayloadException constructor.
     *
     * @param RequestObject                    $requestObject
     * @param ConstraintViolationListInterface $errors
     */
    public function __construct(RequestObject $requestObject, ConstraintViolationListInterface $errors)
    {
        parent::__construct();

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
}
