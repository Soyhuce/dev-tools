<?php

namespace Soyhuce\DevTools\Debug\Entries;

use Illuminate\Database\Events\QueryExecuted;
use Soyhuce\DevTools\Tools\Time;

class Query extends Entry
{
    public function __construct(string $source, QueryExecuted $queryExecuted)
    {
        parent::__construct(
            $source,
            $this->format($queryExecuted)
        );
        $this->restoreMicroTimeToQueryBegin($queryExecuted);
    }

    private function format(QueryExecuted $queryExecuted): string
    {
        return sprintf(
            '%s -> %s',
            $this->toReadableSql($queryExecuted),
            Time::humanizeMilliseconds($queryExecuted->time)
        );
    }

    private function toReadableSql(QueryExecuted $queryExecuted): string
    {
        $connection = $queryExecuted->connection;
        $grammar = $connection->getQueryGrammar();

        return $grammar->substituteBindingsIntoRawSql(
            $queryExecuted->sql,
            $connection->prepareBindings($queryExecuted->bindings)
        );
    }

    private function restoreMicroTimeToQueryBegin(QueryExecuted $queryExecuted): void
    {
        $this->microTime -= $queryExecuted->time / 1000;
    }
}
