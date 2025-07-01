<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default CSS Framework
    |--------------------------------------------------------------------------
    |
    | This option controls the default CSS framework that will be used by the
    | package when rendering form components
    |
    | Supported: "bootstrap-5", "material-admin-26"
    |
    */

    'framework' => 'bootstrap-5',

    'use_eloquent_date_casting' => false,

    /*
    |--------------------------------------------------------------------------
    | Framework Settings
    |--------------------------------------------------------------------------
    |
    | Framework specific configs
    |
    */

    'frameworks' => [
        'bootstrap-5' => [
            'icon-prefix' => 'fa-regular',
            'date-icon' => 'fa-calendar',
            'date-icon-wrapper-class' => 'date-icon-wrapper',
            'datetime-icon' => 'fa-calendar',
            'time-icon' => 'fa-clock',
            'date-clear-icon' => 'fa-close',
            'date-clear-btn-class' => 'btn btn-outline-secondary btn-date-clear disable-w-input',
            'file-download-icon' => 'fa-arrow-to-bottom',
            'file-upload-icon' => 'fa-arrow-to-top',
            'file-clear-icon' => 'fa-close',
            'image-icon' => 'fa-image',
            'inline-label-class' => 'col-sm-3 col-lg-2 col-form-label',
            'inline-input-class' => 'col-sm-9 col-lg-10',
            'inline-entry-label-class' => 'col-sm-6 col-md-4',
            'inline-entry-class' => 'col-sm-6 col-md-8',
            'search-icon' => 'fa-search',
            'no-items-icon' => 'fa-file',
        ],

        'material-admin-26' => [
            'icon-prefix' => 'zmdi',
            'date-icon' => 'zmdi-calendar',
            'date-icon-wrapper-class' => 'date-icon-wrapper',
            'datetime-icon' => 'zmdi-calendar',
            'time-icon' => 'zmdi-clock',
            'date-clear-icon' => 'zmdi-close',
            'date-clear-btn-class' => 'text-body btn-date-clear disable-w-input',
            'file-download-icon' => 'zmdi-open-in-new',
            'file-upload-icon' => 'zmdi-upload',
            'file-clear-icon' => 'zmdi-close',
            'image-icon' => 'zmdi-image',
            'inline-label-class' => 'col-sm-3 col-lg-2 col-form-label',
            'inline-input-class' => 'col-sm-9 col-lg-10',
            'inline-entry-label-class' => 'col-sm-6 col-md-4',
            'inline-entry-class' => 'col-sm-6 col-md-8',
            'search-icon' => 'zmdi-search',
            'no-items-icon' => 'zmdi-file',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Scripts Stack
    |--------------------------------------------------------------------------
    |
    | The name of the stack to push scripts
    |
    */

    'scripts_stack' => 'scripts',

    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key
    |--------------------------------------------------------------------------
    |
    | API key to use for map inputs
    |
    */

    'map_api_key' => env('MAP_API_KEY'),
];
