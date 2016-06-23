<?php
namespace Viserio\Exception\Displayers;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use Viserio\Contracts\Exception\Displayer as DisplayerContract;
use Viserio\Http\Response;
use Whoops\Handler\{
    PrettyPageHandler,
    PlainTextHandler,
    JsonResponseHandler,
    XmlResponseHandler
};
use Whoops\Run as Whoops;

class WhoopsDisplayer implements DisplayerContract
{
    /**
     * {@inheritdoc}
     */
    public function display(Throwable $exception, string $id, int $code, array $headers): ResponseInterface
    {
        $content = $this->getWhoops()->handleException($exception);

        return new Response(
            $code,
            array_merge($headers, ['Content-Type' => $this->contentType()]),
            $content
        );
    }

    /**
     * {@inheritdoc}
     */
    public function contentType(): string
    {
        return 'text/html';
    }

    /**
     * {@inheritdoc}
     */
    public function canDisplay(Throwable $original, Throwable $transformed, int $code): bool
    {
        return class_exists(Whoops::class);
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose(): bool
    {
        return true;
    }

    /**
     * Returns the whoops instance.
     *
     * @return Whoops
     */
    private function getWhoops()
    {
        $whoops = new Whoops();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);

        $whoops->pushHandler(new PrettyPageHandler());

        return $whoops;
    }
}