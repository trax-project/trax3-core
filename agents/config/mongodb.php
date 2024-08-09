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

    'xapi_agents' => [

        'indexes' => [
            ['key' => ['sid_type' => 1]],
            ['key' => ['members' => 1]],
            ['key' => ['is_group' => 1]],
            ['key' => ['pseudonymized' => 1]],
            ['key' => ['person_id' => 1]],
            ['key' => ['store' => 1]],
            ['key' => ['stored' => 1]],
            ['key' => ['updated' => 1]],
        ],
    ],
];
