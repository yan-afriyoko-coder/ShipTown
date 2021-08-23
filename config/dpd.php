<?php

return [
    'live'               => env('DPD_LIVE', false),

    // live credentials
    'token'              => env('DPD_TOKEN', ''),
    'user'               => env('DPD_USER', ''),
    'password'           => env('DPD_PASSWORD', ''),

    // testing credentials
    'test_token'         => env('TEST_DPD_TOKEN', ''),
    'test_user'          => env('TEST_DPD_USER', ''),
    'test_password'      => env('TEST_DPD_PASSWORD', ''),

    'collection_address' => [
        'contact'           => env('DPD_COLLECTION_CONTACT', ''),
        'contact_telephone' => env('DPD_COLLECTION_CONTACT_TELEPHONE', ''),
        'contact_email'     => env('DPD_COLLECTION_CONTACT_EMAIL', ''),
        'business_name'     => env('DPD_COLLECTION_BUSINESS_NAME', ''),
        'address_line1'     => env('DPD_COLLECTION_ADDRESS_LINE_1', ''),
        'address_line2'     => env('DPD_COLLECTION_ADDRESS_LINE_2', ''),
        'address_line3'     => env('DPD_COLLECTION_ADDRESS_LINE_3', ''),
        'address_line4'     => env('DPD_COLLECTION_ADDRESS_LINE_4', ''),
        'country_code'      => env('DPD_COLLECTION_COUNTRY_CODE', ''),
    ],
];
