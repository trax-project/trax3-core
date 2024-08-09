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

    'xapi_agents' => [

        'mappings' => [
            'id' => ['type' => 'keyword'],
            'sid_field_1' => ['type' => 'keyword'],
            'sid_field_2' => ['type' => 'keyword'],
            'sid_type' => ['type' => 'keyword'],
            'name' => ['type' => 'text'],
            'is_group' => ['type' => 'boolean'],
            'pseudonymized' => ['type' => 'boolean'],
            'person_id' => ['type' => 'keyword'],
            'store' => ['type' => 'keyword'],
            'stored' => ['type' => 'date_nanos'],
            'updated' => ['type' => 'date_nanos'],
        ],
    ],
];
