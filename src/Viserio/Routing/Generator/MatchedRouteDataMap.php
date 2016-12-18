<?php
declare(strict_types=1);
namespace Viserio\Routing\Generator;

use Viserio\Contracts\Routing\NodeContents as NodeContentsContract;
use Viserio\Contracts\Routing\Route as RouteContract;

class MatchedRouteDataMap implements NodeContentsContract
{
    /**
     * @var array
     */
    protected $httpMethodRouteMap = [];

    /**
     * Create a new matched route data map instance.
     *
     * @param array $httpMethodRouteMap
     */
    public function __construct(array $httpMethodRouteMap = [])
    {
        $this->httpMethodRouteMap = $httpMethodRouteMap;
    }

    /**
     * @return array
     */
    public function getHttpMethodRouteDataMap(): array
    {
        return $this->httpMethodRouteMap;
    }

    /**
     * Get all allowed http methods.
     *
     * @return array
     */
    public function allowedHttpMethods(): array
    {
        $allowedHttpMethods = [];

        foreach ($this->httpMethodRouteMap as $item) {
            foreach ($item[0] as $method) {
                $allowedHttpMethods[] = $method;
            }
        }

        return array_values($allowedHttpMethods);
    }

    /**
     * Adds the supplied route to the matched route data map.
     *
     * @param \Viserio\Contracts\Routing\Route $route
     * @param array                            $parameterIndexNameMap
     */
    public function addRoute(RouteContract $route, array $parameterIndexNameMap)
    {
        $this->httpMethodRouteMap[] = [$route->getMethods(), [$parameterIndexNameMap, $route->getIdentifier()]];
    }
}
