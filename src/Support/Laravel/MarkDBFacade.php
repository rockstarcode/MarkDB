<?php

namespace MarkDb\Support\Laravel;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\View\Factory
 */
class MarkDBFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'markdb';
    }
}
