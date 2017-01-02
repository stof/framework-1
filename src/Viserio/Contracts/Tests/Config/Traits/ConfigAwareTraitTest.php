<?php
declare(strict_types=1);
namespace Viserio\Contracts\Config\Tests\Traits;

use Narrowspark\TestingHelper\Traits\MockeryTrait;
use PHPUnit\Framework\TestCase;
use Viserio\Contracts\Config\Repository as RepositoryContract;
use Viserio\Contracts\Config\Traits\ConfigAwareTrait;

class ConfigAwareTraitTest extends TestCase
{
    use MockeryTrait;
    use ConfigAwareTrait;

    public function testGetAndSetConfig()
    {
        $this->setConfig($this->mock(RepositoryContract::class));

        self::assertInstanceOf(RepositoryContract::class, $this->getConfig());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Config is not set up.
     */
    public function testGetConfigThrowExceptionIfConfigIsNotSet()
    {
        $this->getConfig();
    }
}
