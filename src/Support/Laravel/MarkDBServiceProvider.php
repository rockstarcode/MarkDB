<?php

namespace MarkDB\Support\Laravel;

use Illuminate\Support\ServiceProvider;
use MarkDB\MarkDb;

class CmsServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('markdb', function($app)
        {
            return new MarkDb(env('MARKDB_PATH'));
        });

    }

}