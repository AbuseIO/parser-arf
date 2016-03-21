<?php

return [
    'parser' => [
        'name'          => 'Arf',
        'enabled'       => true,
        'sender_map'    => [
            '/nobody@woody.ch/',
            '/@mailpit.powerweb.de/',
            '/@r.iecc.com/',
            '/@junkemailfilter.com/',
            '/abuse-auto@support.(juno|netzero).com/',
            '/@USGOabuse.net/',
        ],
        'body_map'      => [
            //
        ],
        // The aliases convert the body_map address into a more friendly source name
        'aliases'       => [
            '/nobody@woody.ch/'                             => 'Woody',
            '/@mailpit.powerweb.de/'                        => 'DNSBLDE',
            '/@r.iecc.com/'                                 => 'IECCCOM',
            '/@junkemailfilter.com/'                        => 'JunkEmailFilter',
            '/abuse-auto@support.(juno|netzero).com/'       => 'UOL',
            '/@USGOabuse.net/'                              => 'USGO',
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
