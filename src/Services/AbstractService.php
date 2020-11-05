<?php


namespace Dr\Redismon\Services;


abstract class AbstractService
{
    const LIMIT = 50;

    /** @var \Predis\Client */
    protected $rClient;

    public function __construct()
    {
        $this->rClient = RedisFactory::connection('default');
    }

    protected function checkTheResultOfFiltration($comparisonResults)
    {
        foreach ($comparisonResults as $res) {
            if (!$res) {
                return false;
            }
        }
        return true;
    }
}