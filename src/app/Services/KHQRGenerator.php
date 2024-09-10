<?php

namespace App\Services;

class KHQRGenerator
{
    // Currency data
    const CURRENCY_USD = '840';
    const CURRENCY_KHR = '116';

    // Individual Info class to store payment details
    public function generateIndividual($accountId, $merchantName, $merchantCity, $optionalData = [])
    {
        $currency = $optionalData['currency'] ?? self::CURRENCY_USD;
        $amount = $optionalData['amount'] ?? '0.01';
        $billNumber = $optionalData['billNumber'] ?? '';
        $mobileNumber = $optionalData['mobileNumber'] ?? '';
        $storeLabel = $optionalData['storeLabel'] ?? '';
        $terminalLabel = $optionalData['terminalLabel'] ?? '';

        // Construct KHQR string
        $qrString = implode('', [
            '00', '01', // Payload Format Indicator
            '01', '12', '00', '02', 'KH', // Point of Initiation Method and Merchant Country Code
            '26', '00', '21', // Merchant Account Information Template (MCC)
            '00', '20', $accountId, // Merchant Account
            '02', '16', $merchantName, // Merchant Name
            '03', '10', $merchantCity, // Merchant City
            '54', '03', $currency, // Transaction Currency
            '55', '04', $amount, // Transaction Amount
            '58', '04', 'KH', // Country Code
            '59', '15', $billNumber, // Bill Number
            '60', '11', $mobileNumber, // Mobile Number
            '62', '02', '01', // Merchant Information
            '07', '14', $storeLabel, // Store Label
            '08', '16', $terminalLabel, // Terminal Label
            '63', '04', $this->crc16('KHQR') // CRC16 Checksum (mocked implementation)
        ]);

        return [
            'qr' => $qrString,
            'md5' => md5($qrString)
        ];
    }

    // CRC16 function (mock implementation)
    private function crc16($string)
    {
        // Replace with actual CRC16 computation logic
        return 'ABCD';
    }
}
