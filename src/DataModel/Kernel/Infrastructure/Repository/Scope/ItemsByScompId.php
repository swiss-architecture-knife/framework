<?php
declare(strict_types=1);

namespace Swark\DataModel\Kernel\Infrastructure\Repository\Scope;

use Swark\DataModel\Enterprise\Domain\Entity\Zone;

class ItemsByScompId implements Scoping
{
    public function __construct(public readonly ?array $instanceParameters = null, public readonly ?array $defaultQueryOptions = null, public readonly ?array $customQueryOptions = null)
    {
    }

    private ?array $mergedOptions = null;

    private function mergedOptions(): array
    {
        if (!$this->mergedOptions) {
            $this->mergedOptions = array_merge(
                (array)($this->defaultQueryOptions ?? new \stdClass()),
                (array)($this->customQueryOptions ?? new \stdClass()),
            );
        }
        return $this->mergedOptions;
    }

    public function schema(): string
    {
        // TODO
        return '';
    }

    public function query(): ScopedQuery
    {
        if ($this->instanceParameters['type'] == 'logical_zone') {
            // TODO Depends on concrete model
            return ScopedQuery::of('logical_zone', Zone::whereIn('scomp_id', $this->mergedOptions()['scomp_ids']));
        }

        throw new \Exception("Not implemented yet");
    }
}
