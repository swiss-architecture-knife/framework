<?php

namespace Swark\DataModel\SoftwareArchitecture\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Domain\Model\Name;

class ComponentName extends Name
{
    public static function from(Model $record)
    {
        $args = [];

        if ($record->component_name) {
            $args = [
                $record->software_name,
                $record->component_name,
            ];
        } else {
            $args = [
                $record->software->name,
                $record->name,
            ];
        }

        return new static($args, 2);
    }
}
