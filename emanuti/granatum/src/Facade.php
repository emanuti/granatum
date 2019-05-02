<?php 
namespace Emanuti\Granatum;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return Granatum::class;
    }
}
