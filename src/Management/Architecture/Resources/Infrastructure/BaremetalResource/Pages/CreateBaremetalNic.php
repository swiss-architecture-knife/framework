<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages;

use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages\CreateChildNic;


class CreateBaremetalNic extends CreateChildNic {
    protected static string $resource = BaremetalResource::class;
}
