<?php
return [
    'nav' => [
        'top' => [
            'business' => 'Business-Administration',
            'admin' => 'IT-Administration',
            'compliance' => 'Compliance',
        ],
        'side' => [
            'strategy' => [
                'title' => 'Strategie',
                'overview' => 'Übersicht',
                'findings' => 'Findings',
                'kpi' => 'Zielerreichung'
            ],
            'policies' => 'Richtlinien',
            'it_architecture' => [
                'title' => 'IT-Architektur',
            ],
            'infrastructure' => [
                'title' => 'Infrastruktur',
                'overview' => 'Systemlandschaft',
                'baremetal' => 'Baremetals',
                'cluster' => 'Cluster',
                'resources' => 'Ressourcen',
                'instances' => 'Instanzen',
            ],
            'software' => [
                'title' => 'Software',
                'catalog' => 'Katalog',
                'instances' => 'Instanzen',
            ],
            'glossary' => [
                'title' => 'Glossar',
            ],
            'sandbox' => [
                'title' => 'swark Sandbox',
            ]
        ]
    ],
    'toc' => [
        'on_this_page' => 'Auf dieser Seite',
        'overview' => 'Übersicht',
    ],
    'status' => [
        'last_update' => 'Letzte Aktualisierung :date'
    ],
    'company' => [
        'introduction' => [
            'title' => 'Einleitung',
            'none' => 'Keine Einleitung hinterlegt.',
        ],
    ],
    'strategy' => [
        'title' => 'Strategie',
        'introduction' => [
            'none' => 'Keine Einleitung hinterlegt.',
        ],
        'reason' => 'Begründung',
        'findings' => 'Findings',
        'actions' => 'Maßnahmen',
        'vision' => [
            'title' => 'Vision',
            'none' => 'Keine Vision hinterlegt.',
        ],
        'big_picture' => [
            'title' => 'Zielbild',
        ],
        'none' => 'Keine Strategie vorhanden.',
    ],
    'findings' => [
        'title' => 'Findings',
        'overview' => 'Übersicht über die Findings',
        'none' => 'Keine Findings vorhanden',
        'actions_detail' => 'Findings im Detail',
        'timeline' => 'Planung',
        'table' => [
            'cols' => [
                'action' => ['title' => 'Action'],
                'status' => ['title' => 'Status'],
                'begin_at' => ['title' => 'Beginn am'],
                'end_at' => ['title' => 'Ende am'],
            ],
        ],
    ],
    'policies' => [
        'intro' => 'Richtlinien',
        'none' => 'Keine Richtlinien hinterlegt.',
    ],
    'kpi' => [
        'title' => 'Zielerreichung',
        'table' => [
            'objective' => 'Ziel / Maßnahme',
            'target' => 'Zielwert',
            'current' => 'Aktueller Wert',
        ]
    ],
    'table' => [
        'empty' => 'Keine Elemente vorhanden',
    ],
    'it_architecture' => [
        'title' => 'IT-Architektur',
        'lead' => 'Übersicht über die allgemeine IT-Architektur',
        'data_classification' => [
            'title' => 'Datenklassifizierung',
            'table' => [
                'cols' => [
                    'class' => [
                        'title' => 'Klasse',
                    ],
                    'description' => [
                        'title' => 'Beschreibung',
                    ],
                ],
            ],
        ],
        'zone_model' => [
            'title' => 'Zonenmodell',
            'table' => [
                'cols' => [
                    'zone' => [
                        'title' => 'Zone'
                    ],
                    'description' => [
                        'title' => 'Beschreibung'
                    ],
                    'classification' => [
                        'title' => 'Datenklassifizierung'
                    ],
                    'actors' => [
                        'title' => 'Zugriff durch'
                    ],
                    'rules' => [
                        'title' => 'Richtlinie',
                    ]
                ]
            ]
        ],
        'matrix' => [
            'title' => 'Zugriffsmatrix',
            'hint' => html('Standardmäßig werden - falls nicht explizit angegeben - alle Verbindungen in die jeweilige Zone <strong>verweigert</strong>.'),
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
            'title' => 'Systemlandschaft',
            'lead' => 'Big Picture',
        ],
        'baremetal' => [
            'title' => 'Baremetal-Systeme',
            'overview' => 'Übersicht',
            'summary' => 'Zusammenfassung',
            'table' => [
                'cols' => [
                    'region' => [
                        'title' => 'MSP / Region / AZ'
                    ],
                    'name' => [
                        'title' => 'Baremetal-Name'
                    ],
                    'virtualizer' => [
                        'title' => 'Virtualisierer'
                    ],
                    'operating_system' => [
                        'title' => 'Betriebssystem'
                    ],
                    'hostname' => [
                        'title' => 'Hostname'
                    ],
                ]
            ]
        ]
    ],
    'alert' => [
        'no_content' => 'No content has been provided for this page.'
    ]
];
