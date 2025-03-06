<?php

namespace Swark\DataModel\Infrastructure\Exchange\Database;

use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Chapter;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Control;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\Regulation;

interface ImportableDatabaseRegulation
{
    public function upsertRegulation(array $args): Regulation;

    public function upsertChapter(array $regulationChapterExtracted): Chapter;

    public function upsertControl(array $regulationControl): Control;
}
