<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
public function getProvinsi()
    {
        $provinsi = DB::table('indonesia_postal_codes')->select('province')->distinct()->orderBy('province')->get();
        return response()->json($provinsi);
    }

    public function getRegency(Request $request)
    {
        $regency = DB::table('indonesia_postal_codes')
            ->select('regency')->where('province', $request->province)
            ->distinct()->orderBy('regency')->get();
        return response()->json($regency);
    }

    public function getDistrict(Request $request)
    {
        $district = DB::table('indonesia_postal_codes')
            ->select('district')->where([
                ['province', '=', $request->province],
                ['regency', '=', $request->regency],
            ])->distinct()->orderBy('district')->get();
        return response()->json($district);
    }

    public function getVillage(Request $request)
    {
        $village = DB::table('indonesia_postal_codes')
            ->select('village')->where([
                ['province', '=', $request->province],
                ['regency', '=', $request->regency],
                ['district', '=', $request->district],
            ])->distinct()->orderBy('village')->get();
        return response()->json($village);
    }

    public function getKodepos(Request $request)
    {
        $data = DB::table('indonesia_postal_codes')->select('postal_code')
            ->where([
                ['province', '=', $request->province],
                ['regency', '=', $request->regency],
                ['district', '=', $request->district],
                ['village', '=', $request->village],
            ])->first();

        return response()->json(['kodepos' => $data ? $data->postal_code : '']);
    }
}