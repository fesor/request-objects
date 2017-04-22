<?php

namespace Fesor\RequestObject;

use Fesor\RequestObject\DependeyInjection\RequestObjectExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RequestObjectBundle extends Bundle
{
    protected function getContainerExtensionClass()
    {
        return RequestObjectExtension::class;
    }
}
