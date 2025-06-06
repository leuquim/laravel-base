<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SafetyMechanismsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_prevents_stray_http_requests_by_default()
    {
        // This should throw an exception because stray requests are prevented
        $this->expectException(\Illuminate\Http\Client\RequestException::class);
        
        Http::get('https://httpbin.org/get');
    }

    #[Test]
    public function it_allows_http_requests_when_explicitly_enabled()
    {
        // Allow stray requests for this test
        Http::allowStrayRequests();
        
        // Mock the response instead of making a real request
        Http::fake([
            'httpbin.org/*' => Http::response(['success' => true], 200)
        ]);
        
        $response = Http::get('https://httpbin.org/get');
        
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['success' => true], $response->json());
    }

    #[Test]
    public function it_can_fake_specific_requests()
    {
        // Fake specific requests
        Http::fake([
            'api.example.com/*' => Http::response(['data' => 'mocked'], 200),
            'github.com/*' => Http::response(['user' => 'test'], 200)
        ]);
        
        $response1 = Http::get('https://api.example.com/users');
        $response2 = Http::get('https://github.com/user');
        
        $this->assertEquals(['data' => 'mocked'], $response1->json());
        $this->assertEquals(['user' => 'test'], $response2->json());
    }

    #[Test]
    public function safety_configuration_is_loaded()
    {
        // Test that our safety configuration is properly loaded
        $this->assertTrue(config('safety.prevent_lazy_loading.enabled'));
        $this->assertTrue(config('safety.prevent_missing_attributes'));
        $this->assertTrue(config('safety.prevent_silently_discarding_attributes'));
        $this->assertTrue(config('safety.enforce_morph_map'));
        $this->assertTrue(config('safety.query_monitoring.enabled'));
        $this->assertTrue(config('safety.lifecycle_monitoring.enabled'));
        $this->assertTrue(config('safety.prevent_stray_requests_in_tests'));
    }
} 