<?php

namespace Tests\Feature;

use Tests\TestCase;

class BotTest extends TestCase
{
    /**
     * Test the bot start message endpoint.
     *
     * @return void
     */
    public function testBotStartMessage()
    {
        // Simulate a POST request to the /start endpoint
        $response = $this->postJson('/start', [
            'chat_id' => '385485547' // Replace with a valid chat ID for testing
        ]);

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains the expected data structure
        $response->assertJson([
            'ok' => true,
        ]);
    }
}
