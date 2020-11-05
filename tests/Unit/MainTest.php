<?php

namespace Dr\Redismon\Tests\Unit;

use Dr\Redismon\Controllers\RedismonController;
use Dr\Redismon\Providers\RedismonServiceProvider;
use Dr\Redismon\Services\ItemsService;
use Dr\Redismon\Services\RedisFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;

class MainTest extends TestCase
{
    use RefreshDatabase;

    /** @var ItemsService */
    public $redisService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->redisService = app()->make(ItemsService::class);
    }

    public function testFactory()
    {
        $this->assertNotEmpty(RedisFactory::connection('default'));
    }

    public function testSetGet()
    {
        $str = 'public function testSet()';
        $key = 'testDD';
        $this->redisService->setItem($key, $str);
        $res = $this->redisService->getItem($key);
        $this->assertEquals($res, $str);
    }

    public function testGetJobList()
    {
        $jobs = $this->redisService->getJobs();
        $this->assertNotEmpty($jobs);
    }

    public function testController()
    {
        /** @var RedismonController $controller */
        $controller = $this->app->make(RedismonController::class);
        $res = $controller->getJobsInfo(1);
        $this->assertNotEmpty($res);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => 5432,
            'database' => 'redismon_testing',
            'username' => 'postgres',
            'password' => 'postgres',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);
        $app['config']->set('database.redis', [
            'default' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => env('REDIS_DB', 0),
            ],
        ]);
        $app['config']->push('app.providers', RedismonServiceProvider::class);
    }
}
