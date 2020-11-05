<?php


namespace Dr\Redismon\Models;


class Job
{
    public $id;
    public $name;
    public $updatedAt;
    public $connection;
    public $queue;
    public $createdAt;
    public $status;
    public $exception;
    /** @var Payload */
    public $payload;

    public function fill($item)
    {
        $this->id = $item['id'] ?? null;
        $this->name = $item['name'] ?? null;
        $this->updatedAt = $item['updated_at'] ?? null;
        $this->connection = $item['connection'] ?? null;
        $this->queue = $item['queue'] ?? null;
        $this->createdAt = $item['created_at'] ?? null;
        $this->status = $item['status'] ?? null;
        $this->exception = $item['exception'] ?? null;
        $payload = $item['payload'] ?? null;
        if (!empty($payload)) {
            $this->payload = $this->parsePayload($payload);
        }

        if (empty($this->id)) {
            $this->id = $this->payload->id ?? null;
        }
        if (empty($this->name)) {
            $this->name = $this->payload->job ?? null;
        }
        if (empty($this->createdAt)) {
            $this->createdAt = $this->payload->pushedAt ?? null;
        }
    }

    public function parsePayload($payloadStr)
    {
        $payload = new Payload();
        $payload->fill(json_decode($payloadStr));
        return $payload;
    }

    public function scopeByStatus(&$comparisonResults, $status)
    {
        $comparisonResults[] = $this->status == $status;
    }

    public function scopeByModelName(&$comparisonResults, $modelName)
    {
        $comparisonResults[] = strpos($this->payload->model, $modelName) !== false;
    }

    public function scopeByJobName(&$comparisonResults, $jobName)
    {
        $comparisonResults[] = strpos($this->name, $jobName) !== false;
    }

    public function scopeByAttributes(&$comparisonResults, $search)
    {
        $str = json_encode($this->payload->attributes);
        $comparisonResults[] = strpos($str, $search) !== false;
    }
}
