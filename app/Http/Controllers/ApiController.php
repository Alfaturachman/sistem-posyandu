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

        $updated = DB::table('api_iot')
            ->where('device_id', $deviceId)
            ->update([
                'weight' => $validated['weight'],
                'timestamp' => now()
            ]);

        if ($updated) {
            return response()->json(['message' => 'Weight berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Device ID tidak ditemukan atau data tidak berubah'], 404);
        }
    }

    public function ApiHeight(Request $request)
    {
        $validated = $request->validate([
            'height' => 'required|numeric',
        ]);

        $deviceId = 'isp32_scale_017';

        $updated = DB::table('api_iot')
            ->where('device_id', $deviceId)
            ->update([
                'height' => $validated['height'],
                'timestamp' => now()
            ]);

        if ($updated) {
            return response()->json(['message' => 'Height berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Device ID tidak ditemukan atau data tidak berubah'], 404);
        }
    }

    public function ApiHad(Request $request)
    {
        $validated = $request->validate([
            'had' => 'required|numeric',
        ]);

        $deviceId = 'isp32_scale_017';

        $updated = DB::table('api_iot')
            ->where('device_id', $deviceId)
            ->update([
                'had' => $validated['had'],
                'timestamp' => now()
            ]);

        if ($updated) {
            return response()->json(['message' => 'HAD berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Device ID tidak ditemukan atau data tidak berubah'], 404);
        }
    }

    public function ApiArm(Request $request)
    {
        $validated = $request->validate([
            'arm' => 'required|numeric',
        ]);

        $deviceId = 'isp32_scale_017';

        $updated = DB::table('api_iot')
            ->where('device_id', $deviceId)
            ->update([
                'arm' => $validated['arm'],
                'timestamp' => now()
            ]);

        if ($updated) {
            return response()->json(['message' => 'ARM berhasil diupdate'], 200);
        } else {
            return response()->json(['message' => 'Device ID tidak ditemukan atau data tidak berubah'], 404);
        }
    }
}
