<?php

namespace Fesor\RequestObject\DependeyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CollectMappersCompilePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $mappers = $container->findTaggedServiceIds(RequestObjectExtension::MAPPER_TAG);

        $bindings = [];
        foreach ($mappers as $mapperId) {
            $bindings = array_merge($bindings, $this->configureBindings($mapperId, $container->getDefinition($mapperId)));
        }

        $requestMapperDefinition = $container->getDefinition(RequestObjectExtension::REQUEST_MAPPER_ID);
        $requestMapperDefinition->addMethodCall('registerBindings', [$bindings]);
    }

    private function configureBindings(string $id, Definition $mapper)
    {
        $classReflection = new \ReflectionClass($mapper->getClass());
        $publicMethods = $classReflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $mapperMethods = array_filter($publicMethods, function (\ReflectionMethod $method) {
            return $method->hasReturnType();
        });

        $bindings = [];
        foreach ($mapperMethods as $method) {
            $bindings[(string) $method->getReturnType()] = [$id, $method->getName()];
        }

        return $bindings;
    }
}