<?php
namespace Swark\Kernel\Infrastructure\Facades;

class Cms extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string {
        return 'cms';
    }
}
