<?php

namespace ZanySoft\LaravelPDF\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \ZanySoft\LaravelPDF\PDF make(string $filemane = '')
 */
class PDF extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'pdf';
    }

}