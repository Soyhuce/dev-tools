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
        $query = $queryExecuted->sql;
        $pdo = $queryExecuted->connection->getPdo();
        $bindings = $queryExecuted->connection->prepareBindings($queryExecuted->bindings);

        foreach ($bindings as $key => $binding) {
            // This regex matches placeholders only, not the question marks,
            // nested in quotes, while we iterate through the bindings
            // and substitute placeholders by suitable values.
            $regex = is_numeric($key)
                ? "/\\?(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/";
            $query = preg_replace($regex, $pdo->quote($binding), $query, 1);
        }

        return $query;
    }

    private function restoreMicroTimeToQueryBegin(QueryExecuted $queryExecuted): void
    {
        $this->microTime -= $queryExecuted->time / 1000;
    }
}
