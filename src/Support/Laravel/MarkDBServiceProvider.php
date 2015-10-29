<?php

namespace MarkDb\Support\Laravel;

use Illuminate\Support\ServiceProvider;
use MarkDb\MarkDb;

class MarkDBServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('markdb', function($app)
        {
            $markdb =  new MarkDb(env('MARKDB_PATH'));

            /**
             * converts collection of arrays into
             */
            $markdb->setArrayFilter(function($array, $page, $limit) use($markdb){

                return new \Illuminate\Pagination\LengthAwarePaginator(
                    array_slice($markdb->articles, (($page - 1) * $limit), $limit),
                    count($markdb->articles),
                    $limit
                );

            });

            return $markdb;

        });

    }

}