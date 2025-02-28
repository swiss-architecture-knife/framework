<?php
return [
    'nav' => [
        'top' => [
            'start' => 'Start',
            'admin' => 'Administration',
        ],
        'side' => [
            'strategy' => [
                'title' => 'Strategy',
                'overview' => 'Overview',
                'findings' => 'Findings',
                'kpi' => 'KPI'
            ],
            'policies' => 'Policies',
            'it_architecture' => [
                'title' => 'IT architecture',
            ],
            'infrastructure' => [
                'title' => 'Infrastructure',
                'overview' => 'System landscape',
                'baremetal' => 'Baremetals',
                'cluster' => 'Cluster',
                'resources' => 'Resources',
                'instances' => 'Instances',
            ],
            'software' => [
                'title' => 'Software',
                'catalog' => 'Catalog',
                'instances' => 'Instances',
            ],
            'glossary' => [
                'title' => 'Glossary',
            ],
            'sandbox' => [
                'title' => 'swark Sandbox',
            ]
        ]
    ],
    'toc' => [
        'on_this_page' => 'On this page',
        'overview' => 'Overview',
    ],
    'status' => [
        'last_update' => 'Last update :date'
    ],
    'company' => [
        'introduction' => [
            'title' => 'Introduction',
            'none' => 'No introduction defined.',
        ],
    ],
    'strategy' => [
        'title' => 'Strategy',
        'introduction' => [
            'none' => 'No strategy defined.',
        ],
        'reason' => 'Reason',
        'findings' => 'Findings',
        'actions' => 'Actions',
        'vision' => [
            'title' => 'Vision',
            'none' => 'No vision defined.',
        ],
        'big_picture' => [
            'title' => 'Big picture',
        ],
        'none' => 'No strategy defined  .',
    ],
    'findings' => [
        'title' => 'Findings',
        'overview' => 'Overview of findings',
        'none' => 'No findings available',
        'actions_detail' => 'Findings in detail',
        'timeline' => 'Planning',
        'table' => [
            'cols' => [
                'action' => ['title' => 'Action'],
                'status' => ['title' => 'Status'],
                'begin_at' => ['title' => 'Start at'],
                'end_at' => ['title' => 'End at'],
            ],
        ],
    ],
    'policies' => [
        'intro' => 'Policies',
        'none' => 'No policies defined.',
    ],
    'kpi' => [
        'title' => 'KPI',
        'table' => [
            'objective' => 'Goal / Objective',
            'target' => 'Target value',
            'current' => 'Current value',
        ]
    ],
    'table' => [
        'empty' => 'No elements available',
    ],
    'it_architecture' => [
        'title' => 'IT architecture',
        'lead' => 'Overview of general IT architecture',
        'data_classification' => [
            'title' => 'Data classification',
            'table' => [
                'cols' => [
                    'class' => [
                        'title' => 'Class',
                    ],
                    'description' => [
                        'title' => 'Description',
                    ],
                ],
            ],
        ],
        'zone_model' => [
            'title' => 'Zone model',
            'table' => [
                'cols' => [
                    'zone' => [
                        'title' => 'Zone'
                    ],
                    'description' => [
                        'title' => 'Description'
                    ],
                    'classification' => [
                        'title' => 'Data classification'
                    ],
                    'actors' => [
                        'title' => 'Access by'
                    ],
                    'rules' => [
                        'title' => 'Rule',
                    ]
                ]
            ]
        ],
        'matrix' => [
            'title' => 'Access matrix',
            'hint' => html('If not explicitly defined, all connections in the target zone will be <strong>denied</strong>.'),
            'table' => [
                'cols' => [
                    'map' => [
                        'title' => html('Target ➔<br/>Source ↓'),
                    ],
                ],
            ],
        ],
    ],
    'infrastructure' => [
        'landscape' => [
            'title' => 'System landscape',
            'lead' => 'Big Picture',
        ],
        'baremetal' => [
            'title' => 'Baremetal systems',
            'overview' => 'Overview',
            'summary' => 'Summary',
            'table' => [
                'cols' => [
                    'region' => [
                        'title' => 'MSP / Region / AZ'
                    ],
                    'name' => [
                        'title' => 'Baremetal name'
                    ],
                    'virtualizer' => [
                        'title' => 'Virtualizer'
                    ],
                    'operating_system' => [
                        'title' => 'Operating system'
                    ],
                    'hostname' => [
                        'title' => 'Host name'
                    ],
                ]
            ]
        ]
    ],
    'alert' => [
        'no_content' => 'No content has been provided for this page.'
    ]
];
