<?php
declare(strict_types=1);
namespace Viserio\Component\Parser\Tests\Formats\Format;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Viserio\Component\Parser\Dumper\IniDumper;
use Viserio\Component\Parser\Parser\IniParser;

class IniTest extends TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    /**
     * @var array
     */
    private $excepted;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->root     = vfsStream::setup();
        $this->excepted = [
            'first_section' => [
                'one'    => true,
                'two'    => false,
                'tree'   => null,
                'five'   => 5,
                'animal' => 'BIRD',
            ],
            'second_section' => [
                'path' => '/usr/local/bin',
                'URL'  => 'http://www.example.com/~username',
            ],
            'third_section' => [
                'phpversion' => [
                    5.0,
                    5.1,
                    5.2,
                    5.3,
                ],
                'urls' => [
                    'svn' => 'http://svn.php.net',
                    'git' => 'http://git.php.net',
                ],
            ],
        ];
    }

    public function testParse(): void
    {
        $file = vfsStream::newFile('temp.ini')
            ->withContent('
; This is a sample configuration file
; Comments start with \';\', as in php.ini

[first_section]
one = true
two = false
tree = null
five = 5
animal = BIRD

[second_section]
path = "/usr/local/bin"
URL = "http://www.example.com/~username"

[third_section]
phpversion[] = "5.0"
phpversion[] = "5.1"
phpversion[] = "5.2"
phpversion[] = "5.3"

urls[svn] = "http://svn.php.net"
urls[git] = "http://git.php.net"')
            ->at($this->root);

        $parsed = (new IniParser())->parse(\file_get_contents($file->url()));

        self::assertInternalType('array', $parsed);
        self::assertSame($this->excepted, $parsed);
    }

    public function testParseWithSection(): void
    {
        $file = vfsStream::newFile('temp.ini')->withContent(
            '
[main]

explore=true
[main.sub]

[main.sub.sub]
value=5'
        )->at($this->root);

        $parsed = (new IniParser())->parse(\file_get_contents($file->url()));

        self::assertInternalType('array', $parsed);
        self::assertSame(
            ['main' => ['explore' => true], 'main.sub' => [], 'main.sub.sub' => ['value' => 5]],
            $parsed
        );
    }

    /**
     * @expectedException \Viserio\Component\Contract\Parser\Exception\ParseException
     */
    public function testParseToThrowException(): void
    {
        (new IniParser())->parse('nonexistfile');
    }

    public function testDump(): void
    {
        $dump = (new IniDumper())->dump($this->excepted);
        $file = vfsStream::newFile('temp.ini')
            ->withContent('
[first_section]
one=true
two=false
tree=null
five="5"
animal="BIRD"

[second_section]
path="/usr/local/bin"
URL="http://www.example.com/~username"

[third_section]
phpversion[]="5"
phpversion[]="5.1"
phpversion[]="5.2"
phpversion[]="5.3"

urls[svn]="http://svn.php.net"
urls[git]="http://git.php.net"')
            ->at($this->root);

        self::assertEquals(\preg_replace('/^\s+|\n|\r|\s+$/m', '', \file_get_contents($file->url())), \preg_replace('/^\s+|\n|\r|\s+$/m', '', $dump));
    }
}
