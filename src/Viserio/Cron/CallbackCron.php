<?php
declare(strict_types=1);
namespace Viserio\Cron;

use InvalidArgumentException;
use LogicException;
use Psr\Cache\CacheItemPoolInterface;
use Viserio\Contracts\Cron\Cron as CronContract;

class CallbackCron extends Cron
{
    /**
     * The callback to call.
     *
     * @var string|callable
     */
    protected $callback;

    /**
     * The parameters to pass to the method.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Create a new callback cron instance.
     *
     * @param string|callable                   $callback
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     * @param array                             $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(CacheItemPoolInterface $cache, $callback, array $parameters = [])
    {
        if (! is_string($callback) && ! is_callable($callback)) {
            throw new InvalidArgumentException(
                'Invalid scheduled callback cron job. Must be string or callable.'
            );
        }

        $this->callback = $callback;
        $this->cache = $cache;
        $this->parameters = $parameters;
    }

    /**
     * Run the given cron job.
     *
     * @return mixed
     */
    public function run()
    {
        if ($this->description) {
            $item = $this->cache->getItem($this->getMutexName());
            $item->set($this->getMutexName());
            $item->expiresAfter(1440);

            $this->cache->save($item);
        }

        try {
            $response = $this->getInvoker()->call($this->callback, $this->parameters);
        } finally {
            if ($this->description) {
                $this->cache->deleteItem($this->getMutexName());
            }
        }

        $this->callAfterCallbacks();

        return $response;
    }

    /**
     * Do not allow the cron job to overlap each other.
     *
     * @throws \LogicException
     *
     * @return $this
     */
    public function withoutOverlapping(): CronContract
    {
        if (! isset($this->description)) {
            throw new LogicException(
                'A scheduled cron job description is required to prevent overlapping. ' .
                "Use the 'description' method before 'withoutOverlapping'."
            );
        }

        return $this->skip(function () {
            return $this->cache->hasItem($this->getMutexName());
        });
    }

    /**
     * Get the summary of the event for display.
     *
     * @return string
     */
    public function getSummaryForDisplay(): string
    {
        if (is_string($this->description)) {
            return $this->description;
        }

        return is_string($this->callback) ? $this->callback : 'Closure';
    }

    /**
     * Get the mutex name for the scheduled command.
     *
     * @return string
     */
    protected function getMutexName(): string
    {
        return 'schedule-' . sha1($this->description);
    }
}