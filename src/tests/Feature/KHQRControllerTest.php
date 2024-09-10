<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KHQRControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_khqr_code()
    {
        // Mock the request data
        $requestData = [
            'amount' => 0.01,
        ];

        // Make a POST request to the /generate-khqr endpoint
        $response = $this->postJson('/generate-khqr', $requestData);

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains the expected keys
        $response->assertJsonStructure([
            'qr',
            'md5',
        ]);

        // Optionally, assert that the generated QR code string is not empty
        $this->assertNotEmpty($response->json('qr'));
    }
}
