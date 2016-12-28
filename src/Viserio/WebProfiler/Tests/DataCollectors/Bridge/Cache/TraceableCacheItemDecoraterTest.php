<?php
declare(strict_types=1);
namespace Viserio\WebProfiler\Tests\DataCollectors\Bridge\Recording;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Mockery as Mock;
use Narrowspark\TestingHelper\Traits\MockeryTrait;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Viserio\WebProfiler\DataCollectors\Bridge\Cache\TraceableCacheItemDecorater;

class TraceableCacheItemDecoraterTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function tearDown()
    {
        parent::tearDown();

        $this->allowMockingNonExistentMethods(true);

        // Verify Mockery expectations.
        Mock::close();
    }

    public function testGetItem()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        static::assertInstanceOf(CacheItemInterface::class, $adapter->getItem('test'));
        $object = $adapter->getCalls()[0];

        static::assertFalse($object->isHit);
        static::assertSame('getItem', $object->name);
        static::assertSame(['test'], $object->arguments);
    }

    public function testHasItem()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        static::assertFalse($adapter->hasItem('test'));
        $object = $adapter->getCalls()[0];

        static::assertFalse($object->result);
        static::assertSame('hasItem', $object->name);
        static::assertSame(['test'], $object->arguments);
    }

    public function testDeleteItem()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        $adapter->deleteItem('test');
        $object = $adapter->getCalls()[0];

        static::assertTrue($object->result);
        static::assertSame('deleteItem', $object->name);
        static::assertSame(['test'], $object->arguments);
    }

    public function testSave()
    {
        $adapter = $this->getTraceableCacheItemDecorater();
        $item    = $this->mock(CacheItemInterface::class);
        $item->shouldReceive('getKey')
            ->twice();

        static::assertTrue($adapter->save($item));

        $object = $adapter->getCalls()[0];

        static::assertTrue($object->result);
        static::assertSame('save', $object->name);
        static::assertTrue(is_array($object->arguments));
    }

    public function testSaveDeferred()
    {
        $adapter = $this->getTraceableCacheItemDecorater();
        $item    = $this->mock(CacheItemInterface::class);
        $item->shouldReceive('getKey')
            ->times(3);

        static::assertTrue($adapter->saveDeferred($item));

        $object = $adapter->getCalls()[0];

        static::assertTrue($object->result);
        static::assertSame('saveDeferred', $object->name);
        static::assertTrue(is_array($object->arguments));
    }

    public function testGetItems()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        static::assertInstanceOf(CacheItemInterface::class, $adapter->getItems(['item'])['item']);

        $object = $adapter->getCalls()[0];

        static::assertTrue(is_array($object->result));
        static::assertSame('getItems', $object->name);
        static::assertTrue(is_array($object->arguments));
    }

    public function testClear()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        static::assertTrue($adapter->clear());

        $object = $adapter->getCalls()[0];

        static::assertTrue($object->result);
        static::assertSame('clear', $object->name);
        static::assertTrue(is_array($object->arguments));
    }

    public function testDeleteItems()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        static::assertTrue($adapter->deleteItems(['test']));

        $object = $adapter->getCalls()[0];

        static::assertTrue($object->result);
        static::assertSame('deleteItems', $object->name);
        static::assertTrue(is_array($object->arguments));
    }

    public function testCommit()
    {
        $adapter = $this->getTraceableCacheItemDecorater();

        static::assertTrue($adapter->commit());

        $object = $adapter->getCalls()[0];

        static::assertTrue($object->result);
        static::assertSame('commit', $object->name);
        static::assertTrue(is_array($object->arguments));
    }

    private function getTraceableCacheItemDecorater()
    {
        return new TraceableCacheItemDecorater(new ArrayCachePool(), new Stopwatch());
    }
}
