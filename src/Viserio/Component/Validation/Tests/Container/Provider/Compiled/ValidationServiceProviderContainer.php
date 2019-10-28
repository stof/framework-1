<?php

declare(strict_types=1);

/**
 * This file is part of Narrowspark Framework.
 *
 * (c) Daniel Bannert <d.bannert@anolilab.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Viserio\Component\Validation\Tests\Container\Provider\Compiled;

/**
 * This class has been auto-generated by Viserio Container Component.
 */
final class ValidationServiceProviderContainer extends \Viserio\Component\Container\AbstractCompiledContainer
{
    /**
     * Create a new Compiled Container instance.
     */
    public function __construct()
    {
        $this->services = $this->privates = [];
        $this->methodMapping = [
            \Viserio\Contract\Validation\Validator::class => 'get9c2345652e8ae3f87ba009d0f8fedee27bb751398014908e9ab2fb6d5bf1300f',
        ];
        $this->aliases = [
            \Viserio\Component\Validation\Validator::class => \Viserio\Contract\Validation\Validator::class,
            'validator' => \Viserio\Contract\Validation\Validator::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRemovedIds(): array
    {
        return [
            \Psr\Container\ContainerInterface::class => true,
            \Viserio\Contract\Container\Factory::class => true,
            \Viserio\Contract\Container\TaggedContainer::class => true,
            'container' => true,
        ];
    }

    /**
     * Returns the public Viserio\Contract\Validation\Validator shared service.
     *
     * @return \Viserio\Component\Validation\Validator
     */
    protected function get9c2345652e8ae3f87ba009d0f8fedee27bb751398014908e9ab2fb6d5bf1300f(): \Viserio\Component\Validation\Validator
    {
        return $this->services[\Viserio\Contract\Validation\Validator::class] = new \Viserio\Component\Validation\Validator();
    }
}