<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class SensorDataController extends Controller
{
    public function store(Request $request)
    {
        $config = Config::latest()->first();
        
        $limit =  $config->jenis_tegangan * 80/100;

        $sensorData = new SensorData();
        $sensorData->voltage = $request->voltage;
        $sensorData->current = $request->current;
        $sensorData->power = $request->power;
        $sensorData->energy = $request->energy;
        $sensorData->save();
        if($request->power > $limit){
            $response = Http::withHeaders([
                'Authorization'=> env('APP_FONNTE'),
                ])->post('https://api.fonnte.com/send',[
                    'target' =>$config->no_hp_target, // gw tandain 
                    'message' => "Daya Listrik Melebihi 80% dari " . $config->jenis_tegangan." Matikan Beberapa Elektronik Untuk mengurangi Beban Daya",                
                    'countryCode' => '+62',
                ]);
        }

        if($request->power > $config->jenis_tegangan)
        {
            RelayControl::updateOrCreate([], ['state' => 'off']);  
            $response = Http::withHeaders([
                'Authorization'=> env('APP_FONNTE'),
                ])->post('https://api.fonnte.com/send',[
                    'target' =>$config->no_hp_target, // gw tandain 
                    'message' => "Daya Listrik Melebihi dari " . $config->jenis_tegangan." Sistem Akan Mematikan Beban Daya",                
                    'countryCode' => '+62',
                ]);
        }

        return response()->json(['message' => 'Data stored successfully'], 200);
    }

    public function latest()
    {
        $latestData = SensorData::orderBy('created_at', 'desc')->first();
        return response()->json($latestData);
    }
}
