<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource\Pages;


use Swark\Management\Architecture\Resources\Infrastructure\BaremetalResource;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages\ManageChildNics;

class ManageBaremetalNics extends ManageChildNics
{
    protected static string $resource = BaremetalResource::class;
}
