<?php

namespace Swark\DataModel\Kernel\Infrastructure\Repository;

/**
 * Map a row to different cubes
 */
class MapToGroup
{
    private array $mappings = [];

    public function property(string $key, string $selector, ?string $keep = null, ?callable $mapper = null): MapToGroup
    {
        $this->mappings[$key] = ['selector' => $selector, 'columns' => null, 'mapper' => null, 'keep' => $keep];

        return $this;
    }

    private function extract(array $data, string $key, array $meta): array
    {
        if ($meta['columns'] === null) {
            $meta['columns'] = [];
            $meta['properties'] = [];

            foreach ($data as $columnName => $value) {
                if (preg_match('/' . $meta['selector'] . '/', $columnName, $result)) {
                    $meta['columns'][$columnName] = $result[1];
                    $meta['properties'][$result[1]] = $columnName;
                }

                if ($meta['keep']) {
                    if (preg_match('/' . $meta['keep'] . '/', $columnName, $result)) {
                        $meta['columns'][$columnName] = $columnName;
                        $meta['properties'][$columnName] = $columnName;
                    }
                }
            }

            $this->mappings[$key] = $meta;
        }


        $r = [];

        // If property 'id' has not been set, we assume that this object is empty
        if (empty($data[$meta['properties']['id']])) {
            $r = null;
        } else {
            foreach ($meta['columns'] as $columnName => $targetProperty) {
                $r[$targetProperty] = $data[$columnName];
            }
        }

        return [$key => $r];
    }

    /**
     * Map given plain data to cubes
     * @param array $data
     * @return array
     */
    public function map(array $data): array
    {
        $r = [];

        foreach ($this->mappings as $key => $meta) {
            $r = array_merge($r, $this->extract($data, $key, $meta));
        }

        return $r;
    }
}
