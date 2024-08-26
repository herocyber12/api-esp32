@extends('layouts.app')
@section('content')
    <div class="contianer">
        <div class="row vh-100">
            <div class="col-xl-12">
                
                <div class="text-center align-content-center">
                @php
    $bg = '';
    $relayStatus = '';
    $checked = false;
@endphp
@if ($relay->state === "on")
    @php
        $bg = "success";
        $relayStatus = "Matikan Lampu";
        $lamp = "img/hidup.png";
        $val = "off";
        $checked = true;
    @endphp
@elseif ($relay->state === "off")
    @php
        $bg = "danger";
        $relayStatus = "Hidupkan Lampu";
        $lamp = "img/mati.png";
        $val = "on";
        $checked = false;
    @endphp
@endif

<img src="{{ asset($lamp) }}" class="img-fluid w-25 mb-3" alt="">
<form action="">
    
    <input type="checkbox" id="lampSwitch" data-toggle="toggle" data-on="Lampu Nyala" data-off="Lampu Mati" data-onstyle="success" data-offstyle="danger" @if($checked) checked @endif>
</form>

                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#lampSwitch').bootstrapSwitch();
        $('#lampSwitch').on('switchChange.bootstrapSwitch', function(event, state) {
            var status = state ? 'on' : 'off';

            $.ajax({
                url: '{{ url("/api/relay-control") }}',  // Sesuaikan dengan endpoint Anda
                method: 'POST',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'  // Menambahkan CSRF token untuk keamanan
                },
                success: function(response) {
                    console.log('Status relay berhasil diperbarui: ' + response);
                    // Jika Anda ingin memperbarui gambar berdasarkan status
                    var lampImage = state ? 'img/hidup.png' : 'img/mati.png';
                    $('img').attr('src', '{{ asset("") }}' + lampImage);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan: ' + error);
                }
            });
        });
    });
</script>

@endsection