@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4 text-center"><img src="{{ asset('brand/icono-tiendasiete.png') }}" alt="Tienda Siete" style="height:72px;width:72px;object-fit:contain"></div>
                    <p class="text-uppercase small fw-bold text-primary mb-2">Administración</p>
                    <h1 class="h3 fw-bold mb-4">Ingresar al panel</h1>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recordar sesión</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
