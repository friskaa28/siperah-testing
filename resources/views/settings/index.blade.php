@extends('layouts.app')

@section('title', 'Pengaturan Fitur')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pengaturan Fitur Aplikasi</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kontrol Fitur</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fitur</th>
                                <th>Deskripsi</th>
                                <th style="width: 150px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $setting)
                                <tr>
                                    <td style="font-weight: 500;">
                                        @if($setting->key == 'feature_produksi') <i class="fas fa-cow"></i> Input Produksi
                                        @elseif($setting->key == 'feature_distribusi') <i class="fas fa-truck"></i> Input Distribusi
                                        @elseif($setting->key == 'feature_notifikasi') <i class="fas fa-bell"></i> Notifikasi
                                        @else {{ $setting->key }}
                                        @endif
                                    </td>
                                    <td>{{ $setting->description }}</td>
                                    <td style="text-align: center;">
                                        <div class="custom-control custom-switch">
                                            <label class="switch">
                                                <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value == '1' ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Toggle Switch CSS */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: var(--primary);
}

input:focus + .slider {
  box-shadow: 0 0 1px var(--primary);
}

input:checked + .slider:before {
  -webkit-transform: translateX(24px);
  -ms-transform: translateX(24px);
  transform: translateX(24px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
@endsection
