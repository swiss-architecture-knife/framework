<?php

namespace Swark\Frontend\Domain\Architecture;

class C4Generator
{
    private $lines = [];

    public function create(): string
    {
        return '!include <C4/C4_Container>' . PHP_EOL . implode("\r\n", $this->lines);
    }

    public function push($line, bool $allowDuplicates = true)
    {
        $push = true;

        if (!$allowDuplicates) {
            $push = !in_array($line, $this->lines);
        }

        if ($push) {
            $this->lines[] = $line;
        }

        return $this;
    }

    public function pushOnce($line): C4Generator
    {
        return $this->push($line, allowDuplicates: false);
    }
}
