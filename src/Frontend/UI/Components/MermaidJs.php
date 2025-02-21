<?php

namespace Swark\Frontend\UI\Components;

use Illuminate\View\Component;

class MermaidJs extends Component
{

    public function render()
    {
        return function (array $data) {
            $content = trim($data['slot']);
            $hash = sha1($content);
            $id = $hash;

            return view('swark::components.mermaidjs', [
                'id' => $id,
                'code' => preg_replace('/^ +/m', '', $content)
            ]);
        };
    }
}
