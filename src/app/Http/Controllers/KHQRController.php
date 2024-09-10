<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\KHQRGenerator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class KHQRController extends Controller
{
    protected $khqrGenerator;

    public function __construct(KHQRGenerator $khqrGenerator)
    {
        $this->khqrGenerator = $khqrGenerator;
    }

    public function showForm()
    {
        // Show the form view
        return view('generate');
    }

    public function generateKHQR(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Retrieve input data from the request
        $amount = $request->input('amount');
        $merchantName = "Vanny Cute Boy";
        $merchantCity = "Phnom Penh";
        $billNumber = "12345";
        $mobileNumber = "85587714004";
        $storeLabel = "Vanny Cute Boy";
        $terminalLabel = "terminal_1";
        $accountInfo = "000592094";
        $acquiringBank = "ABA Bank";

        // Prepare optional data for the KHQR code
        $optionalData = [
            'currency' => KHQRGenerator::CURRENCY_USD,
            'amount' => $amount,
            'billNumber' => $billNumber,
            'mobileNumber' => $mobileNumber,
            'storeLabel' => $storeLabel,
            'terminalLabel' => $terminalLabel,
        ];

        // Generate the KHQR code
        $result = $this->khqrGenerator->generateIndividual($accountInfo, $merchantName, $merchantCity, $optionalData);

        // Log the generated KHQR data
        Log::info('Generated KHQR Data:', $result);

        // Generate the QR code as a base64-encoded image
        $qrCode = new QrCode($result['qr']);
        $writer = new PngWriter();
        $qrImage = $writer->write($qrCode);

        // Convert the image to base64
        $qrBase64 = base64_encode($qrImage->getString());

        // Redirect back to the form with the generated QR code
        return redirect()->route('generate.form')->with('qr', $qrBase64);
    }
}
