<?php

namespace Soyhuce\DevTools\Debug\Collectors;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Soyhuce\DevTools\Debug\Entries\Entry;
use Soyhuce\DevTools\Debug\Entries\Query;
use Soyhuce\DevTools\Debug\Warnings\QueriesExceeded;
use function count;

class QueryCollector extends DataCollector
{
    /** @var array<\Soyhuce\DevTools\Debug\Entries\Query> */
    private array $queries = [];

    /**
     * QueryCollector constructor.
     */
    public function __construct(
        private Application $app,
    ) {
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
        $this->queries = [];
    }

    public function collect(): array
    {
        $collection = $this->queries;
        $collection[] = new Entry($this->getName(), 'query executed : ' . count($this->queries));

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
}
