<?php

namespace Metalogico\Formello\Facades;

use Illuminate\Support\Facades\Facade;

class Formello extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'formello';
    }
}
