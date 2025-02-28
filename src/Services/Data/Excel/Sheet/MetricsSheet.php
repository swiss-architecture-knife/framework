<?php

namespace Swark\Services\Data\Excel\Sheet;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithTitle;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Kpi;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Measurement;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Metric;
use Swark\DataModel\Governance\Domain\Entity\Kpi\Period;
use Swark\Services\Data\Excel\Column;
use Swark\Services\Data\Excel\Header;
use Swark\Services\Data\Excel\Import\RowContext;

class MetricsSheet extends AbstractSwarkExcelSheet implements FromGenerator, WithTitle
{
    use Exportable;

    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const TYPE_COLUMN = 'type';
    const PRECISION_COLUMN = 'precision';
    const GOAL_DIRECTION_COLUMN = 'goal_direction';
    const GOAL_VALUE_COLUMN = 'goal_value';
    const THRESHOLD_1_COLUMN = 'threshold_1';
    const THRESHOLD_2_COLUMN = 'threshold_2';
    const ASSIGNED_COLUMN = 'assigned';
    const CURRENT_VALUE_COLUMN = 'current';

    public function generator(): \Generator
    {
        yield ['name', 'scomp-id', 'description', 'begin at', 'end at'];
    }

    public function createHeader(): Header
    {
        return (new Header())
            ->add(Column::of('Name', static::NAME_COLUMN))
            ->add(Column::scompId())
            ->add(Column::of('Description', static::DESCRIPTION_COLUMN))
            ->add(Column::of('Type', static::TYPE_COLUMN))
            ->add(Column::of('Precision', static::PRECISION_COLUMN))
            ->add(Column::of('Goal direction', static::GOAL_DIRECTION_COLUMN))
            ->add(Column::of('Goal value', static::GOAL_VALUE_COLUMN))
            ->add(Column::of('Threshold 1', static::THRESHOLD_1_COLUMN))
            ->add(Column::of('Threshold 2', static::THRESHOLD_2_COLUMN))
            ->add(Column::of('Assigned', static::ASSIGNED_COLUMN))
            ->add(Column::of('Current value', static::CURRENT_VALUE_COLUMN))
            ->
            next()
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('[boolean, decimal percentage, time_seconds, time_minutes, time_hours, time_days]'))
            ->add(Column::empty())
            ->add(Column::of('[higher,lower]'))
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::empty())
            ->add(Column::of('${action.scomp_id | strategy_objective.scomp_id}'))
            ->add(Column::empty());
    }

    public function title(): string
    {
        return "Metrics";
    }

    private ?Period $default = null;

    protected function importRow(RowContext $row)
    {
        $metric = Metric::updateOrCreate([
            'scomp_id' => $row->nonEmpty(Column::SCOMP_ID_COLUMN),
        ], [
            'name' => $row[static::NAME_COLUMN],
            'description' => $row[static::DESCRIPTION_COLUMN],
            'type' => $row[static::TYPE_COLUMN],
            'goal_direction' => $row[static::GOAL_DIRECTION_COLUMN],
            'precision' => $row->ifPresent(static::PRECISION_COLUMN, fn($value) => (int)$value) ?? 0,
            // idx 7 is threshold
            'is_measurable' => $row->ifPresent(static::THRESHOLD_1_COLUMN, fn($value) => true) ?? false,
            'is_system_parameter' => $row->ifPresent(static::THRESHOLD_2_COLUMN, fn($value) => false) ?? true,
        ]);

        $this->compositeKeyContainer->set('metric', $metric->scomp_id, $metric->id);

        $row->ifPresent(static::GOAL_VALUE_COLUMN, function ($value) use ($row, $metric) {
            DB::delete("DELETE FROM kpi_assigned WHERE kpi_id IN (SELECT id FROM kpi WHERE metric_id = ?)", [$metric->id]);

            $type = $row[static::TYPE_COLUMN];
            $threshold1 = $row[static::THRESHOLD_1_COLUMN];
            $threshold2 = $row[static::THRESHOLD_2_COLUMN];

            $kpi = Kpi::updateOrCreate([
                'metric_id' => $metric->id
            ], [
                'goal_value' => (float)$row[static::GOAL_VALUE_COLUMN],
                'integer_threshold_1' => $type == 'decimal' ? $threshold1 : null,
                'integer_threshold_2' => $type == 'decimal' ? $threshold2 : null,
                'percentage_threshold_1' => $type == 'percentage' ? $threshold1 : null,
                'percentage_threshold_2' => $type == 'percentage' ? $threshold2 : null,
            ]);

            $rawScomps = $row[static::ASSIGNED_COLUMN];

            if (!empty($rawScomps)) {
                $scomps = explode(",", $rawScomps);
                foreach ($scomps as $scomp) {
                    $parts = explode(":", $scomp);
                    $scompType = $parts[0];

                    $refId = $this->compositeKeyContainer->get($scompType, ... array_slice($parts, 1));

                    if ($scompType == 'strategy_objective') {
                        $kpi->objectives()->attach($refId);
                    } elseif ($scompType == 'action') {
                        $kpi->actions()->attach($refId);
                    } else {
                        throw new \Exception("Unknown $scompType $scompType");
                    }
                }

                $kpi->save();
            }

            DB::delete("DELETE FROM measurement WHERE kpi_id = ?", [$kpi->id]);

            $measurement = new Measurement(['measurement_period_id' => $this->compositeKeyContainer->get('measurement_period', 'default'), 'current_value' => (float)$row[static::CURRENT_VALUE_COLUMN]]);
            $kpi->measurements()->save($measurement);
        });
    }
}

