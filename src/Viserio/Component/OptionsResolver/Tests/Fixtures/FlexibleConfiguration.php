<?php
declare(strict_types=1);
namespace Viserio\Component\OptionsResolver\Tests\Fixtures;

use Viserio\Component\Contracts\OptionsResolver\RequiresComponentConfig as RequiresComponentConfigContract;

class FlexibleConfiguration implements RequiresComponentConfigContract
{
    public function getDimensions(): iterable
    {
        return ['one', 'two', 'three', 'four'];
    }
}