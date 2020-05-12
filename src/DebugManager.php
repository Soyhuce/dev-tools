<?php

namespace Soyhuce\DevTools;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Log;
use Soyhuce\DevTools\Collectors\DataCollector;
use Soyhuce\DevTools\Collectors\MemoryCollector;
use Soyhuce\DevTools\Collectors\MessageCollector;
use Soyhuce\DevTools\Collectors\ModelCollector;
use Soyhuce\DevTools\Collectors\QueryCollector;
use Soyhuce\DevTools\Collectors\RequestCollector;
use Soyhuce\DevTools\Collectors\ResponseCollector;
use Soyhuce\DevTools\Collectors\TimeCollector;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DebugManager
 */
class DebugManager
{
    private static $availableCollectors = [
        MessageCollector::class,
        RequestCollector::class,
        MemoryCollector::class,
        ModelCollector::class,
        TimeCollector::class,
        QueryCollector::class,
        ResponseCollector::class,
    ];

    /** @var Application */
    private $app;

    /** @var array */
    private $collectors;

    /** @var bool */
    private $booted;

    /**
     * DebugManager constructor.
     *
     * @param Container $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->booted = false;
    }

    public function boot()
    {
        if (!$this->booted) {
            $this->bootCollectors();
            $this->booted = true;
        }
    }

    public function message(string $message): void
    {
        optional($this->getCollector('message'))->addMessage($message);
    }

    public function request(Request $request)
    {
        optional($this->getCollector('request'))->setRequest($request);
    }

    public function response(Response $response)
    {
        optional($this->getCollector('response'))->setResponse($response);
    }

    public function startMeasure(string $name)
    {
        $this->getCollector('time')->startMeasure($name);
    }

    public function stopMeasure(string $name)
    {
        $this->getCollector('time')->stopMeasure($name);
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.enabled');
    }

    public function log(): void
    {
        $report = implode(PHP_EOL, $this->data());
        $warnings = implode(PHP_EOL, $this->warnings());
        if ($warnings) {
            $warnings = PHP_EOL . implode(PHP_EOL, [str_repeat('!', 60), $warnings, str_repeat('!', 60)]);
        }
        \Log::debug(PHP_EOL . $report . $warnings);
    }

    private function bootCollectors()
    {
        foreach (static::$availableCollectors as $collector) {
            $this->addCollector($this->app->make($collector));
        }
    }

    private function getCollector($name): ?DataCollector
    {
        return data_get($this->collectors, $name);
    }

    private function addCollector(DataCollector $collector)
    {
        if ($collector->isEnabled()) {
            $collector->boot();
            $this->collectors[$collector->getName()] = $collector;
        }
    }

    private function data()
    {
        return collect($this->collectors)
            ->flatMap
            ->collect()
            ->sortBy('time')
            ->map(
                static function ($datum) {
                    return sprintf('=> [%s] %s : %s', $datum['pretty_time'], $datum['type'], $datum['message']);
                }
            )
            ->toArray();
    }

    private function warnings()
    {
        return collect($this->collectors)
            ->flatMap
            ->warnings()
            ->map(
                static function ($warning) {
                    return sprintf('!! %s : %s', $warning['type'], $warning['message']);
                }
            )->toArray();
    }
}
