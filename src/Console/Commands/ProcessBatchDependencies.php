<?php
declare(strict_types=1);

namespace Swark\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Traversable;

class ProcessBatchDependencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-batch-dependencies {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads batch dependencies, validates it and generates dependency graphs out of it';


    private array $map = [];
    private ?Spreadsheet $spreadsheet = null;
    private ?Xlsx $reader = null;

    private function readSheet(string $name, string $untilColumn, int $skipRows = 1): \Generator
    {
        $worksheet = $this->spreadsheet->getSheetByName($name);

        $rowNumber = 0;

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowNumber++;

            if ($rowNumber <= $skipRows) {
                continue;
            }

            $dataArray = $worksheet->rangeToArray("A{$rowNumber}:{$untilColumn}{$rowNumber}");
            $row = $dataArray[0];

            if (!$row || (sizeof($row) == 0) || empty($row[0])) {
                continue;
            }

            yield $row;
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->reader = new Xlsx();
        $this->reader->setReadDataOnly(true);

        $path = $this->argument('path');

        if (!file_exists($path)) {
            throw new \Exception("File $path does not exist");
        }

        $this->spreadsheet = $this->reader->load($path);

        $context = $this->parse();
        $this->generate($context);
    }

    private function generate(Context $context)
    {
        $data = [];
        $dotPre = '';
        $dotPost = '';

        $gantt = '';
        $ganttPre = '';
        $ganttPost = '';
        $length = 30;

        /** @var Batch $batch */
        foreach ($context as $batch) {
            $pres = [];
            $posts = [];

            $gantt .= '[' . $batch->name->value . "] starts D+$length and requires 10 days" . PHP_EOL;
            $dotPre.= '"' . $batch->name->value . '" [label="'  . $batch->name->value . '", shape="box",style="filled",fillcolor="white"];' . PHP_EOL;

            /** @var Batch $pre */
            foreach ($batch->getPredecessors() as $pre) {
                $pres[] = $pre->name->value;

                $ganttPost .= '[' . $batch->name->value . '] starts at [' . $pre->name->value . ']\'s end' . PHP_EOL;
                $dotPost .= '"' . $pre->name->value . '" -> "' . $batch->name->value . '" [label=" ",color="blue",arrowhead="normal"];' . PHP_EOL;
            }

            /** @var Batch $post */
            foreach ($batch->getSuccessors() as $post) {
                $posts[] = $post->name->value;
            }

            $tasks = [];

            /** @var Task $task */
            foreach ($batch->getTasks() as $task) {
                $tasks[] = $task->name . " (" . $task->order . ")";
                $useLength = $length + 30;
                $gantt .= '[' . $task->name . "] starts D+{$useLength} and requires 10 days" . PHP_EOL;
                $ganttPost .= '[' . $task->name .'] starts at [' . $batch->name->value . ']\'s end' . PHP_EOL;

                $dotPre .= '"' . $task->name . '" [label="'  . $task->name . '", shape="circle",style="filled",fillcolor="white"];' . PHP_EOL;
                $dotPost .= '"' . $batch->name->value . '" -> "' . $task->name . '" [label=" ",color="green",arrowhead="normal"];' . PHP_EOL;
            }

            $businessProcesses = [];
            /** @var BusinessProcess $businessProcess */
            foreach ($batch->getParentBusinessProcesses() as $businessProcess) {
                $id = md5($businessProcess->name);
                $label = Str::substr($businessProcess->name, 0, 60, "UTF-8");

                $ganttPre .= '[' . $label . "... ] as [$id] requires 15 days" . PHP_EOL;
                $ganttPost .= '[' . $batch->name->value .'] starts at [' . $id . ']\'s end' . PHP_EOL;

                $dotPre .= '"' . $id . '" [label="'  . $label . '", shape="circle",style="filled",fillcolor="white"];' . PHP_EOL;
                $dotPost .= '"' . $id . '" -> "' . $batch->name->value . '" [label=" ",color="blue",arrowhead="normal"];' . PHP_EOL;
                $businessProcesses[] = $businessProcess->name;
            }

            $data[] = [
                $batch->name->value,
                implode(", ", $businessProcesses),
                implode(",", $pres),
                implode(",", $posts),
                implode(",", $tasks)
            ];
        }

        $this->table(['Name', 'Triggered by process', 'Pre', 'Post', 'Batch tasks'], $data);
        $gantt = '@startgantt' . PHP_EOL . $ganttPre . PHP_EOL . $gantt . PHP_EOL . $ganttPost . PHP_EOL . "@endgantt";

        file_put_contents("/mnt/c/temp/gantt.txt", utf8_decode($gantt));

        $dot = <<<DOT
digraph G {
fontname="Helvetica,Arial,sans-serif"
node [fontname="Helvetica,Arial,sans-serif"]
edge [fontname="Helvetica,Arial,sans-serif"]
ratio = "auto" ;
mincross = 2.0 ;

  $dotPre

  $dotPost
}
DOT;

        file_put_contents("/mnt/c/temp/dot.g", utf8_decode($dot));
    }

