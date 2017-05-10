<?php
declare(strict_types=1);
namespace Viserio\Component\Foundation\Tests\Bootstrap;

use Narrowspark\TestingHelper\Phpunit\MockeryTestCase;
use Viserio\Component\Contracts\Foundation\Kernel as KernelContract;
use Viserio\Component\Foundation\Bootstrap\LoadEnvironmentVariables;

class LoadEnvironmentVariablesTest extends MockeryTestCase
{
    public function testDontLoadIfCached()
    {
        $bootstraper = new LoadEnvironmentVariables();

        $kernel = $this->mock(KernelContract::class);
        $kernel->shouldReceive('getStoragePath')
            ->once()
            ->with('config.cache')
            ->andReturn(__DIR__ . '/../Fixtures/Config/app.php');
        $kernel->shouldReceive('getEnvironmentFile')
            ->never();
        $kernel->shouldReceive('getEnvironmentPath')
            ->never();

        $bootstraper->bootstrap($kernel);
    }

    public function testBootstrap()
    {
        $bootstraper = new LoadEnvironmentVariables();

        $kernel = $this->mock(KernelContract::class);
        $kernel->shouldReceive('getStoragePath')
            ->once()
            ->with('config.cache')
            ->andReturn('');
        $kernel->shouldReceive('getEnvironmentFile')
            ->once()
            ->andReturn('.env');
        $kernel->shouldReceive('getEnvironmentPath')
            ->once()
            ->andReturn('');

        $bootstraper->bootstrap($kernel);
    }

    public function testBootstrapWithAppEnv()
    {
        putenv('APP_ENV=production');

        $bootstraper = new LoadEnvironmentVariables();

        $kernel = $this->mock(KernelContract::class);
        $kernel->shouldReceive('getEnvironmentPath')
            ->twice()
            ->andReturn(__DIR__ . '/../Fixtures/');
        $kernel->shouldReceive('getEnvironmentFile')
            ->twice()
            ->andReturn('.env');
        $kernel->shouldReceive('loadEnvironmentFrom')
            ->once()
            ->with('.env.production');
        $kernel->shouldReceive('getStoragePath')
            ->once()
            ->with('config.cache')
            ->andReturn('');

        $bootstraper->bootstrap($kernel);

        // remove APP_ENV
        putenv('APP_ENV=');
        putenv('APP_ENV');
    }
}