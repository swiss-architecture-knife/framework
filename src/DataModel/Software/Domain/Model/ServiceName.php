<?php

namespace Swark\DataModel\Software\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Domain\Model\Name;

class ServiceName extends Name
{
    public static function from(Model $record)
    {
        $args = [];

        if ($record->service_name) {
            $args = [
                $record->software_name,
                $record->component_name,
                $record->service_name,
            ];
        } else {
            $args = [
                $record->component->software->name,
                $record->component->name,
                $record->name,
            ];
        }

        return new static($args, 3);
    }
}
