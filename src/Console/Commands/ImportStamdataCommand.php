<?php
declare(strict_types=1);

namespace Swark\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Sheet;
use Swark\Services\Data\AlreadyRegisteredException;
use Swark\Services\Data\Concerns\HasPublicTitle;
use Swark\Services\Data\ImportService;
use TorMorten\Eventy\Facades\Eventy;

class ImportStamdataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import {path} {--strategies} {--regulations} {--infrastructure} {--rules} {--content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Excel sheet and Markdown files';

    public function handle()
    {
        $path = $this->argument('path');

        $importService = app()->make(ImportService::class);

        $this->configureLogging();

        try {
            $this->info("Importing $path...");

            $importService->import($path, array_keys($this->options()));
        } catch (AlreadyRegisteredException $e) {
            echo "{$e->key} for type {$e->type} is already registered" . PHP_EOL;

            foreach ($e->registeredMappings as $k => $v) {
                echo $k . ": " . $v . PHP_EOL;
            }

            throw $e;
        }
    }

    private static $instance = null;

    private static function instance($instance = null): ImportStamdataCommand
    {
        if ($instance !== null) {
            static::$instance = $instance;
        }

        return static::$instance;
    }

    private function configureLogging()
    {
        // it looks stupid, but otherwise we cannot access the logger from inside of the closure due to serialization issues.
        static::instance($this);

        Eventy::addAction('yo.swark.import.*', function ($tagDepth, $level, $message, $args, $tag) {
            $ref = static::instance();
            $padding = str_repeat('  ', $tagDepth - 2);
            $message = $padding . $message;

            match ($level) {
                "info" => $ref->info($message),
                "error" => $ref->error($message),
                "warn" => $ref->warn($message),
                "debug" => $ref->info($message),
            };
        }, 20, 5);

        Sheet::listen(BeforeSheet::class, function (BeforeSheet $event) {
            $name = $event->sheet->getTitle();

            if ($event->getConcernable() instanceof HasPublicTitle) {
                $name = $event->getConcernable()->publicTitle();
            }

            yo_info('Importing sheet %s', [$name], 'import.excel.sheet');
        });

        Sheet::listen(AfterSheet::class, function (AfterSheet $event) {
            yo_info('... done', tag: 'import.excel.sheet');
        });
    }
}
