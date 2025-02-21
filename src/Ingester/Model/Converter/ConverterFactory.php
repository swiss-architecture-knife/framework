<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Converter;

use Swark\Ingester\IngesterException;
use Swark\Ingester\Model\StatusFlag;

class ConverterFactory
{
    private array $registry = [];

    public function locate(string $aliasOrClass, array $options = []): ?Converter
    {
        // this is an alias, register that alias
        if (isset($options['class'])) {
            return $this->aliasForClassName(alias: $aliasOrClass, className: $options['class'], options: $options);
        }

        if (class_exists($aliasOrClass)) {
            return $this->createFromClass($aliasOrClass, $options);
        }

        return $this->fromAlias($aliasOrClass);
    }

    public function fromAlias(string $alias): ?Converter
    {
        if (!isset($this->registry[$alias])) {
            throw new IngesterException(StatusFlag::INVALID_CONVERTER, "Referenced converter alias '" . $alias . "' does not exist");
        }

        return $this->registry[$alias];
    }

    private function aliasForClassName(string $alias, string $className, array $options)
    {
        if (!isset($this->registry[$alias])) {
            $this->registry[$alias] = static::createFromClass($className, $options);
        }

        return $this->registry[$alias];
    }

    public function createFromClass($className, array $options = []): ?Converter
    {
        /** @var ?Converter $r */
        $r = null;

        if (class_exists($className)) {
            $r = new $className($options);

            if (!($r instanceof Converter)) {
                throw new IngesterException(StatusFlag::INVALID_CONVERTER, "Converter with name $className does not implement required Converter interface");
            }
        }

        return $r;
    }
}
