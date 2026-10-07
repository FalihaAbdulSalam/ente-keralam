<?php

return [
    'recipient_csv_path' => storage_path('app/campaign/iffk.csv'),
    'sources' => [
        'iffk' => storage_path('app/campaign/iffk.csv'),
        'mizhivu' => storage_path('app/campaign/mizhivu.csv'),
    ],
    'default_source' => 'iffk',
    'mail_content_path' => storage_path('app/campaign/mail-content.txt'),
    'logo_public_path' => 'https://entekeralam.kerala.gov.in/img/ente-keralam.png',
    'button' => [
        'enabled' => true,
        'label' => 'Participate Now',
        'url' => 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest',
    ],
    'allowed_categories' => ['DELEGATE'],
    'email_validation' => ['rfc', 'strict', 'dns', 'spoof'],
    'send_spacing_seconds' => 2,
];
