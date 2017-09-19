<?php
declare(strict_types=1);
namespace Viserio\Component\Contract\Mail;

interface Mailer
{
    /**
     * Set the global from address and name.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return void
     */
    public function alwaysFrom(string $address, string $name = null): void;

    /**
     * Set the global to address and name.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return void
     */
    public function alwaysTo(string $address, string $name = null): void;

    /**
     * Send a new message when only a raw text part.
     *
     * @param string $text
     * @param mixed  $callback
     *
     * @return int
     */
    public function raw(string $text, $callback): int;

    /**
     * Send a new message when only a plain part.
     *
     * @param string $view
     * @param array  $data
     * @param mixed  $callback
     *
     * @return int
     */
    public function plain(string $view, array $data, $callback): int;

    /**
     * Send a new message using a view.
     *
     * @param array|string         $view
     * @param array                $data
     * @param null|\Closure|string $callback
     *
     * @return int
     */
    public function send($view, array $data = [], $callback = null): int;

    /**
     * Get the array of failed recipients.
     *
     * @return array
     */
    public function failures(): array;
}