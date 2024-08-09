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

    'xapi_activities' => [

        'indexes' => [
            ['key' => ['type_id' => 1]],
            ['key' => ['is_category' => 1]],
            ['key' => ['is_profile' => 1]],
            ['key' => ['store' => 1]],
            ['key' => ['stored' => 1]],
            ['key' => ['updated' => 1]],
        ],
    ],
];
