<?php

namespace Swark\Frontend\UI\Components;

use Illuminate\View\Component;

class Criticality extends Component
{
    public function __construct(
        public string $name,
        public int    $position,
        public string $prefix = '',
        public ?array $range = [],
    )
    {
    }

    public function render()
    {
        $r = [
            '1' => 'info',
            '2' => 'warning',
            '3' => 'danger',
            '4' => 'danger'
        ];

        $class = $r[$this->position] ?? 'info';

        return <<<HTML
<span class="badge bg-$class">{$this->prefix}{$this->name}</span>
HTML;
    }
}
