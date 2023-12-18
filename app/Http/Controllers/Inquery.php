<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Inquery extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://10.10.10.96:88/inv-ifca/transfer_va/inquiry',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
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
            }',
            CURLOPT_HTTPHEADER => array(
                'CHANNEL-ID: 95231',
                'X-PARTNER-ID: 99999',
                'X-EXTERNAL-ID: 123456789012345678901234567890123456',
                'Content-Type: application/json',
                'Cookie: ci_session=b85j0aktgb8rmm3jajmb5r3osrr1lih0'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
