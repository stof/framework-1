<?php
declare(strict_types=1);
namespace Viserio\Container\Tests\Fixture;

class ContainerLazyExtendFixture
{
    public static $initialized = false;

    public function init()
    {
        static::$initialized = true;
    }
}