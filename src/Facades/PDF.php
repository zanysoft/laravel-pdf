<?php

namespace ZanySoft\LaravelPDF\Facades;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Facade;
use RuntimeException;

/**
 * @method static \ZanySoft\LaravelPDF\PDF make(string $filemane = '')
 * @method static \ZanySoft\LaravelPDF\PDF loadHTML(string|Htmlable $html)
 * @method static \ZanySoft\LaravelPDF\PDF loadView($view, array $data = [], array $mergeData = [])
 * @method static \ZanySoft\LaravelPDF\PDF loadFile(string $file)
 */
class PDF extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'mpdf.wrapper';
    }


    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array<mixed> $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        /** @var \Illuminate\Contracts\Foundation\Application|null */
        $app = static::getFacadeApplication();
        if (! $app) {
            throw new RuntimeException('Facade application has not been set.');
        }

        // Resolve a new instance, avoid using a cached instance
        $instance = $app->make(static::getFacadeAccessor());

        return $instance->$method(...$args);
    }

}