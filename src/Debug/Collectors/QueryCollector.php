<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Soyhuce\DevTools\Debug\Entries\Entry;
use Soyhuce\DevTools\Debug\Entries\Query;
use Soyhuce\DevTools\Debug\Warnings\QueriesExceeded;
use Soyhuce\DevTools\Tools\Stats;
use Soyhuce\DevTools\Tools\Time;
use function count;
use function sprintf;

class QueryCollector extends DataCollector
{
    /** @var Collection<int, Query> */
    private Collection $queries;

    /**
     * QueryCollector constructor.
     */
    public function __construct(
        private Application $app,
    ) {
        $this->queries = new Collection();
    }

    public function getName(): string
    {
        return 'database';
    }

    public function boot(): void
    {
        $this->app['db']->listen(function (QueryExecuted $event): void {
            $this->addQuery($event);
        });
    }

    private function addQuery(QueryExecuted $queryExecuted): void
    {
        $this->queries[] = new Query($this->getName(), $queryExecuted);
    }

    public function reset(): void
    {
        $this->queries = new Collection();
    }

    public function collect(): array
    {
        $collection = $this->queries->all();
        $collection[] = new Entry($this->getName(), $this->statistics());

        return $collection;
    }

    public function warnings(): array
    {
        $max = config('dev-tools.debugger.database.max_queries');
        if ($max === null) {
            return [];
        }

        $max = (int) $max;
        if (count($this->queries) <= $max) {
            return [];
        }

        return [
            new QueriesExceeded($this->getName(), $max, count($this->queries)),
        ];
    }

    private function statistics(): string
    {
        $statistics = ['query executed : ' . $this->subStatistics($this->queries)];

        $this->queries
            ->groupBy(fn (Query $query) => explode(' ', $query->sql, 2)[0])
            ->each(function (Collection $queries, string $type) use (&$statistics): void {
                $statistics[] = sprintf(
                    '%s%s : %s',
                    str_repeat(' ', 43),
                    $type,
                    $this->subStatistics($queries)
                );
            });

        return implode(PHP_EOL, $statistics);
    }

    /**
     * @param Collection<int, Query> $queries
     */
    public function subStatistics(Collection $queries): string
    {
        $stats = new Stats($queries->map(fn (Query $query) => $query->duration)->all());

        if ($stats->count() === 0) {
            return '0';
        }

        return sprintf(
            '%s / total duration %s (avg : %s - min : %s - max : %s - std : %s)',
            $stats->count(),
            Time::humanizeMilliseconds($stats->sum()),
            Time::humanizeMilliseconds($stats->avg()),
            Time::humanizeMilliseconds($stats->min()),
            Time::humanizeMilliseconds($stats->max()),
            Time::humanizeMilliseconds($stats->std())
        );
    }
}
