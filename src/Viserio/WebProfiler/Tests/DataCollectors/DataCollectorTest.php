<?php
declare(strict_types=1);
namespace Viserio\WebProfiler\Tests\DataCollectors;

use Viserio\WebProfiler\Tests\Fixture\FixtureDataCollector;

class DataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $collector = new FixtureDataCollector();

        $this->assertSame('fixture-data-collector', $collector->getName());
    }

    public function testCreateTable()
    {
        $collector = new FixtureDataCollector();
        $defaultTable = file_get_contents(__DIR__ . '/../Fixture/View/default_table.html');

        $this->assertSame(
            $this->removeSymfonyVarDumper($defaultTable),
            $this->removeSymfonyVarDumper($collector->getTableDefault())
        );
    }

    public function testCreateTooltipGroupDefault()
    {
        $collector = new FixtureDataCollector();

        $this->assertSame(
            '<div class="webprofiler-menu-tooltip-group"><div class="webprofiler-menu-tooltip-group-piece"><b>test</b><span>test</span></div></div>',
            $collector->getTooltippGroupDefault()
        );
    }

    public function testCreateTooltipGroupArray()
    {
        $collector = new FixtureDataCollector();

        $this->assertSame(
            '<div class="webprofiler-menu-tooltip-group"><div class="webprofiler-menu-tooltip-group-piece"><b>test</b><span class="test">test</span><span class="test2">test2</span></div></div>',
            $collector->getTooltippGroupArray()
        );
    }

    private function removeSymfonyVarDumper(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/', '', $html);

        return trim(preg_replace('/id=sf-dump-(?:\d+) /', '', $html));
    }
}
