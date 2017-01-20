<?php
declare(strict_types=1);
namespace Viserio\Component\Bus\Tests;

use Mockery as Mock;
use Narrowspark\TestingHelper\ArrayContainer;
use Narrowspark\TestingHelper\Traits\MockeryTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Viserio\Component\Bus\QueueingDispatcher;
use Viserio\Component\Bus\Tests\Fixture\BusDispatcherBasicCommand;
use Viserio\Component\Bus\Tests\Fixture\BusDispatcherCustomQueueCommand;
use Viserio\Component\Bus\Tests\Fixture\BusDispatcherQueuedHandler;
use Viserio\Component\Bus\Tests\Fixture\BusDispatcherSpecificDelayCommand;
use Viserio\Component\Bus\Tests\Fixture\BusDispatcherSpecificQueueAndDelayCommand;
use Viserio\Component\Bus\Tests\Fixture\BusDispatcherSpecificQueueCommand;
use Viserio\Component\Contracts\Queue\QueueConnector as QueueConnectorContract;
use Viserio\Component\Contracts\Queue\ShouldQueue as ShouldQueueContract;

class QueueingDispatcherTest extends TestCase
{
    use MockeryTrait;

    public function testDispatchNowShouldNeverQueue()
    {
        $container = new ArrayContainer();

        $handler = $this->mock(stdClass::class);
        $handler->shouldReceive('handle')
            ->once()
            ->andReturn('foo');

        $container->set('Handler', $handler);

        $dispatcher = new QueueingDispatcher($container);
        $dispatcher->mapUsing(function () {
            return 'Handler@handle';
        });

        self::assertEquals(
            'foo',
            $dispatcher->dispatch($this->mock(ShouldQueueContract::class))
        );
    }

    public function testHandlersThatShouldQueueIsQueued()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(QueueConnectorContract::class);
            $mock->shouldReceive('push')
                ->once();

            return $mock;
        });

        $dispatcher->mapUsing(function () {
            return BusDispatcherQueuedHandler::class . '@handle';
        });

        $dispatcher->dispatch(new BusDispatcherBasicCommand());
    }

    public function testCommandsThatShouldQueueIsQueuedUsingCustomQueueAndDelay()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(QueueConnectorContract::class);
            $mock->shouldReceive('laterOn')
                ->once()
                ->with('foo', 10, Mock::type(BusDispatcherSpecificQueueAndDelayCommand::class));

            return $mock;
        });

        $dispatcher->dispatch(new BusDispatcherSpecificQueueAndDelayCommand());
    }

    public function testCommandsThatShouldQueueIsQueuedUsingCustomQueue()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(QueueConnectorContract::class);
            $mock->shouldReceive('pushOn')
                ->once()
                ->with('foo', Mock::type(BusDispatcherSpecificQueueCommand::class));

            return $mock;
        });

        $dispatcher->dispatch(new BusDispatcherSpecificQueueCommand());
    }

    public function testCommandsThatShouldQueueIsQueuedUsingCustomDelay()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(QueueConnectorContract::class);
            $mock->shouldReceive('later')
                ->once()
                ->with(10, Mock::type(BusDispatcherSpecificDelayCommand::class));

            return $mock;
        });

        $dispatcher->dispatch(new BusDispatcherSpecificDelayCommand());
    }

    public function testCommandsThatShouldQueueIsQueued()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(QueueConnectorContract::class);
            $mock->shouldReceive('push')->once();

            return $mock;
        });

        $dispatcher->dispatch($this->mock(ShouldQueueContract::class));
    }

    public function testDispatchShouldCallAfterResolvingIfCommandNotQueued()
    {
        $container = new ArrayContainer();

        $handler = $this->mock(stdClass::class)->shouldIgnoreMissing();
        $handler->shouldReceive('after')->once();

        $container->set('Handler', $handler);

        $dispatcher = new QueueingDispatcher($container);
        $dispatcher->mapUsing(function () {
            return 'Handler@handle';
        });

        $dispatcher->dispatch(new BusDispatcherBasicCommand(), function ($handler) {
            $handler->after();
        });
    }

    public function testCommandsThatShouldQueueIsQueuedUsingCustomHandler()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(QueueConnectorContract::class);
            $mock->shouldReceive('push')->once();

            return $mock;
        });

        $dispatcher->dispatch(new BusDispatcherCustomQueueCommand());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Queue resolver did not return a Queue implementation.
     */
    public function testCommandsThatShouldThrowException()
    {
        $container = new ArrayContainer();

        $dispatcher = new QueueingDispatcher($container, function () {
            $mock = $this->mock(stdClass::class);

            return $mock;
        });

        $dispatcher->dispatch(new BusDispatcherCustomQueueCommand());
    }
}