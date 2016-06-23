<?php
namespace Viserio\Contracts\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface Log extends PsrLoggerInterface
{
    /**
     * Register a file log handler.
     *
     * @param string      $path
     * @param string      $level
     * @param object|null $processor
     * @param object|null $formatter
     *
     * @return void
     */
    public function useFiles(
        string $path,
        string $level = 'debug',
        $processor = null,
        $formatter = null
    );

    /**
     * Register a daily file log handler.
     *
     * @param string      $path
     * @param int         $days
     * @param string      $level
     * @param object|null $processor
     * @param object|null $formatter
     *
     * @return void
     */
    public function useDailyFiles(
        string $path,
        int $days = 0,
        string $level = 'debug',
        $processor = null,
        $formatter = null
    );
}