<?php


namespace Dr\Services\Services;


use Carbon\Carbon;
use Dr\Redismon\Services\AbstractService;

class ChartDataService extends AbstractService
{
    public function getConsolidatedInfo()
    {
        return [
            'recent_jobs' => $this->rClient->zcard('horizon:recent_jobs'),
            'completed_jobs' => $this->rClient->zcard('horizon:completed_jobs'),
            'recent_failed_jobs' => $this->rClient->zcard('horizon:recent_failed_jobs'),
            'failed_jobs' => $this->rClient->zcard('horizon:failed_jobs'),
            'measured_jobs' => $this->rClient->sMembers('horizon:measured_jobs'),
            'measured_queues' => $this->rClient->sMembers('horizon:measured_queues'),
            'jobCountInQueue' => $this->getJobCountInQueue(),
            'time' => time(),
            'timeStr' => (new Carbon())->format("H:i:s")
        ];
    }

    public function getJobCountInQueue()
    {
        $key = 'queues:default';
        return $this->rClient->llen($key);
    }
}