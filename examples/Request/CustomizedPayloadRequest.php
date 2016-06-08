<?php

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\PayloadResolver;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CustomizedPayloadRequest extends RequestObject implements PayloadResolver
{
    public function resolvePayload(Request $request)
    {
        $query = $request->query->all();
        // turn string to array of relations
        if (isset($query['includes'])) {
            $query['includes'] = explode(',', $query['includes']);
        }

        return $query;
    }
}
