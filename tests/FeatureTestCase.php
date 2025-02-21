<?php

namespace Swark\Tests;

use Illuminate\Contracts\Config\Repository;
use Maatwebsite\Excel\ExcelServiceProvider;
use Swark\SwarkServiceProvider;
use TorMorten\Eventy\EventServiceProvider;

class FeatureTestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app->setBasePath(__DIR__ . '/..');
    }

    protected function getPackageProviders($app)
    {
        return [
            EventServiceProvider::class,
            ExcelServiceProvider::class,
            SwarkServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config) use ($app){
            $config->set('database.default', 'mysql_testing');
            $config->set('database.connections.mysql_testing', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'database' => env('DB_DATABASE', 'swark_testing'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', 'root'),
                'port' => env('DB_PORT', '3306'),
                'prefix' => '',
            ]);
        });
    }

    protected $enablesPackageDiscoveries = true;
}

