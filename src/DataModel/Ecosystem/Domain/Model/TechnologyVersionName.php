<?php

namespace Swark\DataModel\Ecosystem\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Kernel\Domain\Model\Name;

class TechnologyVersionName extends Name
{
    public static function from(Model $record)
    {
        $args = [];

        if ($record->technology_version_name) {
            $args = [
                $record->technology_name,
                $record->technology_version_name,
            ];
        } else {
            $args = [
                $record->technology->name,
                $record->name,
            ];
        }

        return new static($args, 2);
    }
}
