<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndonesiaPostalCode;

class PostalCodeController extends Controller
{
    public function getRegencies(Request $request)
    {
        return IndonesiaPostalCode::where('province', $request->province)
            ->select('regency')
            ->distinct()
            ->orderBy('regency')
            ->get();
    }

    public function getDistricts(Request $request)
    {
        return IndonesiaPostalCode::where('province', $request->province)
            ->where('regency', $request->regency)
            ->select('district')
            ->distinct()
            ->orderBy('district')
            ->get();
    }

    public function getVillages(Request $request)
    {
        return IndonesiaPostalCode::where('province', $request->province)
            ->where('regency', $request->regency)
            ->where('district', $request->district)
            ->select('village')
            ->distinct()
            ->orderBy('village')
            ->get();
    }
}
