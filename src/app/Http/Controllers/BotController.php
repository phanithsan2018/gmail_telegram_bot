<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    protected $telegramService;
    protected $botToken;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    /**
     * Handle the initial start message sent to the Telegram bot.
     */
    public function start(Request $request)
    {
        $chatId = $request->input('chat_id'); // Get the chat ID from the request

        // Use the Telegram service to send the start message
        $response = $this->telegramService->sendStartMessage($chatId);

        return response()->json($response); // Return the response from the Telegram API
    }

    /**
     * Handle incoming callback queries from Telegram.
     */
    public function handleCallback(Request $request)
    {
        // Log the incoming callback data for debugging
        Log::info('Received Telegram callback', ['callback_data' => $request->all()]);

        // Extract the callback_query from the incoming request
        $callbackQuery = $request->input('callback_query');

        if ($callbackQuery) {
            $data = $callbackQuery['data']; // This is the 'callback_data' sent when the button was clicked
            $chatId = $callbackQuery['message']['chat']['id'];

            if ($data === 'start_service') {
                // Send the quantity selection message
                $this->telegramService->sendAskQtyMessage($chatId);

                // Acknowledge the callback query to avoid timeout
                Http::post("https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery", [
                    'callback_query_id' => $callbackQuery['id'],
                    'text' => 'Please select the quantity.',
                ]);

                return response()->json(['status' => 'success']);
            } elseif (strpos($data, 'qty_') === 0) {
                // Handle the quantity selection callback
                $qty = str_replace('qty_', '', $data);

                // Respond to the user with the selected quantity
                Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "You have selected $qty accounts."
                ]);

                // Send the payment message after selecting the quantity
                $this->telegramService->sendPaymentMessage($chatId);

                // Acknowledge the callback query to avoid timeout
                Http::post("https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery", [
                    'callback_query_id' => $callbackQuery['id'],
                    'text' => 'Payment process initiated.',
                ]);

                return response()->json(['status' => 'success']);
            } elseif ($data === 'confirm_payment') {
                // Handle the payment confirmation callback
                // Process the payment
                $this->telegramService->processPayment($chatId);

                // Acknowledge the callback query
                Http::post("https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery", [
                    'callback_query_id' => $callbackQuery['id'],
                    'text' => 'Payment confirmed!',
                ]);

                return response()->json(['status' => 'success']);
            } elseif ($data === 'cancel_payment') {
                // Handle the payment cancellation callback
                // Send a cancellation message
                Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Payment has been canceled."
                ]);

                // Acknowledge the callback query
                Http::post("https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery", [
                    'callback_query_id' => $callbackQuery['id'],
                    'text' => 'Payment canceled!',
                ]);

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'no action'], 200);
    }
}
