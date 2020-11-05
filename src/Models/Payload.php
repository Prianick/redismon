<?php


namespace Dr\Redismon\Models;


use Umbrellio\TableSync\Integration\Laravel\Jobs\PublishJob;
use Umbrellio\TableSync\Integration\Laravel\Jobs\ReceiveMessage;
use Umbrellio\TableSync\Messages\PublishMessage;
use Umbrellio\TableSync\Messages\ReceivedMessage;

class Payload
{
    public $id;
    public $uuid;
    public $displayName;
    public $job;
    public $maxTries;
    public $attempts;
    public $commandName;
    public $command;
    public $attributes;
    public $model;
    public $event;
    public $pushedAt;

    public function fill($payload)
    {
        $this->id = $payload->id ?? null;
        $this->uuid = $payload->uuid ?? null;
        $this->displayName = $payload->displayName ?? null;
        $this->job = $payload->job ?? null;
        $this->pushedAt = $payload->pushedAt ?? null;
        $this->maxTries = $payload->maxTries ?? null;
        $this->attempts = $payload->attempts ?? null;
        $this->commandName = $payload->data->commandName ?? null;
        $this->prepareCommandClass($this->unSerialize($payload->data->command) ?? null);
    }

    public function unSerialize($payload)
    {
        try {
            return unserialize($payload);
        } catch (\Exception $e) {
            $commandInfo = [
                'model' => $e->getMessage(),
                'attributes' => $e->getTraceAsString()
            ];
            $this->command = $commandInfo;
        }
    }

    public function prepareCommandClass($command)
    {
        if (!is_object($command)) {
            return ;
        }

        switch (get_class($command)) {
            default:
                $commandInfo = [
                    'model' => print_r($command, true),
                    'event' => '',
                    'attributes' => '',
                    'appId' => ''
                ];
                break;
        }
        $this->command = $commandInfo;
        $this->model = $commandInfo['model'];
        $this->event = $commandInfo['event'];
        $this->attributes = $commandInfo['attributes'];
    }

    public function scopeByModel(&$comparisonResults, $modelName)
    {
        $comparisonResults[] = strpos($this->model, $modelName) !== false;
    }

    public function scopeByJobName(&$comparisonResults, $modelName)
    {
        $comparisonResults[] = strpos($this->displayName, $modelName) !== false;
    }

    public function scopeByAttributes(&$comparisonResults, $search)
    {
        $str = json_encode($this->attributes);
        $comparisonResults[] = strpos($str, $search) !== false;
    }
}
