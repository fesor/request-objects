<?php

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\ErrorResponseProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExtendedRegisterUserRequest extends RegisterUserRequest implements ErrorResponseProvider
{
    public function getErrorResponse(ConstraintViolationListInterface $errors)
    {
        return new JsonResponse([
            'message' => 'Please check your data',
            'errors' => array_map(function (ConstraintViolation $violation) {

                return [
                    'path' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage()
                ];
            }, iterator_to_array($errors))
        ], 400);
    }

}