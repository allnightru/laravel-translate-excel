<?php

namespace Ins\LaravelTranslateExcel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ins\LaravelTranslateExcel\LaravelTranslateExcel
 */
class LaravelTranslateExcel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Ins\LaravelTranslateExcel\LaravelTranslateExcel::class;
    }
}
