<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Config::latest()->first();
        return view('setting',compact('setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'use_timer' => 'nullable',
            'timer_start' => 'nullable',
            'timer_end' => 'nullable',
            'use_limit_rp' => 'nullable',
            'reach_limit_rp' => 'nullable',
        ]);
        $use_timer = false;
        if($request->use_timer == "on"){
            $use_timer = true;
        }
        $use_limit_rp = false;
        if($request->use_limit_rp == "on"){
            $use_limit_rp = true;
        }
        $check = Config::all();
        if($check->count() >0 ){
            Config::latest()->update([
                'use_timer' => $use_timer,
                'timer_start' => $request->timer_start,
                'timer_end' => $request->timer_end,
                'use_limit_rp' => $use_limit_rp,
                'reach_limit_rp' => $request->reach_limit_rp,
                'setup_new_month_start' => $request->setup_new_month_start,
                'setup_new_month_end' => $request->setup_new_month_end,
                'jenis_tegangan' => $request->jenis_tegangan,
                'no_hp_target' => $request->no_hp_target,
            ]);
        } else {

            $result = Config::create($request->all());
        }
        return redirect()->back()
                         ->with('success', 'Setting created successfully.');
    }
}
