<?php
declare(strict_types=1);
namespace Viserio\Component\Contract\Cookie\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidArgumentException extends BaseInvalidArgumentException implements Exception
{
}