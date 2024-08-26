<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelayControl;
use App\Models\Config;
use App\Models\SensorData;
use Carbon\Carbon;

class RelayControlController extends Controller
{
    public function control(Request $request)
    {
        // Periksa apakah ada data di tabel RelayControl
        $check = RelayControl::first();

        if (!$check) {
            // Jika tidak ada, buat entri baru
            RelayControl::create([
                'state' => $request->status,
            ]);
        } else {
            // Jika ada, perbarui entri yang ada
            $check->update([
                'state' => $request->status,
            ]);
        }
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Status lampu berhasil diperbarui','relay' => $check]);
        } else {
            return redirect()->back();
        }
    }

    public function latest()
    {
        // Get the current time
        $now = Carbon::now('Asia/Jakarta');
        $config = Config::latest()->first();
    
        // Timer logic
        if ($config->use_timer) {
            $timerStart = isset($config->timer_start) ? Carbon::parse($config->timer_start, 'Asia/Jakarta') : null;
            $timerEnd = isset($config->timer_end) ? Carbon::parse($config->timer_end, 'Asia/Jakarta') : null;
    
            if ($timerStart && $timerEnd) {
                if ($now->between($timerStart, $timerEnd)) {
                    RelayControl::updateOrCreate([], ['state' => 'on']);
                } else {
                    RelayControl::updateOrCreate([], ['state' => 'off']);
                }
            }
        }
    
        // Limit RP logic
        if ($config->use_limit_rp) {
            $totalEnergy = SensorData::whereBetween('created_at', [$config->setup_new_month_start, $config->setup_new_month_end])->sum('power') / 1000;
            $totalCost = $totalEnergy * 415;
    
            if ($now->between($config->setup_new_month_start, $config->setup_new_month_end) && $totalCost >= $config->reach_limit_rp) {
                RelayControl::updateOrCreate([], ['state' => 'off']);
            } else {
                $futureDate = $now->copy()->addDays(30);
    
                // Ensure futureDate remains within the same month
                if ($futureDate->month != $now->month) {
                    $futureDate = $now->copy()->endOfMonth();
                }
    
                $config->update([
                    'setup_new_month_start' => $now->copy()->startOfMonth(),
                    'setup_new_month_end' => $futureDate,
                ]);
    
                if ($totalCost >= $config->reach_limit_rp) {
                    RelayControl::updateOrCreate([], ['state' => 'off']);
                }
            }
        }
    
        $latestState = RelayControl::orderBy('created_at', 'desc')->first();
    
        return response()->json($latestState);
    }
    
}
