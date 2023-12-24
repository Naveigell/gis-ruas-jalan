@extends('layouts.app')

@section('section')
    <div class="container" style="margin-top: 10%;">
        <form action="{{ route('login.store') }}" method="post">
            @csrf
            <h3>Login kedalam sistem GIS ruas jalan</h3>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email ..">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password ..">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <br>
            <span class="text-danger text"><a href="{{ route('register.index') }}" class="">Register</a></span>
            <br>
            <br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
