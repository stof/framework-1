<?php
declare(strict_types=1);
namespace Viserio\Component\Parsers\Parser;

use RuntimeException;
use Viserio\Component\Contracts\Parsers\Exception\ParseException;
use Viserio\Component\Contracts\Parsers\Parser as ParserContract;
use Yosymfony\Toml\Exception\ParseException as TomlParseException;
use Yosymfony\Toml\Toml as YosymfonyToml;

class TomlParser implements ParserContract
{
    /**
     * Create a new Toml parser.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (! class_exists('Yosymfony\\Toml\\Toml')) {
            throw new RuntimeException('Unable to read toml, the Toml Parser is not installed.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $payload): array
    {
        try {
            return YosymfonyToml::parse($payload);
        } catch (TomlParseException $exception) {
            throw new ParseException([
                'message' => 'Unable to parse the TOML string.',
                'line'    => $exception->getParsedLine(),
            ]);
        }
    }
}