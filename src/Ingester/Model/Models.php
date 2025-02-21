<?php
declare(strict_types=1);

namespace Swark\Ingester\Model;

use IteratorAggregate;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;
use Swark\Ingester\IngesterException;
use Swark\Ingester\UnitOfWork;
use Traversable;

class Models implements IteratorAggregate
{
    private array $modelTypes = [];
    private array $modelTypesByAlias = [];

    public function add(Context $context): Models
    {
        $this->modelTypes[] = $context;
        $this->modelTypesByAlias[$context->alias] = $context;

        return $this;
    }

    /**
     * Resolve dependencies and check if circular dependencies occurred
     * @return void
     * @throws IngesterException
     */
    public function resolveDependencies()
    {
        $sorter = new StringSort();

        /** @var Context $modelDefinition */
        foreach ($this->modelTypes as $modelType) {
            $knownModelDefinitions[$modelType->alias] = $modelType;
            $dependencies = !$modelType->options() ? [] : $modelType->options()->dependsOn;

            $sorter->add($modelType->alias, $dependencies);
        }

        try {
            $sortedModels = $sorter->sort();
            $newModelTypes = [];

            foreach ($sortedModels as $alias) {
                $newModelTypes[] = $this->modelTypesByAlias[$alias];
            }

            $this->modelTypes = $newModelTypes;

        } catch (ElementNotFoundException $e) {
            throw new IngesterException(StatusFlag::INVALID_MAPPED_MODEL, "Model alias '" . $e->getSource() . "' points to invalid alias '" . $e->getTarget() . "'");
        } catch (CircularDependencyException $e) {
            throw new IngesterException(StatusFlag::INVALID_MAPPED_MODEL, "Circular dependency between aliases " . implode(" -> ", $e->getNodes()));
        }
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->modelTypes);
    }

    public function unitOfWork(): UnitOfWork
    {
        return new UnitOfWork($this);
    }
}
