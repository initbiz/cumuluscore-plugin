<?php

return [
    'encryption' => [

        /*
        |--------------------------------------------------------------------------
        | Cluster keys directory
        |--------------------------------------------------------------------------
        |
        | Directory that's going to store cluster keys
        |
        */
        'keys_dir' => 'cumulus/keys',

        /*
        |--------------------------------------------------------------------------
        | Cipher
        |--------------------------------------------------------------------------
        |
        | Cipher that's going to be used to encrypt data and generate keys
        |
        */
        'cipher' => 'AES-256-CBC',

        /*
        |--------------------------------------------------------------------------
        | Cluster keys file path
        |--------------------------------------------------------------------------
        |
        | Relative path to the storage - left here only for backward compatibility
        |
        */
        'keys_file_path' => 'cluster_keys',
    ]
];
