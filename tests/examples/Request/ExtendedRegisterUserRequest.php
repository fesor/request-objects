<?php

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\ErrorResponseProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExtendedRegisterUserRequest extends RegisterUserRequest implements ErrorResponseProvider
{
    public function getErrorResponse(ConstraintViolationListInterface $errors)
    {
        return new JsonResponse([
            'message' => 'Please check your data',
            'errors' => $errors
        ], 400);
    }

}