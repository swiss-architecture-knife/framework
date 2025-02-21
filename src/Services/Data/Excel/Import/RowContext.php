<?php

namespace Swark\Services\Data\Excel\Import;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Row;


class RowContext implements \ArrayAccess
{
    public function __construct(
        public readonly string $sheetName,
        public readonly int    $rowNumber,
        public readonly Row    $row,
        public readonly array  $mapping,
        public readonly array  $columns
    )
    {
    }

    public function offsetSet($offset, $value): void
    {
        throw new \Exception("You can not change the column content");
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->columns[$offset]);
    }

    public function offsetUnset($offset): void
    {
        throw new \Exception("You can not change the column content");
    }

    public function offsetGet(mixed $offset): mixed
    {
        $useOffset = $offset;

        if (is_string($offset)) {
            $useOffset = $this->mapping[$offset] ?? throw new \Exception("Trying to access offset {$offset} which is not defined");
        }

        return isset($this->columns[$useOffset]) ? $this->columns[$useOffset] : null;
    }

    public function explode(string $explode, string|int $columnIndex): array
    {
        $value = $this->nonEmpty($columnIndex);

        return explode($explode, $value);
    }

    public function nonEmpty(string|int $columnIndex): mixed
    {
        $value = $this[$columnIndex];

        if (empty($value)) {
            $this->fail("Invalid empty string at " . $this->ref($columnIndex));
        }

        return $value;
    }

    public function dateOrNull(string|int $columnIndex): ?Carbon
    {
        return $this->ifPresent($columnIndex, fn($value) => Carbon::parse($value));
    }

    public function ifPresent(string|int $columnIndex, callable $then): mixed
    {
        if (!empty($value = $this[$columnIndex])) {
            return $then($value);
        }

        return null;
    }

    private function ref(string|int $index): string
    {
        $colName = is_int($index) ? $index + 1 : $index;

        return "[Sheet:'" . $this->sheetName . "',row:" . $this->rowNumber . ",col:" . ($colName) . "]";
    }

    public function toJson(string|int $columnIndex, bool $mayNullable = false): mixed
    {
        return static::convertFromJsonStringToObject($this[$columnIndex], $mayNullable);
    }

    public static function convertFromJsonStringToObject(?string $data, bool $mayNullable = false): mixed
    {
        $data = trim($data ?? '');
        if (empty($data)) {
            if ($mayNullable) {
                return null;
            }

            throw new \Exception("Invalid JSON data. You have to set it to nullable");
        }

        $r = json_decode($data, true);
        if (json_last_error()) {
            throw new \Exception("Invalid JSON data provided: '" . $data . "', " . json_last_error_msg());
        }

        return $r;
    }

    private function fail(string $message)
    {
        throw new \Exception($message);
    }
}
