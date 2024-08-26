@extends('layouts.app')
@section('content')
@php
    $settings=null;
@endphp
    <div class="container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pengaturan Aplikasi</h6>
            </div>
            <div class="card-body">
            <form action="{{route('terapkansetting')}}" method="POST">
            @csrf

            <div class="row">
                
            <div class="col-xl-12 d-flex">
                    <div class="col-xl-3">
                        <div class="form-group">
                            <strong>Setup Bulan Baru Start:</strong>
                            <input type="date" name="setup_new_month_start" class="form-control" value="{{ $setting->setup_new_month_start ?? ''}}" require>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="form-group">
                            <strong>Setup Bulan Baru End:</strong>
                            <input type="date" name="setup_new_month_end" class="form-control" value="{{ $setting->setup_new_month_end ?? '' }}" require>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="form-group">
                            <strong>Jenis Tegangan Rumah Anda:</strong>
                            <input type="text" name="jenis_tegangan" class="form-control" value="{{ $setting->jenis_tegangan ?? '' }}" placeholder="450/950/1300/2200/3500" require>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="form-group">
                            <strong>Nomor Hp Anda:</strong><i class="fa fa-question ml-3" id="quest"></i>
                            <input type="text" name="no_hp_target" class="form-control" value="{{ $setting->no_hp_target ?? '' }}" placeholder="Contoh : 08123456789" require>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 d-flex">
                    <div class="col-xl-4">

                        <div class="form-group mt-4">
                            <strong>Use Timer:</strong>
                            @if ($setting->use_timer)
                            @php
                            $disabled_timer = "";
                            $check_it_timer = "checked";
                            @endphp
                            @else
                            @php
                                $check_it_timer = "";
                                $disabled_timer = "disabled";
                            @endphp
                                
                            @endif
                            <input type="checkbox" name="use_timer" class="form-control"{{$check_it_timer}}>
                        </div>
                    </div>
                    <div class="col-xl-4">

                        <div class="form-group">
                            <strong>Timer Start:</strong>
                            <input type="time" name="timer_start" class="form-control" value="{{ $setting->timer_start ?? '' }}"{{$disabled_timer}}>
                        </div>
                    </div>
                    <div class="col-xl-4">

                        <div class="form-group">
                            <strong>Timer End:</strong>
                            <input type="time" name="timer_end" class="form-control" value="{{ $setting->timer_end ?? '' }}"{{$disabled_timer}}>
                        </div>
                    </div>
                    
                </div>
                <div class="col-xl-12 d-flex">
                    <div class="col-xl-4">
                        <div class="form-group mt-4">
                        @if ($setting->use_limit_rp)
                            @php
                            $disabled_limit = "";
                            $check_it_limit= "checked";
                            @endphp
                            @else
                            @php
                            $check_it_limit= "";
                                $disabled_limit = "disabled";
                            @endphp
                                
                            @endif
                            <strong>Use Limit Rp:</strong>
                            <input type="checkbox" name="use_limit_rp" class="form-control" {{ $check_it_limit; }}>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="form-group">
                            <strong>Reach Limit Rp:</strong>
                            <input type="text" name="reach_limit_rp" class="form-control" value="{{ isset($setting->reach_limit_rp) ? number_format($setting->reach_limit_rp, '0','.','.')  : 0 }}" {{$disabled_limit}}>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary d-block col-xl-12">Terapkan</button>
                </div>
            </div>

        </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $("[name='use_timer']").bootstrapSwitch();
            $("[name='use_limit_rp']").bootstrapSwitch();

            $("[name='use_timer']").on('switchChange.bootstrapSwitch', function(event, state) {
                $("[name='timer_start']").prop('disabled', !state);
                $("[name='timer_end']").prop('disabled', !state);
            });
            $("[name='use_limit_rp']").on('switchChange.bootstrapSwitch', function(event, state) {
                $("[name='reach_limit_rp']").prop('disabled', !state);
            });

            $("#quest").click(function(){
                $("#ajaxToast .toast-body").html("Nomor Hp Digunakan Untuk Mengirim Notifikasi Limit Ke Anda");

                $("#ajaxToast").toast('show');
            });
        });
    </script>
@endsection