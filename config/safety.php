<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Safety Mechanisms Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for Laravel's built-in safety
    | mechanisms that help prevent common mistakes and performance issues.
    |
    | Environment Variables:
    | - PREVENT_LAZY_LOADING=true
    | - LOG_LAZY_LOADING_VIOLATIONS=true
    | - PREVENT_MISSING_ATTRIBUTES=true
    | - PREVENT_SILENTLY_DISCARDING_ATTRIBUTES=true
    | - ENFORCE_MORPH_MAP=true
    | - MONITOR_DATABASE_QUERIES=true
    | - CUMULATIVE_QUERY_THRESHOLD=2000
    | - INDIVIDUAL_QUERY_THRESHOLD=1000
    | - MONITOR_LIFECYCLE=true
    | - REQUEST_THRESHOLD=5000
    | - COMMAND_THRESHOLD=5000
    | - PREVENT_STRAY_REQUESTS_IN_TESTS=true
    |
    */

    /*
    |--------------------------------------------------------------------------
    | N+1 Query Prevention
    |--------------------------------------------------------------------------
    |
    | When enabled, this will prevent lazy loading of Eloquent relationships
    | which can cause N+1 query problems. In production, violations are logged
    | instead of throwing exceptions to prevent application crashes.
    |
    */
    'prevent_lazy_loading' => [
        'enabled' => env('PREVENT_LAZY_LOADING', true),
        'log_violations_in_production' => env('LOG_LAZY_LOADING_VIOLATIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Attribute Protection
    |--------------------------------------------------------------------------
    |
    | These settings help prevent common model-related mistakes like accessing
    | attributes that weren't selected from the database or silently discarding
    | attributes during mass assignment.
    |
    */
    'prevent_missing_attributes' => env('PREVENT_MISSING_ATTRIBUTES', true),
    'prevent_silently_discarding_attributes' => env('PREVENT_SILENTLY_DISCARDING_ATTRIBUTES', true),

    /*
    |--------------------------------------------------------------------------
    | Polymorphic Relationship Enforcement
    |--------------------------------------------------------------------------
    |
    | When enabled, this enforces that all polymorphic relationships use a
    | morph map instead of storing full class names in the database.
    |
    */
    'enforce_morph_map' => env('ENFORCE_MORPH_MAP', true),

    /*
    |--------------------------------------------------------------------------
    | Database Query Monitoring
    |--------------------------------------------------------------------------
    |
    | These settings control monitoring of database query performance.
    | Times are specified in milliseconds.
    |
    */
    'query_monitoring' => [
        'enabled' => env('MONITOR_DATABASE_QUERIES', true),
        'cumulative_query_threshold' => env('CUMULATIVE_QUERY_THRESHOLD', 2000), // 2 seconds
        'individual_query_threshold' => env('INDIVIDUAL_QUERY_THRESHOLD', 1000), // 1 second
    ],

    /*
    |--------------------------------------------------------------------------
    | Request and Command Lifecycle Monitoring
    |--------------------------------------------------------------------------
    |
    | Monitor slow HTTP requests and console commands.
    | Times are specified in milliseconds.
    |
    */
    'lifecycle_monitoring' => [
        'enabled' => env('MONITOR_LIFECYCLE', true),
        'request_threshold' => env('REQUEST_THRESHOLD', 5000), // 5 seconds
        'command_threshold' => env('COMMAND_THRESHOLD', 5000), // 5 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Request Prevention in Tests
    |--------------------------------------------------------------------------
    |
    | When enabled, prevents stray HTTP requests during testing unless
    | explicitly allowed. This helps catch unintended external API calls.
    |
    */
    'prevent_stray_requests_in_tests' => env('PREVENT_STRAY_REQUESTS_IN_TESTS', true),
]; 