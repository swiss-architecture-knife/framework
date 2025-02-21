<?php
declare(strict_types=1);

namespace Swark\Console\Commands;

use Illuminate\Console\Command;
use Swark\Ingester\Ingester;
use Swark\Ingester\Model\Context;
use Swark\Ingester\Model\Relationship\Attribute;
use Swark\Ingester\Model\StatusFlag;

/**
 * @deprecated
 */
class IngestFlatStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ingest-flat-structure-deprecated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest files from a flat YAML structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ingester = (new Ingester());

        $models = $ingester->detectModels();

        $rows = [];

        /** @var Context $context */
        foreach ($models as $context) {
            $row = [];

            $row[] = $context->alias;
            $row[] = $context->isUsable() ? 'Yes' : 'No';
            $row[] = $context->attributes() ? implode(", ", collect($context->attributes())->map(fn(Attribute $item) => $item->name . ($item->isUnique ? '*' : ''))->toArray()) : '';
            $row[] = implode(", ", collect($context->flags())->map(fn(StatusFlag $item) => $item->name)->toArray());

            $rows[] = $row;
        }

        $this->table(['Directory', 'Usable', 'Attributes', 'Flags'], $rows);

        $rows = [];
        foreach ($models as $context) {
            foreach ($context->getItems() as $modelBlueprint) {
                $row = [];

                $row[] = $context->alias;
                $row[] = $modelBlueprint->compoundIdentifier->getId();
                $row[] = collect($modelBlueprint->attributeToValueMapping)->map(fn($value, $idx) => $idx . '=>' . $value)->implode(",");

                $rows[] = $row;
            }
        }

        $this->table(['Model type', 'Compound ID', 'Mapping'], $rows);

        $models->unitOfWork()->ingestAll();
    }
}
