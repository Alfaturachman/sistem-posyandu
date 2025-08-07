<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function ApiWeight(Request $request)
    {
        $validated = $request->validate([
            'weight' => 'required|numeric',
        ]);

        $deviceId = 'isp32_scale_017';

        $updated = DB::table('api_weight')
            ->where('device_id', $deviceId)
            ->update([
                'weight' => $validated['weight'],
                'timestamp' => now()
            ]);

        if ($updated) {
            return response()->json(['message' => 'Data berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Device ID tidak ditemukan atau data tidak berubah'], 404);
        }
    }
}
