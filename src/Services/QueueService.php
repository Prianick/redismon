<?php


namespace Dr\Redismon\Services;


use Dr\Redismon\Models\Payload;

class QueueService extends AbstractService
{
    public function getAll($page = 0, $params = [])
    {
        $key = 'queues:default';
        $len = $this->rClient->llen($key);
        $start = $page * self::LIMIT;
        $items = $this->rClient->lrange($key, $start, $start + self::LIMIT - 1);
        $parsedItems = [];
        foreach ($items as $item) {
            $payload = new Payload();
            $payload->fill(json_decode($item));
            if ($this->satisfyFilterConditions($params, $payload)) {
                $parsedItems[] = $payload;
            }
        }
        return [
            'items' => $parsedItems,
            'pageCount' => ceil($len / self::LIMIT)
        ];
    }

    protected function satisfyFilterConditions($params, Payload $payload)
    {
        $comparisonResults = [];
        if (!empty($params['jobName'])) {
            $payload->scopeByJobName($comparisonResults, $params['jobName']);
        }
        if (!empty($params['modelName'])) {
            $payload->scopeByModel($comparisonResults, $params['modelName']);
        }
        if (!empty($params['existInAttributes']) && strlen($params['existInAttributes']) > 3) {
            $payload->scopeByAttributes($comparisonResults, $params['existInAttributes']);
        }
        return $this->checkTheResultOfFiltration($comparisonResults);
    }

    public function deleteQueue()
    {
        return $this->rClient->del('queues:default');
    }
}