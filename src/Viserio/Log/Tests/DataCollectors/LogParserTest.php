<?php
declare(strict_types=1);
namespace Viserio\Log\Tests\DataCollectors;

use PHPUnit\Framework\TestCase;
use Viserio\Log\DataCollectors\LogParser;

class LogParserTest extends TestCase
{
    public function testParse()
    {
        $parse = new LogParser();

        static::assertEquals(
            $this->removeId([
                [
                    'error',
                    '[2017-01-03 18:58:03] develop.ERROR: Viserio\Contracts\Container\Exceptions\NotFoundException: Abstract (Viserio\Translation\DataCollectors\ViserioTranslationDataCollector) is not being managed by the container in \src\Viserio\Container\Container.php:378 Stack trace: #0 \src\Viserio\WebProfiler\Providers\WebProfilerServiceProvider.php(131): Viserio\Container\Container->get(\'Viserio\\\\Transla...\') #1 \src\Viserio\WebProfiler\Providers\WebProfilerServiceProvider.php(68): Viserio\WebProfiler\Providers\WebProfilerServiceProvider::registerCollectorsFromConfig(Object(Viserio\Foundation\Application), Object(Viserio\WebProfiler\WebProfiler)) #2 \src\Viserio\Container\Container.php(433): Viserio\WebProfiler\Providers\WebProfilerServiceProvider::createWebProfiler(Object(Viserio\Foundation\Application), NULL) #3 [internal function]: Viserio\Container\Container::Viserio\Container\{closure}(Object(Viserio\Foundation\Application)) #4 \src\Viserio\Container\ContainerResolver.php(131): ReflectionFunction->invokeArgs(Array) #5 \src\Viserio\Container\ContainerResolver.php(37): Viserio\Container\ContainerResolver->resolveFunction(Object(Closure), Array) #6 \src\Viserio\Container\Container.php(621): Viserio\Container\ContainerResolver->resolve(Object(Closure), Array) #7 \src\Viserio\Container\Container.php(260): Viserio\Container\Container->resolveSingleton(\'Viserio\\\\Contrac...\', Array) #8 \src\Viserio\Container\Container.php(232): Viserio\Container\Container->resolveBound(\'Viserio\\\\Contrac...\', Array) #9 \src\Viserio\Container\Container.php(373): Viserio\Container\Container->resolve(\'Viserio\\\\Contrac...\') #10 \src\Viserio\Foundation\Http\Kernel.php(216): Viserio\Container\Container->get(\'Viserio\\\\Contrac...\') #11 \src\Viserio\Foundation\Http\Kernel.php(174): Viserio\Foundation\Http\Kernel->handleRequest(Object(Viserio\Http\ServerRequest)) #12 D:\Anolilab\Github\Php\narrowspark\public\index.php(36): Viserio\Foundation\Http\Kernel->handle(Object(Viserio\Http\ServerRequest)) #13 {main} {"identification":{}} []',
                ],
            ]),
            $this->removeId($parse->parse(__DIR__ . '/../Fixture/test.log'))
        );
    }

    private function removeId(array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$key][1] = trim(preg_replace('/"id":"(.*?)"/', '', $value[1]));
        }

        return $array;
    }
}