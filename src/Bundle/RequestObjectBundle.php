<?php

namespace Fesor\RequestObject\Bundle;

use Fesor\RequestObject\Bundle\DependeyInjection\RequestObjectExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RequestObjectBundle extends Bundle
{
    protected function getContainerExtensionClass()
    {
        return RequestObjectExtension::class;
    }
}
