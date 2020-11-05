<?php


namespace Dr\Redismon\Services;


use Dr\Redismon\Models\Job;

class JobResultsService extends AbstractService
{
    public function getAll($page = 0, $params = [])
    {
        $keys = $this->rClient->keys('horizon:[0-9]*');
        $jobsCount = count($keys);
        $jobs = [];
        for ($i = $page * self::LIMIT;
             count($jobs) < self::LIMIT || empty($keys[$i]);
             $i++) {
            if (empty($keys[$i])) {
                break;
            }
            $redisItem = $this->rClient->hgetall($keys[$i]);
            $job = new Job();
            $job->fill($redisItem);
            if ($params != [] && !$this->satisfyFilterConditions($params, $job)) {
                continue;
            }
            $jobs[] = $job;
        }
        return ['items' => $jobs, 'pageCount' => ceil($jobsCount / self::LIMIT)];
    }

    protected function satisfyFilterConditions($params, Job $job)
    {
        $comparisonResults = [];
        if (!empty($params['status'])) {
            $job->scopeByStatus($comparisonResults, $params['status']);
        }
        if (!empty($params['jobName'])) {
            $job->scopeByJobName($comparisonResults, $params['jobName']);
        }
        if (!empty($params['modelName'])) {
            $job->scopeByModelName($comparisonResults, $params['modelName']);
        }
        if (!empty($params['existInAttributes']) && strlen($params['existInAttributes']) > 3) {
            $job->scopeByAttributes($comparisonResults, $params['existInAttributes']);
        }
        return $this->checkTheResultOfFiltration($comparisonResults);
    }

    public function deleteJobById($id)
    {
        return $this->rClient->del('horizon:' . $id);
    }
}
