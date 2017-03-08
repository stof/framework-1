<?php
declare(strict_types=1);
namespace Viserio\Component\OptionsResolver\Tests\Fixtures;

use Viserio\Component\Contracts\Container\Traits\ContainerAwareTrait;
use Viserio\Component\Contracts\OptionsResolver\RequiresComponentConfig as RequiresComponentConfigContract;
use Viserio\Component\OptionsResolver\Traits\ConfigurationTrait;

class ConfigurationTraitAndContainerAwareConfiguration implements RequiresComponentConfigContract
{
    use ContainerAwareTrait;
    use ConfigurationTrait;

    public function getOptions($data)
    {
        $this->configureOptions($data);

        return $this->options;
    }

    /**
     * @interitdoc
     */
    public function getDimensions(): iterable
    {
        return ['doctrine', 'connection'];
    }
}