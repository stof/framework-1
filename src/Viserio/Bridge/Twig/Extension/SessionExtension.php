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

namespace Viserio\Bridge\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Viserio\Contract\Session\Store as StoreContract;

class SessionExtension extends AbstractExtension
{
    /**
     * Viserio session instance.
     *
     * @var \Viserio\Contract\Session\Store
     */
    protected $session;

    /**
     * Create a new session extension.
     *
     * @param \Viserio\Contract\Session\Store $session
     */
    public function __construct(StoreContract $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Viserio_Bridge_Twig_Extension_Session';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('session', [$this->session, 'get']),
            new TwigFunction('csrf_token', [$this->session, 'getToken'], ['is_safe' => ['html']]),
            new TwigFunction('csrf_field', [$this, 'getCsrfField'], ['is_safe' => ['html']]),
            new TwigFunction('session_get', [$this->session, 'get']),
            new TwigFunction('session_has', [$this->session, 'has']),
        ];
    }

    /**
     * Return a hidden csrf filed.
     *
     * @return string
     */
    public function getCsrfField(): string
    {
        return '<input type="hidden" name="_token" value="' . $this->session->getToken() . '">';
    }
}
