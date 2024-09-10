<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN'); // Make sure your bot token is set in the .env file
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send the initial start message with an inline button.
     */
    public function sendStartMessage($chatId)
    {
        $text = "សួស្តី! Welcome to Easy Gmail Bot\nStart your Gmail Account now!";
        $imageUrl = "https://png.pngtree.com/png-vector/20220707/ourmid/pngtree-chatbot-robot-concept-chat-bot-png-image_5632381.png"; // Your provided image URL
        $buttonText = "Start Bot";
        $callbackData = "start_service";

        // Define the inline keyboard with one button
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => $buttonText, 'callback_data' => $callbackData]
                ]
            ]
        ];

        // Make the API call to send a photo with an inline keyboard
        $response = Http::post("{$this->apiUrl}/sendPhoto", [
            'chat_id' => $chatId,
            'photo' => $imageUrl,
            'caption' => $text,
            'reply_markup' => json_encode($keyboard) // Convert keyboard array to JSON
        ]);

        return $response->json();
    }

    /**
     * Send a message asking for quantity selection.
     */
    public function sendAskQtyMessage($chatId)
    {
        $text = "សូមជ្រើសរើសចំនួនអាខោនដែលលោកអ្នកចង់ទិញ៖\n\nអាខោន 1 = 0.5$\nអាខោន 2 = 1$\nអាខោន 5 = 2$\nអាខោន 10 = 4$";

        // Define the inline keyboard with quantity buttons
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '1 អាខោន', 'callback_data' => 'qty_1'],
                    ['text' => '2 អាខោន', 'callback_data' => 'qty_2']
                ],
                [
                    ['text' => '3 អាខោន', 'callback_data' => 'qty_3'],
                    ['text' => '4 អាខោន', 'callback_data' => 'qty_4']
                ]
            ]
        ];

        // Make the API call to send the message with the inline keyboard
        $response = Http::post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode($keyboard) // Convert keyboard array to JSON
        ]);

        return $response->json();
    }
    public function sendPaymentMessage($chatId)
    {
        $text = "សូមដកប្រាក់តាមរយៈ QR អ្នកអាចស្កេន: \nបង្ហាញសម្រាប់ការទូទាត់";
        $payButtonText = "បញ្ជាក់"; // Confirm button
        $cancelButtonText = "បោះបង់"; // Cancel button

        // Define the inline keyboard with payment options
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => $payButtonText, 'callback_data' => 'confirm_payment'],
                    ['text' => $cancelButtonText, 'callback_data' => 'cancel_payment']
                ]
            ]
        ];

        // Use the provided external image URL for testing
        $imageUrl = "https://www.ledgerinsights.com/wp-content/uploads/2022/07/cambodia-bakong-qr-hkqr-810x524.jpg";

        // Log the request data being sent to Telegram API
        Log::info('Sending payment message to Telegram with external image', [
            'chat_id' => $chatId,
            'photo' => $imageUrl,
            'caption' => $text,
            'reply_markup' => json_encode($keyboard)
        ]);

        // Make the API call to send a photo with an inline keyboard
        $response = Http::post("{$this->apiUrl}/sendPhoto", [
            'chat_id' => $chatId,
            'photo' => $imageUrl, // URL of the external image
            'caption' => $text,
            'reply_markup' => json_encode($keyboard) // Convert keyboard array to JSON
        ]);

        // Log the response from the Telegram API
        Log::info('Received response from Telegram API', [
            'response' => $response->json()
        ]);

        return $response->json();
    }
    /**
     * Process the payment after confirmation.
     */
    public function processPayment($chatId)
    {
        // Implement the actual payment processing logic here
        // Example: send a message to confirm payment completion
        $response = Http::post("{$this->apiUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "Thank you for your purchase! \n Email: abc@gmail.com \n Password: 123456",
        ]);

        return $response->json();
    }
}