    private function parse(): Context
    {
        $r = new Context();

        // --- BATCH
        foreach ($this->readSheet('Batch', 'E') as $row) {
            $r->addBatch($row[0], empty($row[3]) ? null : (int)$row[3], $row[4]);
        }

        // references
        foreach ($this->readSheet('Batch', 'E') as $row) {
            /** @var Batch $batch */
            $batch = $r->get($row[0]);

            $predecessors = strlen(trim($row[1] ?? '')) > 0 ? explode(",", trim($row[1])) : [];
            $successors = strlen(trim($row[2] ?? '')) > 0 ? explode(",", trim($row[2])) : [];

            foreach ($predecessors as $predecessor) {
                $batch->succeeds($r->get($predecessor));
            }

            foreach ($successors as $successor) {
                $batch->preceeds($r->get($successor));
            }
        }

        // --- TASKS
        foreach ($this->readSheet('Tasks', 'D') as $row) {
            $r->get($row[1])->addTask($row[0], (int)$row[2], $row[3]);
        }

        // --- BUSINESS PROCESSES
        foreach ($this->readSheet('Business Case Mapping', 'E') as $row) {
            $rawRefs = !empty(trim($row[3] ?? '')) ? explode(',', $row[3]) : [];
            $refs = [];

            foreach ($rawRefs as $rawRef) {
                $refs[] = $r->get(trim($rawRef));
            }

            $r->addBusinessProcess($row[0], $row[1], $row[2], $refs, !empty($row[4]) ? true : false);
        }

        return $r;
    }
}

class Context implements \IteratorAggregate
{
    private array $batches = [/* name => Batch */];

    private array $businessProcesses = [/* main => ['$sub' => []] */];

    public function addBatch(string $name, ?int $scheduler, ?string $description = null)
    {
        $batchName = new BatchName($name);

        if (isset($this->batches[$batchName->value])) {
            throw new \Exception("Batch " . $batchName->value . "is already registered");
        }

        $this->batches[$batchName->value] = new Batch($batchName, $scheduler, $description);
        return $this->batches[$batchName->value];
    }

    public function addBusinessProcess(string $name, string $categoryMain, ?string $categorySub, array $triggeredBatches = [], bool $isAutomatic = false): BusinessProcess
    {
        if (!isset($this->businessProcesses[$categoryMain])) {
            $this->businessProcesses[$categoryMain] = [];
        }

        $categorySub = $categorySub ?? '';

        if (!isset($this->businessProcesses[$categoryMain][$categorySub])) {
            $this->businessProcesses[$categoryMain][$categorySub] = [];
        }

        $bp = new BusinessProcess($name, $categoryMain, $categorySub, $triggeredBatches, $isAutomatic);

        /** @var Batch $triggeredBatch */
        foreach ($triggeredBatches as $triggeredBatch) {
            // reverse link
            $triggeredBatch->addParentBusinessProcess($bp);
        }

        $this->businessProcesses[$categoryMain][$categorySub][] = $bp;
        return $bp;
    }

    public function getBusinessProcesses(): array
    {
        return collect($this->businessProcesses)->collapse()->toArray();
    }

    public function get(BatchName|string $name): Batch
    {
        $ref = is_string($name) ? $name : $name->value;

        if (!isset($this->batches[$ref])) {
            throw new \Exception("Referenced batch $name does not exist");
        }

        return $this->batches[$ref];
    }


    public function getIterator(): Traversable
    {
        return new \ArrayIterator(array_values($this->batches));
    }
}

class Batch
{
    private $tasks = [];
    private $predecessors = [];
    private $successors = [];

    private $parentBusinessProcesses = [];

    public function __construct(
        public readonly BatchName $name,
        public readonly ?int      $scheduler = null,
        public readonly ?string   $description = null,
    )
    {
    }

    public function addTask(string $name, int $order, ?string $description = null): Batch
    {
        $this->tasks[] = new Task($name, $this, $order, $description);
        return $this;
    }

    public function addParentBusinessProcess(BusinessProcess $businessProcess): Batch
    {
        $this->parentBusinessProcesses[] = $businessProcess;
        return $this;
    }

    public function getParentBusinessProcesses(): array
    {
        return $this->parentBusinessProcesses;
    }

    public function getPredecessors(): array
    {
        return $this->predecessors;
    }

    public function getSuccessors(): array
    {
        return $this->successors;
    }

    public function getTasks(): array
    {
        return collect($this->tasks)->sortBy(fn($item) => $item->order)->toArray();
    }

    public function succeeds(Batch $batch)
    {
        $this->predecessors[] = $batch;
        return $this;
    }

    public function preceeds(Batch $batch)
    {
        $this->successors[] = $batch;
        return $this;
    }
}

class BatchName
{
    public function __construct(public readonly string $value)
    {
    }
}

class Task
{
    public function __construct(public readonly string  $name,
                                public readonly Batch   $parent,
                                public readonly int     $order,
                                public readonly ?string $description = null,
    )
    {
    }
}

class BusinessProcess
{
    public function __construct(public readonly string $name,
                                public readonly string $categoryMain,
                                public readonly string $categorySub,
                                public readonly array  $referencedBatches = [],
                                public readonly ?bool  $isAutomatic = null,
    )
    {

    }
}
