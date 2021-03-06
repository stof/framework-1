<?php

declare(strict_types=1);

namespace Viserio\Component\Config\Tests\Integration\Container\Pipeline\Integration\Compiled;

/**
 * This class has been auto-generated by Viserio Container Component.
 */
final class ResolveConfigDefinitionPipeContainerTestConfigDefinitionWithNotDefaultConfig extends \Viserio\Component\Container\AbstractCompiledContainer
{
    /**
     * Create a new Compiled Container instance.
     */
    public function __construct()
    {
        $this->services = $this->privates = [];
        $this->parameters = [
            'doctrine' => [
                'connection' => [],
            ],
        ];
        $this->methodMapping = [
            'foo' => 'get55df4251026261c15e5362b72748729c5413605491a6b31caf07b0571c04af5f',
        ];
    }

    /**
     * Returns the public foo shared service.
     *
     * @return \stdClass
     */
    protected function get55df4251026261c15e5362b72748729c5413605491a6b31caf07b0571c04af5f(): \stdClass
    {
        return $this->services['foo'] = new \stdClass(new \Viserio\Component\Config\Container\Pipeline\ConfigBag([], $this->getParameter('doctrine.connection')));
    }

    /**
     * {@inheritdoc}
     */
    public function getRemovedIds(): array
    {
        return [
            \Psr\Container\ContainerInterface::class => true,
            \Viserio\Component\Config\Command\ConfigDumpCommand::class => true,
            \Viserio\Component\Config\Command\ConfigReaderCommand::class => true,
            \Viserio\Contract\Container\CompiledContainer::class => true,
            \Viserio\Contract\Container\Factory::class => true,
            \Viserio\Contract\Container\TaggedContainer::class => true,
            'container' => true,
        ];
    }
}
