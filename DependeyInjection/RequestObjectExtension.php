<?php

namespace Fesor\RequestObject\DependeyInjection;

use Fesor\RequestObject\Bundle\RequestObjectEventListener;
use Fesor\RequestObject\HttpPayloadResolver;
use Fesor\RequestObject\PayloadResolver;
use Fesor\RequestObject\RequestObjectBinder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class RequestObjectExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }
}
