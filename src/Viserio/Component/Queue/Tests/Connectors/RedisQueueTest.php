<?php
declare(strict_types=1);
namespace Viserio\Component\Queue\Tests\Connectors;

use Cake\Chronos\Chronos;
use Narrowspark\TestingHelper\Traits\MockeryTrait;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Viserio\Component\Contracts\Encryption\Encrypter as EncrypterContract;
use Viserio\Component\Queue\Connectors\RedisQueue;

class RedisQueueTest extends TestCase
{
    use MockeryTrait;

    public function testDelayedPushWithDateTimeProperlyPushesJobOntoRedis()
    {
        $date      = Chronos::now();
        $encrypter = $this->mock(EncrypterContract::class);
        $encrypter->shouldReceive('encrypt');

        $redis = $this->mock(Client::class);
        $redis->shouldReceive('zadd')
            ->once()
            ->with(
                'queues:default:delayed',
                2,
                json_encode(['job' => 'foo', 'data' => ['data'], 'id' => 'foo', 'attempts' => '1'])
            );

        $queue = $this->getMockBuilder(RedisQueue::class)
            ->setMethods(['getSeconds', 'getTime', 'getRandomId'])
            ->setConstructorArgs([$redis])->getMock();
        $queue->setEncrypter($encrypter);

        $queue->expects($this->once())
            ->method('getRandomId')
            ->will($this->returnValue('foo'));
        $queue->expects($this->once())
            ->method('getSeconds')
            ->with($date)
            ->will($this->returnValue(1));
        $queue->expects($this->once())
            ->method('getTime')
            ->will($this->returnValue(1));

        $queue->later($date, 'foo', ['data']);
    }

    public function testPushProperlyPushesJobOntoRedis()
    {
        $encrypter = $this->mock(EncrypterContract::class);
        $encrypter->shouldReceive('encrypt');

        $queue = $this->getMockBuilder(RedisQueue::class)
            ->setMethods(['getSeconds', 'getTime', 'getRandomId'])
            ->setConstructorArgs([$redis = $this->mock(Client::class)])->getMock();
        $queue->setEncrypter($encrypter);

        $queue->expects($this->once())
            ->method('getRandomId')
            ->will($this->returnValue('foo'));
        $redis->shouldReceive('rpush')
            ->once()
            ->with(
                'queues:default',
                json_encode(['job' => 'foo', 'data' => ['data'], 'id' => 'foo', 'attempts' => '1'])
            );

        $id = $queue->push('foo', ['data']);

        self::assertEquals('foo', $id);
    }

    public function testDelayedPushProperlyPushesJobOntoRedis()
    {
        $encrypter = $this->mock(EncrypterContract::class);
        $encrypter->shouldReceive('encrypt');

        $redis = $this->mock(Client::class);
        $redis->shouldReceive('zadd')
            ->once()
            ->with(
                'queues:default:delayed',
                2,
                json_encode(['job' => 'foo', 'data' => ['data'], 'id' => 'foo', 'attempts' => '1'])
            );

        $queue = $this->getMockBuilder(RedisQueue::class)
            ->setMethods(['getSeconds', 'getTime', 'getRandomId'])
            ->setConstructorArgs([$redis])
            ->getMock();
        $queue->setEncrypter($encrypter);

        $queue->expects($this->once())
            ->method('getRandomId')
            ->will($this->returnValue('foo'));
        $queue->expects($this->once())
            ->method('getSeconds')
            ->with(1)
            ->will($this->returnValue(1));
        $queue->expects($this->once())
            ->method('getTime')
            ->will($this->returnValue(1));

        $id = $queue->later(1, 'foo', ['data']);

        self::assertEquals('foo', $id);
    }
}