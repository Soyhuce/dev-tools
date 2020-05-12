# Dev Tools

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/dev-tools.svg?style=flat-square)](https://packagist.org/packages/soyhuce/dev-tools)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/dev-tools.svg?style=flat-square)](https://packagist.org/packages/soyhuce/dev-tools)

## Description

This package provides a lot of tools for backend development

## Installation

You can install the package via composer:

``` bash
$ composer require soyhuce/dev-tools --dev
```

In Laravel, instead of adding the service provider in the config/app.php file, you can add the following code to your app/Providers/AppServiceProvider.php file, within the register() method:
``` php
public function register()
{
    if (app()->environment(['local', 'testing'])) {
        $this->app->register(\Soyhuce\DevTools\ServiceProvider::class);
    }
}
```

After installing Dev Tools, publish its assets using:

```bash
php artisan vendor:publish --provider="Soyhuce\DevTools\ServiceProvider" --tag="config"
```

Add facade to your alias in `config\app.php` :
```php
'Debug' => Soyhuce\DevTools\Facades\Debug::class,
```

## Available tools

 * [Debug](#debug) : Debugging tool
 * [BottleneckMiddleware](#bottleneckmiddleware) : Middleware simulating a bottleneck

### Debug

When activated, it allows logging several information on request execution: HTTP request, HTTP response, database requests, timings or even used memory.

Every module can be activated or deactivated separately.

Database, timing and memory modules own their specific configuration to rise an alert when request count, execution time or used memory exceed a predefined threshold.

Collective information are sent to the specified log stack (in debug mode).
 
#### Time
 
 It is possible to time performance portions via the facade :
 ```php
 \Debug::startMeasure('some name');
 // execute some code
 \Debug::stopMeasure('some name');
 ```
 
 `startMeasure` and `stopMeasure` methods don't have to be in the same way. All measurements in progress are completed at the end of the execution of the query.
  
#### Message

It is possible to send a message in the debugger via:
```php
\Debug::message('A message');
```

#### Model

When enabled, the collector allows you to count the number of models retrieved from the database during the query.

### Special use case of outside HTTP context execution

Debugger bootstrapping and collected information logging take place in an automatically added middleware.

If the portion of code you want to debug is not located inside the HTTP stack, you must bootstrap the debugger and log information manually :

For example:
```php
/** @test */
public function theUsersAreCorrectlyImported()
{
    \Debug::boot();
    Artisan::call('import:users');
    \Debug::log();
    $this->assertDatabaseHas('users', ['email'=> 'john.doe@email.com']);
}
```

### BottleneckMiddleware
* Usage

`BottleneckMiddleware` can be used as a classic middleware.
To use it, just add the middleware path in the Kernel (see /App/Http/Kernel.php).

* Configuration

The `bottleneck.php` configuration file is located in the `vendor/soyhuce/dev-tools/src/config` directory.
To adapt the sleep duration (in milliseconds), just modify the `duration` value.

In order to apply the bottleneck only for AJAX requests, you just have to change the boolean variable `only_ajax`.
If another lib than jQuery is used for your AJAX calls, add your request the "HTTP_X_REQUESTED_WITH" header with "XMLHttpRequest" value.
