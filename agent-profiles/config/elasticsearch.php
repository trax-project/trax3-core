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

    'xapi_agent_profiles' => [

        'mappings' => [
            'id' => ['type' => 'keyword'],
            'agent_id' => ['type' => 'keyword'],
            'profile_id' => ['type' => 'keyword'],
            'content_type' => ['type' => 'keyword'],
            'store' => ['type' => 'keyword'],
            'stored' => ['type' => 'date_nanos'],
            'updated' => ['type' => 'date_nanos'],
        ],
    ],
];
