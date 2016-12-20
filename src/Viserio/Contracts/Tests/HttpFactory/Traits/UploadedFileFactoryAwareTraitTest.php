<?php
declare(strict_types=1);
namespace Viserio\Contracts\HttpFactory\Tests\Traits;

use Interop\Http\Factory\UploadedFactoryInterface;
use Narrowspark\TestingHelper\Traits\MockeryTrait;
use Viserio\Contracts\HttpFactory\Traits\UploadedFileFactoryAwareTrait;

class UploadedFileFactoryAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;
    use UploadedFileFactoryAwareTrait;

    public function testSetAndGetUploadedFactory()
    {
        $this->setUploadedFactory($this->mock(UploadedFactoryInterface::class));

        $this->assertInstanceOf(UploadedFactoryInterface::class, $this->getUploadedFactory());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Instance implementing \Interop\Http\Factory\UploadedFactoryInterface is not set up.
     */
    public function testGetUploadedFactoryThrowExceptionIfEventsDispatcherIsNotSet()
    {
        $this->getUploadedFactory();
    }
}
