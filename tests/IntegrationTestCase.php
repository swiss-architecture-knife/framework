<?php

namespace Swark\Tests;


class IntegrationTestCase extends FeatureTestCase
{
    use MigrateDatabaseWithoutRollback;

    public function setUp(): void
    {
        parent::setUp();
        $this->runDatabaseMigrations();
    }
}

