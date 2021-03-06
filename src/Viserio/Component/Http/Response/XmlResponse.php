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

namespace Viserio\Component\Http\Response;

use Viserio\Component\Http\Response;
use Viserio\Component\Http\Response\Traits\CreateBodyTrait;
use Viserio\Component\Http\Response\Traits\InjectContentTypeTrait;

class XmlResponse extends Response
{
    use InjectContentTypeTrait;
    use CreateBodyTrait;

    /**
     * Create an XML response.
     *
     * Produces an XML response with a Content-Type of text/xml and a default status of 200.
     *
     * @param \Psr\Http\Message\StreamInterface|string $xml     plain text or stream for the message body
     * @param null|string                              $charset content charset; default is utf-8
     * @param int                                      $status  integer status code for the response; 200 by default
     * @param array<int|string, mixed>                 $headers array of headers to use at initialization
     * @param string                                   $version protocol version
     *
     * @throws \Viserio\Contract\Http\Exception\InvalidArgumentException if $xml is neither a string or stream
     */
    public function __construct(
        $xml,
        ?string $charset = null,
        int $status = self::STATUS_OK,
        array $headers = [],
        string $version = '1.1'
    ) {
        parent::__construct(
            $status,
            $this->injectContentType('text/xml; charset=' . ($charset ?? 'utf-8'), $headers),
            $this->createBody($xml),
            $version
        );
    }
}
