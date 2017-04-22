<?php

namespace Fesor\RequestObject\DependeyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CollectMappersCompilePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $mappers = $this->getMappers($container);

        $bindings = [];
        foreach ($mappers as $mapperId => $mapper) {
            $bindings = array_merge_recursive($bindings, $this->configureBindings($mapperId, $mapper));
        }

        $requestMapperDefinition = $container->getDefinition(RequestObjectExtension::REQUEST_MAPPER_ID);
        $requestMapperDefinition->addMethodCall('registerBindings', [$bindings]);
    }

    private function configureBindings(string $id, Definition $mapper)
    {
        $tag = $mapper->getTag(RequestObjectExtension::MAPPER_TAG);

        $classReflection = new \ReflectionClass($mapper->getClass());
        $publicMethods = $classReflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $mapperMethods = array_filter($publicMethods, function (\ReflectionMethod $method) {
            return $method->hasReturnType();
        });

        $bindings = [];
        foreach ($mapperMethods as $method) {
            $bindings[(string) $method->getReturnType()][] = [$id, $method->getName()];
        }

        return $bindings;
    }

    private function getMappers(ContainerBuilder $builder)
    {
        $tags = $builder->findTaggedServiceIds(RequestObjectExtension::MAPPER_TAG);

        uasort($tags, function ($a, $b) {

            return ($a['priority'] ?? 0) <=> ($b['priority'] ?? 0);
        });

        $ids = array_keys($tags);

        return array_combine($ids, array_map(function(string $id) use ($builder) {

            return $builder->getDefinition($id);
        }, $ids));
    }
}