<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void boot()
 * @method static void log()
 * @method static void increment(string $name, int $counter = 1)
 * @method static void message(string $message)
 * @method static void request(\Illuminate\Http\Request $request)
 * @method static void response(\Symfony\Component\HttpFoundation\Response $response)
 * @method static void startMeasure(string $name)
 * @method static void stopMeasure(string $name)
 * @method static void measuring(string $name, callable $callable)
 * @method static void dd(...$vars)
 *
 * @see \Soyhuce\DevTools\Debug\DebugManager
 */
class Debug extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DebugManager::class;
    }
}
