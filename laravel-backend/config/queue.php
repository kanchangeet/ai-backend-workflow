<?php

return [

    'default' => env('QUEUE_CONNECTION', 'sqs'),

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'sqs' => [
            'driver'      => 'sqs',
            'key'         => env('AWS_ACCESS_KEY_ID'),
            'secret'      => env('AWS_SECRET_ACCESS_KEY'),
            'prefix'      => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/000000000000'),
            'queue'       => env('SQS_QUEUE', 'default'),
            'suffix'      => env('SQS_SUFFIX'),
            'region'      => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => true, // dispatch only after DB transaction commits
        ],

        // Local fallback (used in tests / local dev)
        'database' => [
            'driver'       => 'database',
            'connection'   => env('QUEUE_DB_CONNECTION', 'pgsql'),
            'table'        => 'jobs',
            'queue'        => 'default',
            'retry_after'  => 90,
            'after_commit' => false,
        ],

    ],

    'batching' => [
        'database'   => env('DB_CONNECTION', 'pgsql'),
        'table'      => 'job_batches',
    ],

    'failed' => [
        'driver'   => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'pgsql'),
        'table'    => 'failed_jobs',
    ],

];
