<?php

namespace Fesor\RequestObject\Examples;

use Fesor\RequestObject\Examples\Request\ExtendedRegisterUserRequest;
use Fesor\RequestObject\Examples\Request\RegisterUserRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisterUserController extends Controller
{
    public function registerUserAction(RegisterUserRequest $request)
    {
        return new JsonResponse($request->all(), 201);
    }
    
    public function registerUserCustomAction(ExtendedRegisterUserRequest $request)
    {
        return new JsonResponse($request->all(), 201);
    }
}