<?php

return [
    'parser' => [
        'name'          => 'Arf',
        'enabled'       => true,
        'sender_map'    => [
            '/nobody@woody.ch/',
        ],
        'body_map'      => [
            //
        ],
        // The aliases convert the body_map address into a more friendly source name
        'aliases'       => [
            '/nobody@woody.ch/'                             => 'Woody',
        ]
    ],

    'feeds' => [
        'default' => [
            'class'     => 'SPAM',
            'type'      => 'ABUSE',
            'enabled'   => true,
            'fields'    => [
                'Source-IP',
                'Feedback-Type',
                'Received-Date',
            ],
        ],

    ],
];
