<?php

namespace Fesor\RequestObject\Examples\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ExtendedRegisterUserRequest extends RegisterUserRequest
{
    public function rules()
    {
        return new Assert\Collection(array_merge([
            'additional_field' => new Assert\NotNull([
                'message' => 'Extended request requires additional field',
            ]),
        ], parent::rules()->fields));
    }
}
