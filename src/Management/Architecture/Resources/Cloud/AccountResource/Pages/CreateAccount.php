<?php

namespace Swark\Management\Architecture\Resources\Cloud\AccountResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Swark\Management\Architecture\Resources\Cloud\AccountResource;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;
}
