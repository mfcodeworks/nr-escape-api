<?php

return [

    // These CSS rules will be applied after the regular template CSS

    /*
        'css' => [
            '.button-content .button { background: red }',
        ],
    */

    'colors' => [

        'highlight' => '#004ca3',
        'button'    => '#004cad',

    ],

    'view' => [
        'senderName'  => env('APP_NAME', 'Escape'),
        'reminder'    => 'This email was automatically generated by a robot at '.env('APP_NAME', 'Escape').' that wishes you a lovely day.',
        'unsubscribe' => null,
        'address'     => 'Your Escape',

        'logo'        => [
            'path'   => env('APP_URL').'/img/logo.png',
            'width'  => '130px',
            'height' => 'auto',
        ],

        'twitter'  => 'nygmarose',
        'facebook' => 'nygmarosebeauty',
        'flickr'   => null,
    ],

];
