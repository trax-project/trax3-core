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

    'xapi_activities' => [

        'mappings' => [
            'id' => ['type' => 'keyword'],
            'iri' => ['type' => 'keyword'],
            'type_id' => ['type' => 'keyword'],
            'is_category' => ['type' => 'boolean'],
            'is_profile' => ['type' => 'boolean'],
            'store' => ['type' => 'keyword'],
            'stored' => ['type' => 'date_nanos'],
            'updated' => ['type' => 'date_nanos'],
        ],
    ],
];
