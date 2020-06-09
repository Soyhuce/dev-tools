# Dev Tools

Tools for Laravel development

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/dev-tools.svg?style=flat-square)](https://packagist.org/packages/soyhuce/dev-tools)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/dev-tools.svg?style=flat-square)](https://packagist.org/packages/soyhuce/dev-tools)

- [Installation](#installation)
- [Available tools](#available-tools)
    - [Debug](#debug)
    - [Bottleneck Middleware](#bottleneckmiddleware)
    - [Image Faker](#image-faker)

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
 * [Image faker](#image-faker) : Generates random images locally

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

This middleware adds some latency to your requests, server side. It can be useful to check how your application behaves when the user does not have a good network connection.

`Soyhuce\DevTools\Middlewares\BottleneckMiddleware` can be used as a classic middleware.
To use it, just add the middleware in your `App/Http/Kernel.php` or in your route file(s).

You can modify bottleneck duration in `config/dev-tools.php` file.

You also may want to apply it for only ajax requests. If so, adjust the `only_ajax` value. Please ensure that the ajax requests are sent with the `X-Requested-With` header set to `XMLHttpRequest`.

### Image Faker

Sometimes you want to generate images locally for testing, placeholders, ...

You can then use `Soyhuce\DevTools\Faker\Image` to do so. For this, you have to install `intervention/image`.

```
Image::generate(int $width = 640, int $height = 640, ?string $text = null, string $encoding = 'jpg'): \Intervention\Image\Image
```

It will generate an image with random color and with the given text (or "width x height"). For example :

`Image::generate(200, 150)`

![](assets/doc/fake_image_200x150.jpg)

`Image::generate(300, 100, 'The colors are not that good', 'png')`

![](assets/doc/fake_image_text.png)

See [intervention/image documentation](http://image.intervention.io/) to know how to use returned image.
