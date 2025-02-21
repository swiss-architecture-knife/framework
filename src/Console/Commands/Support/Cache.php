<?php

namespace Swark\Console\Commands\Support;

class Cache
{
    private ?array $cache = null;
    private mixed $callback = null;

    private mixed $initializeCallback = null;

    public function get(string $key, array $args = []): mixed
    {
        $this->ensureInitialized();

        if (!array_key_exists($key, $this->cache)) {
            throw_if(!$this->callback, "No callback handler defined for resolving cache key");

            $value = ($this->callback)($key, $args);

            throw_if($value === null, "Callback handler returned null for key '$key'");

            $this->cache[$key] = $value;

        }

        return $this->cache[$key];
    }

    public function all(): array
    {
        return $this->cache;
    }

    private function ensureInitialized()
    {
        if ($this->cache !== null) {
            return;
        }

        $r = [];

        if ($this->initializeCallback) {
            $r = ($this->initializeCallback)();
        }

        $this->cache = $r;
    }

    public function initialize(callable $callback): Cache
    {
        $this->initializeCallback = $callback;
        return $this;
    }

    public function onMiss(callable $callback): Cache
    {
        $this->callback = $callback;
        return $this;
    }
}
