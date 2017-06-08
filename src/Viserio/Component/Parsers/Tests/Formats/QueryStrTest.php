<?php
declare(strict_types=1);
namespace Viserio\Component\Parsers\Tests\Formats;

use PHPUnit\Framework\TestCase;
use Viserio\Component\Parsers\Dumpers\QueryStrDumper;
use Viserio\Component\Parsers\Parsers\QueryStrParser;

class QueryStrTest extends TestCase
{
    public function testParse()
    {
        $parsed = (new QueryStrParser())->parse('status=123&message=hello world');

        self::assertTrue(is_array($parsed));
        self::assertSame(['status' => '123', 'message' => 'hello world'], $parsed);
    }

    public function testDump()
    {
        $expected = ['status' => 123, 'message' => 'hello world'];
        $payload  = http_build_query($expected);
        $dump     = (new QueryStrDumper())->dump($expected);

        self::assertEquals($payload, $dump);
    }
}
