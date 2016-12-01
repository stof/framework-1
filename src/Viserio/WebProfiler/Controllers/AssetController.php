<?php
declare(strict_types=1);
namespace Viserio\WebProfiler\Controllers;

use Viserio\Routing\AbstractController;
use Interop\Http\Factory\ResponseFactoryInterface;
use Interop\Http\Factory\ServerRequestFactoryInterface;
use Interop\Http\Factory\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Viserio\WebProfiler\WebProfiler;

class AssetController extends AbstractController
{
    /**
     * Response factory instance.
     *
     * @var \Interop\Http\Factory\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * Stream factory instance.
     *
     * @var \Interop\Http\Factory\StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * WebProfiler instance.
     *
     * @var \Viserio\WebProfiler\WebProfiler
     */
    protected $webprofiler;

    /**
     * [__construct description]
     *
     * @param \Interop\Http\Factory\ServerRequestFactoryInterface $request
     * @param \Interop\Http\Factory\ResponseFactoryInterface      $responseFactory
     * @param \Interop\Http\Factory\StreamFactoryInterface        $streamFactory
     * @param \Viserio\WebProfiler\WebProfiler                    $webprofiler
     */
    public function __construct(
        ServerRequestFactoryInterface $request,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        WebProfiler $webprofiler
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->webprofiler = $webprofiler;

        if ($session = $request->getAttribute('session')) {
            $session->reflash();
        }
    }

    /**
     * Return the javascript for the Debugbar
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function js(): ResponseInterface
    {
        $renderer = $this->webprofiler->getJavascriptRenderer();

        $stream = $this->streamFactory->createStream(
            $renderer->dumpAssetsToString('js')
        );

        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('content-type', 'text/js');

        return $response->withBody($stream);
    }

    /**
     * Return the stylesheets for the Debugbar
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function css(): ResponseInterface
    {
        $renderer = $this->webprofiler->getJavascriptRenderer();

        $stream = $this->streamFactory->createStream(
            $renderer->dumpAssetsToString('css')
        );

        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('content-type', 'text/css');

        return $response->withBody($stream);
    }
}
