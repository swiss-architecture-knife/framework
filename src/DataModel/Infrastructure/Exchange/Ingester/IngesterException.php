<?php
declare(strict_types=1);

namespace Swark\DataModel\Infrastructure\Exchange\Ingester;

class IngesterException extends \Exception
{
    public function __construct(public readonly \Swark\DataModel\Infrastructure\Exchange\Ingester\StatusFlag $modelStatusFlag, string $message = "")
    {
        parent::__construct($message, null);
    }
}
