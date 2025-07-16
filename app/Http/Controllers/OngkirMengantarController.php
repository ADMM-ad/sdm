<?php

namespace App\Http\Controllers;
use App\Services\MengantarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class OngkirMengantarController extends Controller
{
     

 public function searchAddress(Request $request)
    {
        $keyword = $request->query('keyword');
        $apiKey = '1234567890abcdef';

        $response = Http::get("https://api-public.mengantar.com/api/public/{$apiKey}/address/search", [
            'keyword' => $keyword,
        ]);

        return response()->json($response->json());
    }

    public function getEstimate(Request $request)
    {
        $origin_id = $request->query('origin_id');
        $destination_id = $request->query('destination_id');
        $weight = $request->query('weight');
        $cod = $request->query('COD_AMOUNT');

        $response = Http::get('https://api-public.mengantar.com/api/order/allEstimatePublic', [
            'origin_id' => $origin_id,
            'destination_id' => $destination_id,
            'weight' => $weight,
            'COD_AMOUNT' => $cod
        ]);

        return response()->json($response->json());
    }
    
}
