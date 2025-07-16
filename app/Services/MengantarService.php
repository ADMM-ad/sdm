<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MengantarService
{
    protected $baseUrl = 'https://www.mengantar.com/api';

    public function searchAddress($query)
    {
        $response = Http::get("{$this->baseUrl}/address/search", [
            'q' => $query
        ]);

        return $response->json();
    }

    public function getShippingEstimate($originId, $destinationId, $weight = 1, $codAmount = 0)
    {
        $response = Http::get("{$this->baseUrl}/order/allEstimatePublic", [
            'origin_id' => $originId,
            'destination_id' => $destinationId,
            'weight' => $weight,
            'COD_AMOUNT' => $codAmount
        ]);

        return $response->json();
    }
}
