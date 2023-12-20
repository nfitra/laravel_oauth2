<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Inquery extends Controller
{
    public function index(Request $request)
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
}
