<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages;

use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class EditChildNic extends CreateRelatedRecord {
    use NestedPage;

    protected static string $relationship = 'nics';
}
