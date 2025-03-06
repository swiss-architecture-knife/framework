<?php

namespace Swark\DataModel\Infrastructure\Exchange\Database;

use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;

class DatabaseRegulationHandler implements ImportableDatabaseRegulation
{

    public function upsertRegulation(array $args): Regulation
    {
        return Regulation::upsert(... $args);
    }

    public function upsertChapter(array $regulationChapterExtracted): Chapter
    {
        return $chapter = Chapter::updateOrCreate(...$regulationChapterExtracted);
    }

    public function upsertControl(array $regulationControl): Control
    {
        return $control = Control::updateOrCreate(...$regulationControl);
    }
}
