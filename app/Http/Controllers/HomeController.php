<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelayControl;
use App\Models\SensorData;
use App\Models\Config;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $relay = RelayControl::select('state')->first();
        $sensor = SensorData::latest()->take(100)->get();
        $sekarang = SensorData::latest()->first();
        return view('index', compact('relay', 'sensor','sekarang'));
    }

    public function viewDataSensor()
    {
        $sensor = SensorData::orderBy('created_at','Desc')->take(100)->get();
        return view('table', compact('sensor'));
    }

    public function dataSensor(){
        $sensor = SensorData::latest()->take(100)->get();
        return response()->json($sensor);
    }

    public function dataSekarang()
    {
        $now = Carbon::now();
        $config = Config::latest()->first();
        $totalEnergy = SensorData::whereBetween('created_at',[$config->setup_new_month_start,$config->setup_new_month_end])->sum('power');
        $totalEnergy = $totalEnergy/1000;
        $totalCost = $totalEnergy * 415;
        $reach_limit = false;
        $limit = $config->jenis_tegangan * 80/100;
        $sekarang = SensorData::orderBy('created_at','desc')->first();
        if($sekarang->power > $limit)
    // if($simulasi > $limit)
        {
            $reach_limit = true;
            $response = Http::withHeaders([
                'Authorization'=> env('APP_FONNTE'),
                ])->post('https://api.fonnte.com/send',[
                    'target' =>$config->no_hp_target, // gw tandain 
                    'message' => "Daya Listrik Melebihi 80% dari " . $config->jenis_tegangan." Matikan Beberapa Elektronik Untuk mengurangi Beban Daya",                
                    'countryCode' => '+62',
                ]);
        }
            
            $reach_rp = false;
            if ($config->use_limit_rp) {
                $totalEnergy = SensorData::whereBetween('created_at', [$config->setup_new_month_start, $config->setup_new_month_end])->sum('power') / 1000;
                $totalCost = $totalEnergy * 415;
                
                if ($now->between($config->setup_new_month_start, $config->setup_new_month_end) && $totalCost >= $config->reach_limit_rp) {
                    $response = Http::withHeaders([
                        'Authorization'=> env('APP_FONNTE'),
                        ])->post('https://api.fonnte.com/send',[
                            'target' =>$config->no_hp_target, // gw tandain 
                            'message' => "Biaya Mencapai Limit Biaya yang sudah anda atur Lampu akan dimatikan oleh sistem",                
                            'countryCode' => '+62',
                        ]);
                    $reach_rp=true;
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
                    $response = Http::withHeaders([
                        'Authorization'=> env('APP_FONNTE'),
                        ])->post('https://api.fonnte.com/send',[
                            'target' =>$config->no_hp_target, // gw tandain 
                            'message' => "Biaya Mencapai Limit Biaya yang sudah anda atur Lampu akan dimatikan oleh sistem",                
                            'countryCode' => '+62',
                        ]);
                    $reach_rp=true;
                    RelayControl::updateOrCreate([], ['state' => 'off']);
                }
            }
        }
        $data = [
            'reach_rp' => $reach_rp,
            'reach_limit' => $reach_limit,
            'sekarang' => $sekarang,
            'totalBiaya' => number_format($totalCost),0,".",".",
            'jenis_listrik' => $config->jenis_tegangan
        ];

        return response()->json($data);
    }

    public function lampControll(){
        $relay = RelayControl::select('state')->first();
        return view('controllamp',compact('relay'));
    }

}

