<?php

namespace Trax\Framework\Service;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Trax\Framework\Service\Utils\ForwardRequest;
use Trax\Framework\Service\Config;

class MicroService implements MicroServiceInterface
{
    use ForwardRequest;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var bool
     */
    protected $remote = false;

    /**
     * @var string
     */
    protected $serviceKey;

    /**
     * @var \Trax\Framework\Database\Database|null
     */
    protected $database;

    /**
     * The service commands.
     *
     * @var array
     */
    protected $commands = [
        // \Trax\Service\Console\Command::class,
    ];

    /**
     * Instanciate service.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $serviceKey
     * @return bool
     */
    public function __construct(Application $app, string $serviceKey)
    {
        $this->app = $app;
        $this->serviceKey = $serviceKey;
        $this->database = Config::database($serviceKey);
    }

    /**
     * Check that all the service database is up and running.
     *
     * @return object|false
     */
    public function checkDatabase()
    {
        if (empty($this->database)) {
            return false;
        }

        return $this->database->globalStatus();
    }

    /**
     * Get the service host.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function host(): string
    {
        $host = Config::serviceHost($this->serviceKey);
        if (empty($host)) {
            throw new \Exception("Service host undefied ($this->serviceKey).");
        }
        return $host;
    }

    /**
     * Get the service endpoint.
     *
     * @param  string  $path
     * @return string
     */
    public function endpoint(string $path = ''): string
    {
        // Remove the client.
        if (Str::of($path)->startsWith('clients/')) {
            $chunks = explode('/', $path);
            array_shift($chunks);       // Remove 'clients"
            array_shift($chunks);       // Remove the client slug
            $path = implode('/', $chunks);
        }
        // Remove the front segment.
        if (Str::of($path)->startsWith('front/')) {
            $chunks = explode('/', $path);
            array_shift($chunks);       // Remove 'front"
            $path = implode('/', $chunks);
        }
        return $this->host() . "/trax/api/$this->serviceKey/$path";
    }

    /**
     * Check the service.
     *
     * @return object  (object) ['ready' => false, 'reason' => 'Missing endpoint']
     */
    public function check(): object
    {
        if (!$this->remote) {
            return (object)['ready' => true];
        }

        try {
            $endpoint = $this->endpoint('check');
        } catch (\Exception $e) {
            return (object)['ready' => false, 'reason' => 'missing endpoint'];
        }

        try {
            Http::get($endpoint)->throw();
        } catch (\Exception $e) {
            return (object)['ready' => false, 'reason' => 'connection error'];
        }

        return (object)['ready' => true];
    }

    /**
     * Call a function of the service from the gateway.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $controllerClass
     * @param  string  $controllerMethod
     * @return \Illuminate\Http\Response
     */
    public function callFromGateway(Request $request, string $controllerClass, string $controllerMethod)
    {
        if ($this->remote) {
            return $this->forwardRequest($this->endpoint(
                Str::of($request->path())->after('trax/api/gateway/')
            ), $request->method(), $request);
        } else {
            return app($controllerClass)->$controllerMethod(
                $request,
            );
        }
    }

    /**
     * Get the service commands.
     *
     * @return array
     */
    public function commands()
    {
        return $this->commands;
    }
}
