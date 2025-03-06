<?php

namespace Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\DataModel\Presenter\UI\InformationTechnology\Network\NicResource;

class CreateNic extends CreateRecord
{
    use NestedPage;

    protected static string $resource = NicResource::class;
}
