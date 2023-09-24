@extends('layouts.app')

@section('title')
    Create Player
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('player.store') }}">
        @csrf
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Enter Player Name">
            <input type="text" name="city_name" class="form-control" placeholder="Enter City Name">
            <button class="btn btn-success" type="submit">Go</button>
        </div>
    </form>        
@endsection