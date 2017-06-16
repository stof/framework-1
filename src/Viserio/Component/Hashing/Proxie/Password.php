<?php
declare(strict_types=1);
namespace Viserio\Component\Hashing\Proxie;

use Viserio\Component\StaticalProxy\StaticalProxy;

class Password extends StaticalProxy
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public static function getInstanceIdentifier()
    {
        return 'password';
    }
}