<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransferVA extends Controller
{
    public function inquiry(Request $request)
    {
        $url = 'http://10.10.10.96:88/inv-ifca/transfer_va/inquiry';

        $headers = array(
            'CHANNEL-ID: 95231',
            'X-PARTNER-ID: 99999',
            'X-EXTERNAL-ID: 123456789012345678901234567890123456',
            'Content-Type: application/json',
            'Cookie: ci_session=b85j0aktgb8rmm3jajmb5r3osrr1lih0'
        );

        $body = '{
            "partnerServiceId": " 99999",
            "customerNo": "000002022062402946",
            "virtualAccountNo": " 99999000002022062402946",
            "trxDateInit": "2023-11-23T11:12:57+07:00",
            "channelCode": 6011,
            "language": "",
            "amount": null,
            "hashedSourceAccountNo": "",
            "sourceBankCode": "014",
            "additionalInfo": {
                "value": ""
            },
            "passApp": "",
            "inquiryRequestId": "202202111031031234500001136962"
        }';

        return Http::timeout(5)->withHeaders($headers)->withBody($body)->post($url);
    }

    public function payment(Request $request)
    {
        $url = 'http://10.10.10.96:88/inv-ifca/transfer_va/payment';

        $headers = array(
            'CHANNEL-ID: 95231',
            'X-PARTNER-ID: 99999',
            'X-EXTERNAL-ID: 123456789012345678901234567890123456',
            'Content-Type: application/json'
        );

        $body = '{
            "partnerServiceId": " 99999",
            "customerNo": "000002022062402946",
            "virtualAccountNo": " 99999000002022062402946",
            "virtualAccountName": "DR. Kamelia Faisal. MARS",
            "virtualAccountEmail": "kamelia.faisal@gmail.com",
            "virtualAccountPhone": " ",
            "trxId": "",
            "paymentRequestId": "202202111031031234500001136962",
            "channelCode": 6011,
            "hashedSourceAccountNo": "",
            "sourceBankCode": "014",
            "paidAmount": {
                "value": "11495867.00",
                "currency": "IDR"
            },
            "cumulativePaymentAmount": null,
            "paidBills": "",
            "totalAmount": {
                "value": "11495867.00",
                "currency": "IDR"
            },
            "trxDateTime": "2023-11-23T17:29:57+07:00",
            "referenceNo": "00113696201",
            "journalNum": "",
            "paymentType": "",
            "flagAdvise": "N",
            "subCompany": "",
            "billDetails": [{
                "billCode": "1",
                "billNo": "0113",
                "billName": "Electricity",
                "billShortName": "EL",
                "billDescription": {
                    "english": "Electricity",
                    "indonesia": "Listrik"
                },
                "billSubCompany": "",
                "billAmount": {
                    "value": "851904.00",
                    "currency": "IDR"
                },
                "additionalInfo": {
                    "value": "Additional Data 1"
                },
                "billReferenceNo": "00113696201"
                },
                {
                "billCode": "2",
                "billNo": "0114",
                "billName": "Water",
                "billShortName": "WT",
                "billDescription": {
                    "english": "Water",
                    "indonesia": "Air"
                },
                "billSubCompany": "",
                "billAmount": {
                    "value": "65557.00",
                    "currency": "IDR"
                },
                "additionalInfo": {
                    "value": "Additional Data 2"
                },
                "billReferenceNo": "00213696201"
                }
            ],
            "freeTexts": [],
            "additionalInfo": {
            "value": ""
            }
        }';

        return Http::timeout(5)->withHeaders($headers)->withBody($body)->post($url);
    }
}
