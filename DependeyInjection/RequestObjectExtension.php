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
    const MAPPER_TAG = 'request_mapper';

    const REQUEST_MAPPER_ID='fesor.request_object.mapper';

    public function load(array $configs, ContainerBuilder $container)
    {

    }
}
