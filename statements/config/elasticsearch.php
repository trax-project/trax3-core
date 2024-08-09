<?php

return [

    /**
     * This file describes the mappings to be used by Elasticsearch.
     *
     * To customize this file, make a copy in the 'custom/elasticsearch' folder with the command:
     *
     * > php artisan vendor:publish --tag=elasticsearch-config
     *
     * Be aware that changing the mappings may break some requests performed by the Elasticsearch repositories.
     */

    'xapi_statements' => [

        'mappings' => [
            'id' => ['type' => 'keyword'],
            'voided' => ['type' => 'boolean'],
            'voiding' => ['type' => 'boolean'],
            'validated' => ['type' => 'boolean'],
            'valid' => ['type' => 'boolean'],
            'pseudonymized' => ['type' => 'boolean'],
            'actor_id' => ['type' => 'keyword'],
            'verb_id' => ['type' => 'keyword'],
            'object_id' => ['type' => 'keyword'],
            'type_id' => ['type' => 'keyword'],
            'registration' => ['type' => 'keyword'],
            'statement_ref' => ['type' => 'keyword'],
            'store' => ['type' => 'keyword'],
            'client' => ['type' => 'keyword'],
            'timestamp' => ['type' => 'date_nanos'],
            'stored' => ['type' => 'date_nanos'],
        ],
    ],

    'xapi_attachments' => [

        'mappings' => [
            'id' => ['type' => 'keyword'],
            'content_type' => ['type' => 'keyword'],
            'length' => ['type' => 'integer'],
            'store' => ['type' => 'keyword'],
            'client' => ['type' => 'keyword'],
            'stored' => ['type' => 'date_nanos'],
        ],
    ],
];
