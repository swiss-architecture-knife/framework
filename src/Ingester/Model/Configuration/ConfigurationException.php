<?php
declare(strict_types=1);

namespace Swark\Ingester\Model\Configuration;

use Swark\Ingester\IngesterException;

class ConfigurationException extends IngesterException
{
    public function __construct(\Swark\Ingester\Model\StatusFlag $modelStatusFlag, string $message = "", public readonly string $source = '*')
    {
        parent::__construct($modelStatusFlag, $message);
    }
}
