<?php
declare(strict_types=1);
namespace Viserio\Exception\Tests\Providers;

use Viserio\Config\Providers\ConfigServiceProvider;
use Viserio\Container\Container;
use Viserio\Exception\Displayers\HtmlDisplayer;
use Viserio\Exception\Displayers\JsonDisplayer;
use Viserio\Exception\Displayers\ViewDisplayer;
use Viserio\Exception\Displayers\WhoopsDisplayer;
use Viserio\Exception\ExceptionIdentifier;
use Viserio\Exception\ExceptionInfo;
use Viserio\Exception\Filters\CanDisplayFilter;
use Viserio\Exception\Filters\VerboseFilter;
use Viserio\Exception\Handler;
use Viserio\Exception\Providers\ExceptionServiceProvider;
use Viserio\Exception\Transformers\CommandLineTransformer;
use Viserio\Filesystem\Providers\FilesServiceProvider;
use Viserio\HttpFactory\Providers\HttpFactoryServiceProvider;
use Viserio\View\Providers\ViewServiceProvider;

class ExceptionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvider()
    {
        $container = new Container();
        $container->register(new ExceptionServiceProvider());
        $container->register(new ConfigServiceProvider());
        $container->register(new ViewServiceProvider());
        $container->register(new FilesServiceProvider());
        $container->register(new HttpFactoryServiceProvider());

        self::assertInstanceOf(ExceptionIdentifier::class, $container->get(ExceptionIdentifier::class));
        self::assertInstanceOf(ExceptionInfo::class, $container->get(ExceptionInfo::class));
        self::assertInstanceOf(HtmlDisplayer::class, $container->get(HtmlDisplayer::class));
        self::assertInstanceOf(JsonDisplayer::class, $container->get(JsonDisplayer::class));
        self::assertInstanceOf(ViewDisplayer::class, $container->get(ViewDisplayer::class));
        self::assertInstanceOf(WhoopsDisplayer::class, $container->get(WhoopsDisplayer::class));
        self::assertInstanceOf(VerboseFilter::class, $container->get(VerboseFilter::class));
        self::assertInstanceOf(CanDisplayFilter::class, $container->get(CanDisplayFilter::class));
        self::assertInstanceOf(CommandLineTransformer::class, $container->get(CommandLineTransformer::class));
        self::assertInstanceOf(Handler::class, $container->get(Handler::class));
    }

    public function testProviderWithoutConfigManager()
    {
        $container = new Container();
        $container->register(new ExceptionServiceProvider());
        $container->register(new ViewServiceProvider());
        $container->register(new FilesServiceProvider());

        $container->instance('options', [
            'debug' => true,
        ]);

        self::assertInstanceOf(VerboseFilter::class, $container->get(VerboseFilter::class));
    }

    public function testProviderWithoutConfigManagerAndNamespace()
    {
        $container = new Container();
        $container->register(new ExceptionServiceProvider());
        $container->register(new ViewServiceProvider());
        $container->register(new FilesServiceProvider());

        $container->instance('viserio.exception.options', [
            'debug' => true,
        ]);

        self::assertInstanceOf(VerboseFilter::class, $container->get(VerboseFilter::class));
    }
}
