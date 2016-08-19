<?php
declare(strict_types=1);
namespace Viserio\Bus\Tests\Fixture;

use Viserio\Contracts\Queue\ShouldQueue as ShouldQueueContract;

class BusDispatcherCustomQueueCommand implements ShouldQueueContract
{
    public function queue($queue, $command)
    {
        $queue->push($command);
    }
}