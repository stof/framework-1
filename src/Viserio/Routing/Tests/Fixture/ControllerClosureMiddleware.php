<?php
declare(strict_types=1);
namespace Viserio\Routing\Tests\Fixture;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Viserio\Contracts\Middleware\Delegate as DelegateContract;
use Viserio\Contracts\Middleware\ServerMiddleware as ServerMiddlewareContract;
use Viserio\Http\StreamFactory;

class ControllerClosureMiddleware implements ServerMiddlewareContract
{
    public function process(
        ServerRequestInterface $request,
        DelegateContract $frame
    ): ResponseInterface {
        $response = $frame->next($request);

        $response = $response->withBody((new StreamFactory())->createStreamFromString(
            $response->getBody() . '-' . $request->getAttribute('foo-middleware') . '-controller-closure'
        ));

        return $response;
    }
}
