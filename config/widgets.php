<?php

return [
    'types' => [
        'balance' => [
            'properties' => []
        ],

        'balance_global' => [
            'properties' => []
        ],

        'spent' => [
            'properties' => [
                'period' => [
                    'calendar.today' => 'today',
                    'calendar.this_week' => 'this_week',
                    'calendar.this_month' => 'this_month'
                ]
            ]
        ],

        // Any other widgets go here
    ]
];
