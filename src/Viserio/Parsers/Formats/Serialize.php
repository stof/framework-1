<?php
namespace Viserio\Parsers\Formats;

use Exception;
use Viserio\Contracts\Filesystem\Filesystem as FilesystemContract;
use Viserio\Contracts\Parser\Exception\DumpException;
use Viserio\Contracts\Parser\Exception\ParseException;
use Viserio\Contracts\Parsers\Format as FormatContract;

class Serialize implements FormatContract
{
    /**
     * {@inheritdoc}
     */
    public function parse($payload)
    {
        try {
            return unserialize(trim($payload));
        } catch (Exception $exception) {
            throw new ParseException('Failed to parse serialized Data');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dump(array $data)
    {
        return serialize($data);
    }
}
