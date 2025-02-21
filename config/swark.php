<?php
return [
    'routing' => [
        'name' => 'swark.',
        'prefix' => '',
    ],
    'content' => [
        'path' => storage_path('app/swark/_default')
    ],
    'events' => [
        'hookable' => [
            'chapter:before' => 'before-chapter',
            'chapter-body:before' => 'before',
            'chapter-body:after' => 'after',
        ]
    ]
];
