<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Prevent stray HTTP requests during testing
        // This ensures tests don't accidentally make real HTTP requests
        // unless explicitly allowed with Http::allowStrayRequests()
        if (config('safety.prevent_stray_requests_in_tests', true)) {
            Http::preventStrayRequests();
        }
    }
}
