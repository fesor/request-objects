<?php

namespace Fesor\RequestObject\Examples\Request;

use \Fesor\RequestObject\RequestObject;
use \Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest extends RequestObject
{
    public function rules()
    {
        return new Assert\Collection([
            'email' => new Assert\Email(['message' => 'Please fill in valid email']),
            'password' => new Assert\Length(['min' => 4, 'minMessage' => 'Password is to short']),
            'first_name' => new Assert\Type(['type' => 'string', 'message' => 'Please provide your first name']),
            'last_name' => new Assert\Type(['type' => 'string', 'message' => 'Please provide your last name'])
        ]);
    }
}