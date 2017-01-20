<?php
declare(strict_types=1);
namespace Viserio\Component\Session\Fingerprint;

use Psr\Http\Message\ServerRequestInterface;
use Viserio\Component\Contracts\Session\Fingerprint as FingerprintContract;
use Viserio\Component\Support\Http\ClientIp;

class ClientIpGenerator implements FingerprintContract
{
    /**
     * Client ip + secret key string.
     *
     * @var string
     */
    private $clientIp;

    /**
     * Create a new ClientIpGenerator instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $serverRequest
     */
    public function __construct(ServerRequestInterface $serverRequest)
    {
        $ip = (new ClientIp($serverRequest))->getIpAddress();

        $this->clientIp = random_bytes(32) . $ip;
    }

    /**
     * {@inhertiddoc}.
     */
    public function generate(): string
    {
        return hash('ripemd160', $this->clientIp);
    }
}