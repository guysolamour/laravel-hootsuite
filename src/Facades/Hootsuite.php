<?php

namespace Guysolamour\Hootsuite\Facades;

use Illuminate\Support\Facades\Facade;

class Hootsuite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hootsuite';
    }
}
