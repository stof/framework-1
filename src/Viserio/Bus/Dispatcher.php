<?php
declare(strict_types=1);
namespace Viserio\Bus;

use Closure;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Viserio\Contracts\Bus\Dispatcher as DispatcherContract;
use Viserio\Contracts\Container\Traits\ContainerAwareTrait;
use Viserio\Pipeline\Pipeline;
use Viserio\Support\Invoker;

class Dispatcher implements DispatcherContract
{
    use ContainerAwareTrait;

    /**
     * The pipeline instance.
     *
     * @var \Viserio\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * The invoker instance.
     *
     * @var \Viserio\Support\Invoker
     */
    protected $invoker;

    /**
     * The pipes to send commands through before dispatching.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * All of the command-to-handler mappings.
     *
     * @var array
     */
    protected $mappings = [];

    /**
     * The method to call on handler.
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * The fallback mapping Closure.
     *
     * @var \Closure
     */
    protected $mapper;

    /**
     * Create a new command dispatcher instance.
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $pipeline = new Pipeline();
        $pipeline->setContainer($container);

        $this->pipeline = $pipeline;
        $this->invoker = (new Invoker())
            ->injectByTypeHint(true)
            ->injectByParameterName(true)
            ->setContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function via(string $method): DispatcherContract
    {
        $this->method = $method;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHandler($command)
    {
        if (method_exists($command, $this->method)) {
            return $command;
        }

        return $this->container->get($this->getHandlerClass($command));
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlerClass($command): string
    {
        if (method_exists($command, $this->method)) {
            return get_class($command);
        }

        return $this->inflectSegment($command, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlerMethod($command): string
    {
        if (method_exists($command, $this->method)) {
            return $this->method;
        }

        return $this->inflectSegment($command, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function maps(array $commands)
    {
        $this->mappings = array_merge($this->mappings, $commands);
    }

    /**
     * {@inheritdoc}
     */
    public function mapUsing(Closure $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($command, Closure $afterResolving = null)
    {
        return $this->pipeline->send($command)->through($this->pipes)->then(function ($command) use ($afterResolving) {
            if (method_exists($command, $this->method)) {
                return $this->invoker->call([$command, $this->method]);
            }

            $handler = $this->resolveHandler($command);

            if ($afterResolving) {
                call_user_func($afterResolving, $handler);
            }

            return call_user_func([$handler, $this->getHandlerMethod($command)], $command);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function pipeThrough(array $pipes): DispatcherContract
    {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * Get the given handler segment for the given command.
     *
     * @param mixed $command
     * @param int   $segment
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function inflectSegment($command, int $segment): string
    {
        $className = get_class($command);

        // Get the given segment from a given class handler.
        if (isset($this->mappings[$className])) {
            return explode('@', $this->mappings[$className])[$segment];
        }

        // Get the given segment from a given class handler using the custom mapper.
        if ($this->mapper) {
            return explode('@', call_user_func($this->mapper, [$command]))[$segment];
        }

        throw new InvalidArgumentException(sprintf('No handler registered for command [%s].', $className));
    }
}