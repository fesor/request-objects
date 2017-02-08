<?php

namespace Fesor\RequestObject\Tests\Fixtures;

use Symfony\Component\HttpFoundation\Request;

class ConflictOnRequestObjectBindingMapper
{
    public function mapper(Request $request): ExampleRequestObject
    {
        return new ExampleRequestObject();
    }

    public function conflictingMapper(Request $request): ExampleRequestObject
    {
        return new ExampleRequestObject();
    }
}
