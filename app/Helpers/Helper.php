<?php

namespace App\Helpers;

if (!function_exists('vaData')) {
    function getVirtualAccountValue($message)
    {
        $data = [
            'inquiryStatus' => '01',
            'inquiryReason' => [
                'english' => $message,
                'indonesia' => __($message, [], 'id'),
            ],
            'partnerServiceId' => '',
            'customerNo' => '',
            'virtualAccountNo' => '',
            'virtualAccountName' => '',
            'virtualAccountEmail' => '',
            'virtualAccountPhone' => '',
            'inquiryRequestId' => '',
            'totalAmount' => '',
            'subCompany' => '',
            'billDetails' => [
                'billCode' => '',
                'billNo' => '',
                'billName' => '',
                'billShortName' => '',
                'billDescription' => [
                    'english' => '',
                    'indonesia' => '',
                ],
                'billSubCompany' => '',
                'billAmount' => [
                    'value' => '',
                    'currency' => '',
                ],
                'billAmountLabel' => '',
                'billAmountValue' => '',
                'additionalInfo' => ''
            ],
            'freeTexts' => '',
            'virtualAccountTrxType' => '',
            'feeAmount' => '',
            'additionalInfo' => ''
        ];

        return $data;
    }
}
