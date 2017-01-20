<?php
declare(strict_types=1);
namespace Viserio\Component\StaticalProxy\Tests\Fixture;

use StdClass;
use Viserio\Component\StaticalProxy\StaticalProxy;

class FacadeObjectStub extends StaticalProxy
{
    /**
     * {@inheritdoc}
     */
    public static function getInstanceIdentifier()
    {
        return new StdClass();
    }
}