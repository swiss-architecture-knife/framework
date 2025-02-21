<?php

namespace Swark\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;
use Maatwebsite\Excel\ExcelServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Swark\SwarkServiceProvider;
use TorMorten\Eventy\EventServiceProvider;

trait MigrateDatabaseWithoutRollback
{
    use CanConfigureMigrationCommands;

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->beforeRefreshingDatabase();
        $this->migrateTestDatabase();
        $this->afterRefreshingDatabase();
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function migrateTestDatabase()
    {
        $this->artisan('migrate');

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * Perform any work that should take place before the database has started refreshing.
     *
     * @return void
     */
    protected function beforeRefreshingDatabase()
    {
        // ...
    }

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
        // ...
    }
}
