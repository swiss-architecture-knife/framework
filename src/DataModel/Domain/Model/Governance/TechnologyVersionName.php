<?php

namespace Swark\DataModel\Domain\Model\Governance;

use Illuminate\Database\Eloquent\Model;
use Swark\DataModel\Domain\Model\Name;

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
