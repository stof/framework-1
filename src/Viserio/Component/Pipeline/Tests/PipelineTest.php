<?php
declare(strict_types=1);
namespace Viserio\Component\Pipeline\Tests;

use Narrowspark\TestingHelper\ArrayContainer;
use PHPUnit\Framework\TestCase;
use Viserio\Component\Pipeline\Pipeline;
use Viserio\Component\Pipeline\Tests\Fixture\PipelineInvokePipe;
use Viserio\Component\Pipeline\Tests\Fixture\PipelineTestParameterPipe;
use Viserio\Component\Pipeline\Tests\Fixture\PipelineTestPipeOne;

class PipelineTest extends TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new ArrayContainer([
            'PipelineTestPipeOne'       => new PipelineTestPipeOne(),
            'PipelineTestParameterPipe' => new PipelineTestParameterPipe(),
        ]);
    }

    public function testPipelineBasicUsage()
    {
        $pipeTwo = function ($piped, $next) {
            $_SERVER['__test.pipe.two'] = $piped;

            return $next($piped);
        };

        $result = (new Pipeline())
            ->setContainer($this->container)
            ->send('foo')
            ->through(['PipelineTestPipeOne', $pipeTwo])
            ->then(function ($piped) {
                return $piped;
            });

        self::assertEquals('foo', $result);
        self::assertEquals('foo', $_SERVER['__test.pipe.one']);
        self::assertEquals('foo', $_SERVER['__test.pipe.two']);

        unset($_SERVER['__test.pipe.one'], $_SERVER['__test.pipe.two']);
    }

    public function testPipelineUsageWithParameters()
    {
        $parameters = ['one', 'two'];

        $result = (new Pipeline())
            ->setContainer($this->container)
            ->send('foo')
            ->through('PipelineTestParameterPipe:' . implode(',', $parameters))
            ->then(function ($piped) {
                return $piped;
            });

        self::assertEquals('foo', $result);
        self::assertEquals($parameters, $_SERVER['__test.pipe.parameters']);

        unset($_SERVER['__test.pipe.parameters']);
    }

    public function testPipelineViaChangesTheMethodBeingCalledOnThePipes()
    {
        $result = (new Pipeline())
            ->setContainer($this->container)
            ->send('data')
            ->through('PipelineTestPipeOne')
            ->via('differentMethod')
            ->then(function ($piped) {
                return $piped;
            });

        self::assertEquals('data', $result);
    }

    public function testPipelineViaObject()
    {
        $result = (new Pipeline())
            ->send('foo')
            ->through([new PipelineTestPipeOne()])
            ->then(function ($piped) {
                return $piped;
            });

        self::assertEquals('foo', $result);
        self::assertEquals('foo', $_SERVER['__test.pipe.one']);

        unset($_SERVER['__test.pipe.one']);
    }

    public function testPipelineInvoke()
    {
        $parameters = ['one', 'two'];

        $result = (new Pipeline())
            ->send('foo')
            ->through([[PipelineInvokePipe::class, $parameters]])
            ->then(function ($piped) {
                return $piped;
            });

        self::assertEquals('foo', $result);
        self::assertEquals($parameters, $_SERVER['__test.pipe.parameters']);

        unset($_SERVER['__test.pipe.one']);
    }
}