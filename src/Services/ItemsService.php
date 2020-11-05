<?php

namespace Dr\Redismon\Services;

use Dr\Services\Services\ChartDataService;
use Predis\Client;

class ItemsService
{
    /** @var Client */
    protected $rClient;
    protected $jobService;
    protected $queueService;
    protected $chartDataService;

    public function __construct(JobResultsService $jobService, QueueService $queueService, ChartDataService $chartDataService)
    {
        $this->rClient = RedisFactory::connection('default');
        $this->jobService = $jobService;
        $this->queueService = $queueService;
        $this->chartDataService = $chartDataService;
    }

    public function getItems()
    {

    }

    public function getItem($key)
    {
        return $this->rClient->get($key);
    }

    public function setItem($key, $val)
    {
        $this->rClient->set($key, $val);
    }

    public function getJobResults($page = 1, $params = [])
    {
        $page -= 1;
        return $this->jobService->getAll($page, $params);
    }

    public function getConsolidationInfo()
    {
        return $this->jobService->getConsolidatedInfo();
    }

    public function getCurrentJobs($page = 1, $params = [])
    {
        $page -= 1;
        return $this->queueService->getAll($page, $params);
    }

    public function getChartsData()
    {
        return $this->chartDataService->getConsolidatedInfo();
    }
}
