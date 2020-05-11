<?php

namespace Soyhuce\DevTools\Facades;

use Illuminate\Support\Facades\Facade;
use Soyhuce\DevTools\DebugManager;

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
