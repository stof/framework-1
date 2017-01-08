<?php
declare(strict_types=1);
namespace Viserio\Console\Events;

use Viserio\Contracts\Events\Event as EventContract;
use Viserio\Events\Traits\EventTrait;
use Viserio\Contracts\Console\Application as ApplicationContract;

class CommandStartingEvent implements EventContract
{
    use EventTrait;

    /**
     * Create a new command starting event.
     *
     * @param \Viserio\Contracts\Console\Application $application
     *
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationContract $application, array $params)
    {
        $this->name       = 'command.starting';
        $this->target     = $application;
        $this->parameters = $params;
    }
}
