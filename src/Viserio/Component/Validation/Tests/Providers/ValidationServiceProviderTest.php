<?php
declare(strict_types=1);
namespace Viserio\Component\Validation\Tests\Providers;

use PHPUnit\Framework\TestCase;
use Viserio\Component\Container\Container;
use Viserio\Component\Contracts\Validation\Validator as ValidatorContract;
use Viserio\Component\Validation\Providers\ValidationServiceProvider;
use Viserio\Component\Validation\Validator;

class ValidationServiceProviderTest extends TestCase
{
    public function testProvider()
    {
        $container = new Container();
        $container->register(new ValidationServiceProvider());

        self::assertInstanceOf(Validator::class, $container->get(Validator::class));
        self::assertInstanceOf(Validator::class, $container->get(ValidatorContract::class));
        self::assertInstanceOf(Validator::class, $container->get('validator'));
    }
}