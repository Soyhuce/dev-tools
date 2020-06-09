<?php

namespace Soyhuce\DevTools\Debug;

use Illuminate\Support\Facades\Facade;

/**
 * Class Debug
 */
class Debug extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DebugManager::class;
    }
}
