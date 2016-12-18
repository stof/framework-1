<?php
declare(strict_types=1);
namespace Viserio\Routing\Tests\Router;

use Viserio\Contracts\Routing\Pattern;
use Viserio\HttpFactory\ResponseFactory;
use Viserio\HttpFactory\ServerRequestFactory;
use Viserio\HttpFactory\StreamFactory;

class CommonRouteSegmentRouterTest extends RouteRouterBaseTest
{
    public function routerMatchingProvider(): array
    {
        return [
            ['GET', '/route1/a/b/c', 'route1 | p1 = a | p2 = b | p3 = c'],
            ['GET', '/route2/a/b/c', 'route2 | p1 = a | p2 = b | p3 = c'],
            ['GET', '/route3/a/b/c', 'route3 | p1 = a | p2 = b | p3 = c'],
            ['GET', '/route4/a/b/c', 'route4 | p1 = a | p2 = b | p3 = c'],
            ['GET', '/route5/a/b/c', 'route5 | p_1 = a | p_2 = b | p_3 = c'],
            ['GET', '/route6/a/b/c', 'route6 | p_1 = a | p2 = b | p_3 = c'],
        ];
    }

    /**
     * @dataProvider routerMatching404Provider
     * @expectedException \Narrowspark\HttpStatus\Exception\NotFoundException
     * @param mixed $httpMethod
     * @param mixed $uri
     */
    public function testRouter404($httpMethod, $uri)
    {
        $this->router->dispatch(
            (new ServerRequestFactory())->createServerRequest($_SERVER, $httpMethod, $uri),
            (new ResponseFactory())->createResponse()
        );
    }

    public function routerMatching404Provider()
    {
        return [
            ['GET', '/route6/a/1/c'],
            ['GET', '/route1/a/123/c'],
        ];
    }

    protected function definitions($router)
    {
        $router->pattern('p2', Pattern::ALPHA);

        $router->get('/route1/{p1}/{p2}/{p3}', function ($request, $args) {
            return (new ResponseFactory())
                ->createResponse()
                ->withBody(
                    (new StreamFactory())
                    ->createStream($args['name'] . ' | p1 = ' . $args['p1'] . ' | p2 = ' . $args['p2'] . ' | p3 = ' . $args['p3'])
                );
        })->setParameter('name', 'route1');
        $router->get('/route2/{p1}/{p2}/{p3}', function ($request, $args) {
            return (new ResponseFactory())
                ->createResponse()
                ->withBody(
                    (new StreamFactory())
                    ->createStream($args['name'] . ' | p1 = ' . $args['p1'] . ' | p2 = ' . $args['p2'] . ' | p3 = ' . $args['p3'])
                );
        })->setParameter('name', 'route2');
        $router->get('/route3/{p1}/{p2}/{p3}', function ($request, $args) {
            return (new ResponseFactory())
                ->createResponse()
                ->withBody(
                    (new StreamFactory())
                    ->createStream($args['name'] . ' | p1 = ' . $args['p1'] . ' | p2 = ' . $args['p2'] . ' | p3 = ' . $args['p3'])
                );
        })->setParameter('name', 'route3');
        $router->get('/route4/{p1}/{p2}/{p3}', function ($request, $args) {
            return (new ResponseFactory())
                ->createResponse()
                ->withBody(
                    (new StreamFactory())
                    ->createStream($args['name'] . ' | p1 = ' . $args['p1'] . ' | p2 = ' . $args['p2'] . ' | p3 = ' . $args['p3'])
                );
        })->setParameter('name', 'route4');
        $router->get('/route5/{p_1}/{p_2}/{p_3}', function ($request, $args) {
            return (new ResponseFactory())
                ->createResponse()
                ->withBody(
                    (new StreamFactory())
                    ->createStream($args['name'] . ' | p_1 = ' . $args['p_1'] . ' | p_2 = ' . $args['p_2'] . ' | p_3 = ' . $args['p_3'])
                );
        })->setParameter('name', 'route5');
        $router->get('/route6/{p_1}/{p2}/{p_3}', function ($request, $args) {
            return (new ResponseFactory())
                ->createResponse()
                ->withBody(
                    (new StreamFactory())
                    ->createStream($args['name'] . ' | p_1 = ' . $args['p_1'] . ' | p2 = ' . $args['p2'] . ' | p_3 = ' . $args['p_3'])
                );
        })->setParameter('name', 'route6');
    }
}
