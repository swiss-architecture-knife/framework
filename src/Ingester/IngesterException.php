<?php
declare(strict_types=1);

namespace Swark\Ingester;

class IngesterException extends \Exception
{
    public function __construct(public readonly \Swark\Ingester\Model\StatusFlag $modelStatusFlag, string $message = "")
    {
        parent::__construct($message, null);
    }
}
