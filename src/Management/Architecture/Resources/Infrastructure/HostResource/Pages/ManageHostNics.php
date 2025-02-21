<?php
namespace Swark\Management\Architecture\Resources\Infrastructure\HostResource\Pages;


use Swark\Management\Architecture\Resources\Infrastructure\HostResource;
use Swark\Management\Architecture\Resources\Infrastructure\NicResource\Pages\ManageChildNics;

class ManageHostNics extends ManageChildNics
{
    protected static string $resource = HostResource::class;
}
