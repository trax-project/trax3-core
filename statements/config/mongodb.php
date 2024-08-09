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

    'xapi_statements' => [

        'indexes' => [
            ['key' => ['voided' => 1]],
            ['key' => ['voiding' => 1]],
            ['key' => ['validated' => 1]],
            ['key' => ['valid' => 1]],
            ['key' => ['pseudonymized' => 1]],
            ['key' => ['actor_id' => 1]],
            ['key' => ['verb_id' => 1]],
            ['key' => ['object_id' => 1]],
            ['key' => ['type_id' => 1]],
            ['key' => ['agent_ids' => 1]],
            ['key' => ['activity_ids' => 1]],
            ['key' => ['registration' => 1]],
            ['key' => ['statement_ref' => 1]],
            ['key' => ['store' => 1]],
            ['key' => ['client' => 1]],
            ['key' => ['stored' => 1]],
        ],
    ],

    'xapi_attachments' => [

        'indexes' => [
            ['key' => ['content_type' => 1]],
            ['key' => ['store' => 1]],
            ['key' => ['client' => 1]],
            ['key' => ['stored' => 1]],
        ],
    ],
];
