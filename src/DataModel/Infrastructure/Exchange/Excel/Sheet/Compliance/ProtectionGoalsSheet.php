<?php

namespace Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\Compliance;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoal;
use Swark\DataModel\Infrastructure\Eloquent\Model\Compliance\ProtectionGoalLevel;
use Swark\DataModel\Infrastructure\Exchange\Excel\Column;
use Swark\DataModel\Infrastructure\Exchange\Excel\Header;
use Swark\DataModel\Infrastructure\Exchange\Excel\RowContext;
use Swark\DataModel\Infrastructure\Exchange\Excel\Sheet\AbstractSwarkExcelSheet;

class ProtectionGoalsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const LEVELS_COLUMN = 'levels';

    public function generator(): \Generator
    {
        for ($i = 1; $i <= 10; $i++) {
            yield [$i, $i + 1, $i + 2];
        }
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Levels', static::LEVELS_COLUMN));
    }

    public function title(): string
    {
        return "Protection goals";
    }

    protected function importRow(RowContext $row)
    {
        $protectionGoal = ProtectionGoal::upsert(
            $row->nonEmpty(Column::SCOMP_ID_COLUMN),
            [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
        ]);

        $this->compositeKeyContainer->set('protection_goal', $protectionGoal->configurationItem->scomp_id, $protectionGoal->id);

        $position = 0;
        foreach (($protectionGoalLevels = $row->explode(",", static::LEVELS_COLUMN)) as $protectionGoalLevel) {
            $level = ProtectionGoalLevel::upsert([
                'name' => $protectionGoalLevel,
                'protection_goal_id' => $protectionGoal->id,
                'position' => $position++,
            ]);
        }
    }
}

