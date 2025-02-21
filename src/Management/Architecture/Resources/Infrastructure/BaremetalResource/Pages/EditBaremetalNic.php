<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages;

use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages\EditChildNic;

class EditBaremetalNic extends EditChildNic {
    protected static string $resource = BaremetalResource::class;
}
