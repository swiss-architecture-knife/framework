<?php

namespace Swark\DataModel\Business\Infrastructure\UI\OrganizationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\DataModel\Business\Infrastructure\UI\OrganizationResource;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;
}
