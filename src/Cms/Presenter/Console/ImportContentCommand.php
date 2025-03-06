<?php
declare(strict_types=1);

namespace Swark\Cms\Presenter\Console;

use Illuminate\Console\Command;
use Swark\Cms\Application\Exchange\ContentImportJob;
use Swark\Cms\Application\Exchange\ContentImportOptions;
use Swark\Cms\Domain\Model\Exchange\ContentImportResult;
use TorMorten\Eventy\Facades\Eventy;

class ImportContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swark:import:content {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports custom content from markdown files into database';

    public function handle()
    {
        $path = $this->argument('path');

        $options = new ContentImportOptions($path);
        $job = new ContentImportJob($options);

        $this->configureLogging();

        $result = $job->run();
        $table = collect($result)->map(fn(ContentImportResult $item) => [$item->localPath, $item->status->name, $item->message ?? '<empty>']);

        $this->table(['Path', 'Status', 'Message'], $table->toArray());
    }

    private static $instance = null;

    private static function instance($instance = null): ImportContentCommand
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
    }
}
