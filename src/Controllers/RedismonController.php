<?php


namespace Dr\Redismon\Controllers;

use Dr\Redismon\Services\ItemsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RedismonController extends Controller
{
    protected $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }

    protected function getRequestParams(Request $request)
    {
        return [
            'jobName' => $request->get('jobName'),
            'existInAttributes' => $request->get('attributesContain'),
            'status' => $request->get('status'),
            'modelName' => $request->get('modelName'),
        ];
    }

    public function getJobsInfo(Request $request)
    {
        $page = $request->get('page', 1);
        $data = $this->itemsService->getJobResults($page, $this->getRequestParams($request));
        return $this->render('redismon::jobs', $data);
    }

    public function getQueueInfo(Request $request)
    {
        $page = $request->get('page', 1);
        $data = $this->itemsService->getCurrentJobs($page, $this->getRequestParams($request));
        return $this->render('redismon::jobs', $data);
    }

    public function getChartData()
    {
        return $this->itemsService->getChartsData();
    }

    protected function render($view, $data)
    {
        if (config('redismon.viewMode') == 'bladeTemplates')
            return app('view')->make($view, $data);
        else
            return $data;
    }
}
