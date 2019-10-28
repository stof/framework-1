<?php

declare(strict_types=1);

namespace Viserio\Component\Container\Tests\IntegrationTest\Dumper\Compiled;

/**
 * This class has been auto-generated by Viserio Container Component.
 */
final class PhpDumperContainerTestContainerCanBeDumpedWithParameters extends \Viserio\Component\Container\AbstractCompiledContainer
{
    /**
     * Create a new Compiled Container instance.
     */
    public function __construct()
    {
        $this->services = $this->privates = [];
        $this->parameters = [
            'null' => null,
            'true' => true,
            'false' => false,
            'int1' => 1,
            'int0' => 0,
            'float' => 31.1,
            'empty' => '',
            'Foo' => 'bar',
            'BAR' => 'foo',
            'foo' => 'bar',
            'baz' => 'foo is {}foo baz',
            'escape' => '@escapeme',
            'binary' => '����',
            'binary-control-char' => 'This is a Bell char ',
            'true2' => 'true',
            'false2' => 'false',
            'null2' => 'null',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRemovedIds(): array
    {
        return [
            \Psr\Container\ContainerInterface::class => true,
            \Viserio\Contract\Container\Factory::class => true,
            \Viserio\Contract\Container\TaggedContainer::class => true,
            'container' => true,
        ];
    }
}