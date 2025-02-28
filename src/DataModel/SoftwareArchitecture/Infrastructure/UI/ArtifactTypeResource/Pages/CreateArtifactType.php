<?php

namespace Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\SoftwareArchitecture\Infrastructure\UI\ArtifactTypeResource;

class CreateArtifactType extends CreateRecord
{
    protected static string $resource = ArtifactTypeResource::class;
}
