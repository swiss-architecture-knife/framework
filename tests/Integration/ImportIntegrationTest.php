<?php

namespace Swark\Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use Swark\Tests\IntegrationTestCase;

class ImportIntegrationTest extends IntegrationTestCase
{
    // TODO
    #[Test]
    public function import_default_stamdata(): void
    {
        $this->markTestSkipped('must be revisited.');

        $defaultContentDirectory = __DIR__ . '/../../stamdata/_default';

        $this->artisan('app:import ' . $defaultContentDirectory);
    }

    // TODO
    #[Test]
    public function import_custom_stamdata(): void
    {
        $this->markTestSkipped('must be revisited.');

        $excelFile = __DIR__ . '/../../stamdata/custom/import.xlsx';

        $this->artisan('app:import ' . $excelFile);
    }
}
