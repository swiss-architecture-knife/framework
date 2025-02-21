<?php
declare(strict_types=1);

namespace Swark\Internal\Listeners;

use Illuminate\Log\Events\MessageLogged;
use Laravel\Prompts\Output\ConsoleOutput;

class MessageLoggedListener
{
    public function handle(MessageLogged $event)
    {
        if (app()->runningInConsole() && in_array($event->level, ['error', 'warning'])) {
            $string =
                match ($event->level) {
                    "error" => "<error>" . $event->message . "</error>",
                    "warning" => "<bg=yellow>" . $event->message . "</>",
                    default => $event->message,
                };

            $output = new ConsoleOutput();
            $output->writeln($string);
        }
    }
}
