<?php

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Request;

interface PayloadResolver
{
    /**
     * Extracts payload from request.
     *
     * You can decorate extractor with your additional
     * logic, normalize input, deserialize json or xml
     * and anything that should help you to work.
     *
     * The only note that payload should be closest
     * to request as it possible.
     *
     * @param Request $request
     *
     * @return array
     */
    public function resolvePayload(Request $request);
}
