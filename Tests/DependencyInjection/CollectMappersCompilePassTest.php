<?php

namespace Fesor\RequestObject\Tests\DependencyInjection;

use Fesor\RequestObject\DependeyInjection\CollectMappersCompilePass;
use Fesor\RequestObject\DependeyInjection\RequestObjectExtension;
use Fesor\RequestObject\Tests\Fixtures\ExampleRequestMapper;
use Fesor\RequestObject\Tests\Fixtures\ExampleRequestObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Request;

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
            'test.mapper' => ExampleRequestMapper::class
        ]);

        $this->expectBindings([
            ExampleRequestObject::class => ['test.mapper', 'exampleMapper'],
        ]);

        $this->compilePass->process($this->containerMock->reveal());
    }

    private function stubTaggedServices($services)
    {
        $this->containerMock
            ->findTaggedServiceIds(RequestObjectExtension::MAPPER_TAG)
            ->willReturn(array_keys($services))
            ->shouldBeCalled();

        foreach ($services as $id => $service) {

            $definition = new Definition($service);

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
