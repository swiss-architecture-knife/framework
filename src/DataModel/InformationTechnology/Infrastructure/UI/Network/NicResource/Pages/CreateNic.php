<?php

namespace Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\DataModel\InformationTechnology\Infrastructure\UI\Network\NicResource;

class CreateNic extends CreateRecord
{
    use NestedPage;

    protected static string $resource = NicResource::class;
}
