<?php

namespace Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource;

class CreateNic extends CreateRecord
{
    use NestedPage;

    protected static string $resource = NicResource::class;
}
