<?php
declare(strict_types=1);
namespace Viserio\Foundation\Http;

use Interop\Http\Middleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Viserio\Config\Manager as ConfigManager;
use Viserio\Contracts\Events\Dispatcher as DispatcherContract;
use Viserio\Contracts\Events\Traits\EventsAwareTrait;
use Viserio\Contracts\Exception\Handler as HandlerContract;
use Viserio\Contracts\Foundation\Application as ApplicationContract;
use Viserio\Contracts\Foundation\Kernel as KernelContract;
use Viserio\Contracts\Foundation\Terminable as TerminableContract;
use Viserio\Contracts\Routing\Router as RouterContract;
use Viserio\Foundation\Bootstrap\DetectEnvironment;
use Viserio\Foundation\Bootstrap\HandleExceptions;
use Viserio\Foundation\Bootstrap\LoadConfiguration;
use Viserio\Foundation\Bootstrap\LoadRoutes;
use Viserio\Foundation\Bootstrap\LoadServiceProvider;
use Viserio\HttpFactory\ResponseFactory;
use Viserio\Middleware\Dispatcher as MiddlewareDispatcher;
use Viserio\Routing\Router;
use Viserio\StaticalProxy\StaticalProxy;

class Kernel implements TerminableContract, KernelContract
{
    use EventsAwareTrait;

    /**
     * The application implementation.
     *
     * @var \Viserio\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \Viserio\Contracts\Routing\Router
     */
    protected $router;

    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeWithMiddlewares = [];

    /**
     * The application's route without middleware.
     *
     * @var array
     */
    protected $routeWithoutMiddlewares = [];

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        LoadConfiguration::class,
        DetectEnvironment::class,
        HandleExceptions::class,
        LoadRoutes::class,
        LoadServiceProvider::class,
    ];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param \Viserio\Contracts\Foundation\Application $app
     * @param \Viserio\Contracts\Routing\Router         $router
     * @param \Viserio\Contracts\Events\Dispatcher      $events
     */
    public function __construct(
        ApplicationContract $app,
        RouterContract $router,
        DispatcherContract $events
    ) {
        $this->app = $app;
        $this->events = $events;

        foreach ($this->routeWithMiddlewares as $routeWithMiddleware) {
            $router->withMiddleware($this->app->make($routeWithMiddleware));
        }

        foreach ($this->routeWithoutMiddlewares as $routeWithoutMiddleware) {
            $router->withoutMiddleware($this->app->make($routeWithoutMiddleware));
        }

        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response = null): ResponseInterface
    {
        // Passes the request to the container
        $this->app->instance(ServerRequestInterface::class, $request);
        StaticalProxy::clearResolvedInstance('request');

        $this->events->trigger(self::REQUEST, [$request]);

        if ($response === null) {
            $response = (new ResponseFactory())->createResponse();
        }

        $this->app->instance(ResponseInterface::class, $response);

        StaticalProxy::clearResolvedInstance('response');

        $response = $this->handleRequest($request, $response);

        // stop PHP sending a Content-Type automatically
        ini_set('default_mimetype', '');

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->events->trigger(self::TERMINATE, [$request, $response]);

        $this->app->get(HandlerContract::class)->unregister();
    }

    /**
     * Bootstrap the application for HTTP requests.
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers);
        }
    }

    /**
     * Convert request into response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function handleRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->bootstrap();

        $router = $this->router;
        $config = $this->app->get(ConfigManager::class);

        $router->setCachePath($config->get('routing.path'));
        $router->refreshCache($config->get('app.env', 'production') !== 'production');

        try {
            $response = $router->dispatch($request, $response);

            $this->events->trigger(self::RESPONSE, [$request, $response]);
        } catch (Throwable $exception) {
            $this->events->trigger(self::EXCEPTION, [$request, $response]);

            $exceptionHandler = $this->app->get(HandlerContract::class);

            $exceptionHandler->report($exception = new FatalThrowableError($exception));

            $response = $exceptionHandler->render($request, $exception);
        }

        $middlewareDispatcher = new MiddlewareDispatcher($response);

        return $middlewareDispatcher->process($request);
    }
}
