<?php

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\PayloadResolver;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\HttpFoundation\Request;

class CustomizedPayloadRequest extends RequestObject implements PayloadResolver
{
    public function resolvePayload(Request $request)
    {
        $query = $request->query->all();
        // turn string to array of relations
        if (array_key_exists('includes', $query)) {
            $query['includes'] = explode(',', $query['includes']);
        }

        return $query;
    }
}
