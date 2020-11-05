<?php

namespace Dr\Redismon\Providers;

use Illuminate\Support\ServiceProvider;

class RedismonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'redismon');
    }
}
