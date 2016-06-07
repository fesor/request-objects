<?php

namespace Fesor\RequestObject;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class HttpPayloadResolver implements PayloadResolver
{
    /**
     * @inheritdoc
     */
    public function resolvePayload(HttpRequest $request)
    {
        if ($this->shouldNotHasRequestBody($request->getMethod())) {
            return $request->query->all();
        }

        return array_merge(
            $request->request->all(),
            $request->files->all()
        );
    }

    private function shouldNotHasRequestBody($methodName)
    {
        return in_array($methodName, ['GET', 'HEAD', 'DELETE']);
    }

}