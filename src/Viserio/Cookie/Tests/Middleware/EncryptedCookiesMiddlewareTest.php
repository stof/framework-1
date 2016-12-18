<?php
declare(strict_types=1);
namespace Viserio\Cookie\Tests\Middleware;

use Defuse\Crypto\Key;
use Mockery as Mock;
use Narrowspark\TestingHelper\Middleware\CallableMiddleware;
use Narrowspark\TestingHelper\Middleware\Dispatcher;
use Narrowspark\TestingHelper\Traits\MockeryTrait;
use Viserio\Cookie\Cookie;
use Viserio\Cookie\Middleware\EncryptedCookiesMiddleware;
use Viserio\Cookie\RequestCookies;
use Viserio\Cookie\ResponseCookies;
use Viserio\Cookie\SetCookie;
use Viserio\Encryption\Encrypter;
use Viserio\HttpFactory\ResponseFactory;
use Viserio\HttpFactory\ServerRequestFactory;

class EncryptedCookiesMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function tearDown()
    {
        parent::tearDown();

        $this->allowMockingNonExistentMethods(true);

        // Verify Mockery expectations.
        Mock::close();
    }

    public function testEncryptedCookieRequest()
    {
        $encrypter = new Encrypter(Key::createNewRandomKey());
        $request   = (new ServerRequestFactory())->createServerRequest($_SERVER);

        $dispatcher = new Dispatcher([
            new CallableMiddleware(function ($request, $delegate) use ($encrypter) {
                $cookies = RequestCookies::fromRequest($request);
                $cookies = $cookies->add(new Cookie('encrypted', $encrypter->encrypt('test')));

                return $delegate->process($cookies->renderIntoCookieHeader($request));
            }),
            new EncryptedCookiesMiddleware($encrypter),
            new CallableMiddleware(function ($request, $delegate) {
                $cookies = RequestCookies::fromRequest($request);

                self::assertSame('encrypted', $cookies->get('encrypted')->getName());
                self::assertSame('test', $cookies->get('encrypted')->getValue());

                return (new ResponseFactory())->createResponse(200);
            }),
        ]);

        $dispatcher->dispatch($request);
    }

    public function testEncryptedCookieResponse()
    {
        $encrypter = new Encrypter(Key::createNewRandomKey());
        $request   = (new ServerRequestFactory())->createServerRequest($_SERVER);

        $dispatcher = new Dispatcher([
            new EncryptedCookiesMiddleware($encrypter),
            new CallableMiddleware(function ($request, $delegate) {
                $response = (new ResponseFactory())->createResponse(200);

                $cookies = ResponseCookies::fromResponse($response);
                $cookies = $cookies->add(new SetCookie('encrypted', 'test'));

                return $cookies->renderIntoSetCookieHeader($response);
            }),
        ]);

        $response = $dispatcher->dispatch($request);
        $cookies  = ResponseCookies::fromResponse($response);

        self::assertSame('encrypted', $cookies->get('encrypted')->getName());
        self::assertSame('test', $encrypter->decrypt($cookies->get('encrypted')->getValue()));
    }
}
