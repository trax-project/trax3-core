<?php

return [

    /**
     * This file describes the indexes to be used by MongoDB.
     * The default configuration replicates the indexes of the Laravel migrations for MySQL and PostgreSQL.
     *
     * To customize this file, make a copy in the 'custom/mongodb' folder with the command:
     *
     * > php artisan vendor:publish --tag=mongodb-config
     *
     * Refer to the MondoDB documentation to understand the available options:
     *
     * https://www.mongodb.com/docs/php-library/v1.12/reference/method/MongoDBCollection-createIndex/
     */

    'xapi_states' => [

        'indexes' => [
            ['key' => ['activity_id' => 1]],
            ['key' => ['agent_id' => 1]],
            ['key' => ['state_id' => 1]],
            ['key' => ['registration' => 1]],
            ['key' => ['content_type' => 1]],
            ['key' => ['store' => 1]],
            ['key' => ['stored' => 1]],
            ['key' => ['updated' => 1]],
        ],
    ],
];
