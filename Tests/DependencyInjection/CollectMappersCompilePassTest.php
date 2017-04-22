<?php

namespace Fesor\RequestObject\Tests\DependencyInjection;

use Fesor\RequestObject\DependeyInjection\CollectMappersCompilePass;
use Fesor\RequestObject\DependeyInjection\RequestObjectExtension;
use Fesor\RequestObject\Tests\Fixtures\ExampleRequestMapper;
use Fesor\RequestObject\Tests\Fixtures\ExampleRequestMapperV2;
use Fesor\RequestObject\Tests\Fixtures\ExampleRequestObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CollectMappersCompilePassTest extends TestCase
{
    /**
     * @var CollectMappersCompilePass
     */
    private $compilePass;
    private $containerMock;
    private $definitionMock;

    protected function setUp()
    {
        $this->containerMock = $this->prophesize(ContainerBuilder::class);
        $this->definitionMock = $this->prophesize(Definition::class);

        $this->containerMock
            ->getDefinition(RequestObjectExtension::REQUEST_MAPPER_ID)
            ->willReturn($this->definitionMock->reveal());

        $this->compilePass = new CollectMappersCompilePass();
    }

    public function testRegisterBindings()
    {
        $this->stubTaggedServices([
            'test.mapper' => [ExampleRequestMapper::class, ['priority' => 10]],
            'test.mapper.v2' => ExampleRequestMapperV2::class,
        ]);

        $this->expectBindings([
            ExampleRequestObject::class => [
                ['test.mapper.v2', 'exampleMapper'],
                ['test.mapper', 'exampleMapper'],
            ],
        ]);

        $this->compilePass->process($this->containerMock->reveal());
    }

    private function stubTaggedServices($services)
    {
        $definitions = [];
        $tags = [];

        foreach ($services as $id => $service) {
            if (!is_array($service)) $service = [$service, []];
            list($className, $tagAttributes) = $service;

            $tags[$id] = $tagAttributes;
            $definition = new Definition($className);
            $definition->addTag(RequestObjectExtension::MAPPER_TAG, $tagAttributes);
            $definitions[$id] = $definition;
        }

        $this->containerMock
            ->findTaggedServiceIds(RequestObjectExtension::MAPPER_TAG)
            ->willReturn($tags)
            ->shouldBeCalled();

        foreach ($definitions as $id => $definition) {
            $this->containerMock
                ->getDefinition($id)
                ->willReturn($definition);
        }
    }

    private function expectBindings(array $bindings)
    {
        $this->definitionMock->addMethodCall('registerBindings', [$bindings])->shouldBeCalled();
    }
}
