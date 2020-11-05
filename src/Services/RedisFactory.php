<?php

namespace Dr\Redismon\Services;

class RedisFactory
{
    public static function connection($connection)
    {
        $client = new \Predis\Client(config('database.redis.' . $connection));
        $client->connect();
        return $client;
    }
}
