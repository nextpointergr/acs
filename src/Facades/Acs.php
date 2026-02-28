<?php
namespace NextPointer\Acs\Facades;

use Illuminate\Support\Facades\Facade;

class Acs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NextPointer\Acs\Services\AcsClient::class;
    }
}