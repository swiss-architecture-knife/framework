<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use TorMorten\Eventy\Facades\Eventy;

if (!defined('SWARK_PREFIX')) {
    define('SWARK_PREFIX', 'swark');
}

if (!function_exists('swark_resource')) {
    function swark_resource(string $resourcePath)
    {
        return SWARK_PREFIX . '::' . $resourcePath;
    }
}

if (!function_exists('swark_view')) {
    function swark_view($view, ...$args)
    {
        return view(swark_resource($view), ... $args);
    }

    function swark_view_auto(array $args = [], string $view = '~')
    {
        View::share($args);
        return view(swark_resource($view));
    }
}

if (!function_exists('content')) {
    /**
     * @param $path
     * @param $data
     * @param $mergeData
     * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function content($path = null, array $data = [], array $mergeData = []): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $factory = app('content.view-factory');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($path, $data, $mergeData);
    }
}

if (!function_exists('content_exists')) {
    function content_exists($path): bool {
        return app('content.view-factory')->exists($path);
    }
}

if (!function_exists('map_to_named_items')) {
    function map_to_named_items($items, string $nameProperty = 'name', string $idProperty = 'id')
    {
        return collect($items)->map(fn($item) => [$item->$idProperty, $item->$nameProperty])->values()->toArray();
    }
}

if (!function_exists('html')) {
    /**
     * Creates an array with HtmlString as first element. This can be used to carry HTML content inside language files.
     * This is required to make Translator::getLine() work and return the key
     * @param $string
     * @return \Illuminate\Support\HtmlString[]
     */
    function html($string): array
    {
        return [new \Illuminate\Support\HtmlString($string)];
    }
}

if (!function_exists('_t')) {
    function _t($key, ...$args): \Illuminate\Support\HtmlString|string
    {
        $f = __($key, ... $args);

        if (is_array($f) && sizeof($f) > 0 && ($f[0] instanceof \Illuminate\Support\HtmlString)) {
            return $f[0];
        }

        return $f;
    }
}

if (!function_exists('enable_sql_full_mode')) {
    /** Sets sql_mode=only_full_group_by */
    function enable_sql_full_mode()
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    }
}

if (!function_exists('notify')) {
    function notify(?callable $callable, ...$args)
    {
        if ($callable) {
            $callable(... $args);
        }
    }
}

if (!function_exists('yo')) {

    /**
     * Simple event dispatcher without the Laravel hazzle. It uses Eventy to dispatch further actions, e.g. add logging infrastructure.
     *
     * @param string $level
     * @param string $message
     * @param array $args
     * @param string|null $tag
     * @param mixed|null $returnValue
     * @return mixed
     */
    function yo(string $level, string $message, array $args = [], ?string $tag = null, mixed $returnValue = null)
    {
        $tagPrefix = 'yo.swark';
        $fullTag = empty($tag) ? $tagPrefix : $tagPrefix . '.' . $tag;

        $parts = explode('.', $fullTag);
        $max = sizeof($parts);

        $newParts = [];

        foreach ($parts as $idx => $part) {
            $newParts[] = $part;

            $tag = implode(".", $newParts);

            if (($idx + 1) != $max) {
                $tag .= '.*';
            }

            Eventy::action($tag, $max, $level, sprintf($message, ...$args), $message, $args);
        }

        return $returnValue;
    }

    function yo_info(string $message, array $args = [], ?string $tag = null, mixed $returnValue = null)
    {
        return yo('info', $message, $args, $tag, $returnValue);
    }

    function yo_warn(string $message, array $args = [], ?string $tag = null, mixed $returnValue = null)
    {
        return yo('warn', $message, $args, $tag, $returnValue);
    }

    function yo_error(string $message, array $args = [], ?string $tag = null, mixed $returnValue = null)
    {
        return yo('error', $message, $args, $tag, $returnValue);
    }

    function yo_debug(string $message, array $args = [], ?string $tag = null, mixed $returnValue = null)
    {
        return yo('debug', $message, $args, $tag, $returnValue);
    }
}
