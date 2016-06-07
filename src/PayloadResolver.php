<?php

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

interface PayloadResolver
{
    /**
     * Extracts payload from request
     *
     * You can decorate extractor with your additional
     * logic, normalize input, deserialize json or xml
     * and anything that should help you to work.
     *
     * The only note that payload should be closest
     * to request as it possible.
     *
     * @param HttpRequest $request
     * @return array
     */
    public function resolvePayload(HttpRequest $request);
}
