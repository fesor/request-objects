<?php

namespace Fesor\RequestObject\Examples\App;

use Fesor\RequestObject\Examples\Request\ExtendedRegisterUserRequest;
use Fesor\RequestObject\Examples\Request\RegisterUserRequest;
use Fesor\RequestObject\Examples\Request\ResponseProvidingRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class AppController extends Controller
{
    public function registerUserAction(RegisterUserRequest $request)
    {
        return new JsonResponse($request->all(), 201);
    }
    
    public function registerUserCustomAction(ExtendedRegisterUserRequest $request)
    {
        return new JsonResponse($request->all(), 201);
    }

    public function withErrorResponseAction(ResponseProvidingRequest $request)
    {
        return new JsonResponse($request->all(), 201);
    }
    
    public function noCustomRequestAction($foo = '')
    {
        return new Response(null, 204);
    }

    public function validationResultsAction(RegisterUserRequest $request, ConstraintViolationList $errors)
    {
        return new Response(count($errors), 200, ['Content-Type' => 'text/plain']);
    }
}