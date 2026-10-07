<?php
return [

    App\Models\Article::class => [
        'entitle',
        'maltitle',

        'endescription',
        'maldescription',

        'encontent',
        'malcontent',

        // RELATED MODEL FIELDS
        'type:entitle',          // search through ArticleType.entitle
        'type:maltitle',         // optional Malayalam
        'sector:entitle',        // search SectorDetails.entitle
    ],

    App\Models\ArticleType::class => ['entitle', 'maltitle'],
    App\Models\SectorDetail::class => ['entitle'],

    // Other models
    App\Models\User::class => ['name', 'email'],
    App\Models\Faq::class => ['enquestion', 'enanswer'],
];
