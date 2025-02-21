<?php

namespace Swark\DataModel\Kernel\Domain\Model;

abstract class Name
{
    protected function __construct(public readonly array $parts, public readonly ?int $expected = null)
    {
    }

    public function title(): string
    {
        $use = $this->parts;
        if ($this->expected !== null && ($this->expected > ($totalCurrent = sizeof($use)))) {
            $use = array_merge($use, array_fill(0, $this->expected - $totalCurrent, '?'));
        }

        return implode(":", $use);
    }

    public function __toString(): string
    {
        return $this->title();
    }
}
