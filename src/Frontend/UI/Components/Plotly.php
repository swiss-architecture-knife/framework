<?php

namespace Swark\Frontend\UI\Components;

use Illuminate\View\Component;

class Plotly extends Component
{

    public function render()
    {
        return function (array $data) {
            $width = (int)($data['attributes']['width'] ?? 0);
            $height =(int)($data['attributes']['height'] ?? 0);

            $config = $data['config'] ?? '{}';
            $content = $data['slot'];
            $options = $data['options'] ?? '{}';
            $id = sha1($content);

            $style = '';
            if ($width && $height) {
                $style = 'style="width:' . $width . 'px;height:' . $height .'px;"';
            }

            return <<<HTML
<div id="{$id}" $style></div>
<script>
    plotly_{$id} = document.getElementById('{$id}');
    Plotly.newPlot( plotly_{$id}, {$content}, {$options}, {$config});
</script>
HTML;
        };
    }
}
