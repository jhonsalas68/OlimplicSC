<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Aquí se configura el acceso a la nube de Cloudinary.
    |
    */

    'cloud_url' => env('CLOUDINARY_URL'),

    /**
     * El error "Undefined array key 'cloud'" suele suceder cuando 
     * el SDK busca esta estructura:
     */
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

];
