<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RelayControlController;
use App\Http\Controllers\SettingController;
use App\Models\SensorData;
use App\Models\Config;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// URL::forceSheme('https');

Route::get('/tes',function(){
    
    $config = Config::latest()->first();
        
    $limit = $config->jenis_tegangan * 80/100;
    if($request->power > $limit){
        $response = Http::withHeaders([
            'Authorization'=> env('APP_FONNTE'),
            ])->post('https://api.fonnte.com/send',[
                'target' =>$config->no_hp_target, // gw tandain 
                'message' => "Daya Listrik Melebihi 80% dari " . $config->jenis_tegangan." Matikan Beberapa Elektronik Untuk mengurangi Beban Daya",                
                'countryCode' => '+62',
            ]);
    }
});

Route::controller(HomeController::class)->group(function(){
    Route::get('/', 'index')->name('dashboard');
    Route::get('/view-sensor-data', 'viewDataSensor')->name('sensordata');
    Route::get('/real-sensor-data', 'dataSensor')->name('realtimedata');
    Route::get('/real-sensor-data-sekarang', 'dataSekarang')->name('realtimedatanow');
    Route::get('/lamp', 'lampControll')->name('lampControl');
});

Route::controller(SettingController::class)->group(function(){
    Route::get('/setting','index')->name('setting');
    Route::post('/penerapan-setting','store')->name('terapkansetting');
}); 

Route::controller(RelayControlController::class)->group(function(){
    Route::post('/relay-control','control')->name('relayControl');
});


