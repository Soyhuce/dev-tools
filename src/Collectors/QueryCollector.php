<?php

namespace Soyhuce\DevTools\Collectors;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;

/**
 * Class QueryCollector
 */
class QueryCollector extends DataCollector
{
    /** @var Application */
    private $app;

    /** @var array */
    private $queries;

    /**
     * QueryCollector constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->queries = [];
        $this->app = $app;
    }

    public function getName(): string
    {
        return 'db';
    }

    public function isEnabled(): bool
    {
        return config('dev-tools.debugger.database.enabled');
    }

    public function boot()
    {
        $this->app['db']->listen(
            function (QueryExecuted $event) {
                $this->addQuery($event->sql, $event->bindings, $event->time, $event->connection);
            }
        );
    }

    public function collect(): array
    {
        return collect($this->queries)
            ->map(
                function ($query) {
                    return [
                        'time' => $query['time'],
                        'pretty_time' => Carbon::createFromTimestamp((int) $query['time'])->toDateTimeString(),
                        'message' => sprintf('%s -> %s', $query['query'], $this->format($query['duration'])),
                        'type' => $this->getName(),
                    ];
                }
            )
            ->push(
                [
                    'time' => $this->time(),
                    'pretty_time' => Carbon::createFromTimestamp((int) $this->time())->toDateTimeString(),
                    'message' => 'query executed : ' . count($this->queries),
                    'type' => $this->getName(),
                ]
            )
            ->toArray();
    }

    public function warnings(): array
    {
        $max = config('dev-tools.debugger.database.max_queries');
        if (!$max || count($this->queries) <= $max) {
            return [];
        }

        return [
            [
                'message' => sprintf('Number of queries exceeded max %s allowed : %s', $max, count($this->queries)),
                'type' => $this->getName(),
            ],
        ];
    }

    /**
     * @param string $query
     * @param array $bindings
     * @param float $duration
     * @param Connection $connection
     */
    public function addQuery(string $query, array $bindings, float $duration, Connection $connection)
    {
        $startTime = $this->time() - $duration / 1000;

        $pdo = $connection->getPdo();
        $bindings = $connection->prepareBindings($bindings);

        foreach ($bindings as $key => $binding) {
            // This regex matches placeholders only, not the question marks,
            // nested in quotes, while we iterate through the bindings
            // and substitute placeholders by suitable values.
            $regex = is_numeric($key)
                ? "/\\?(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/";
            $query = preg_replace($regex, $pdo->quote($binding), $query, 1);
        }

        $this->queries[] = [
            'time' => $startTime,
            'duration' => $duration,
            'query' => $query,
        ];
    }

    private function format($miliseconds)
    {
        if ($miliseconds < 1) {
            return round($miliseconds * 1000, 2) . 'μs';
        }
        if ($miliseconds >= 1000) {
            return round($miliseconds / 1000, 2) . 's';
        }

        return round($miliseconds, 2) . 'ms';
    }
}
