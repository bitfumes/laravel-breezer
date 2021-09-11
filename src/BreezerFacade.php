<?php

namespace Bitfumes\Breezer;

use Illuminate\Support\Facades\Facade;

class BreezerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'breezer';
    }
}
