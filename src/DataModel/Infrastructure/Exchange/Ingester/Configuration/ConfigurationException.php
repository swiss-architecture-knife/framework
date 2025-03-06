<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester\Configuration;

use Swark\DataModel\Infrastructure\Exchange\Ingester\IngesterException;

class ConfigurationException extends IngesterException
{
    public function __construct(\Swark\DataModel\Infrastructure\Exchange\Ingester\StatusFlag $modelStatusFlag, string $message = "", public readonly string $source = '*')
    {
        parent::__construct($modelStatusFlag, $message);
    }
}
