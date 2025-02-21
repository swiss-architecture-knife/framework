<?php

namespace Swark\Services\Data;

class AlreadyRegisteredException extends \Exception
{
    public function __construct(public readonly string $key, public readonly string $type, public readonly array $registeredMappings)
    {
    }

    /**
     * $this->info("  Previously registered keys for type '$type':");
     * foreach ($this->map[$type] as $k => $v) {
     * $this->info("    - " . $k . " => " . $v);
     * }
     */

    public function __toString()
    {
        return "Referenced key '{$this->key}' in type '{$this->type}' does not exist.";
    }
}
