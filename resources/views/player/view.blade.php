@extends('layouts.app')

@section('title')
    @if(isset($player))
    	{{ $player->name }}
    @else
    	Player
    @endif
@endsection

@section('content')
    @if(isset($player) && $player)
       <ul>
            <li><strong>Name</strong> {{ $player->name }}</li>
            @php($city = $player->city)
            @if($city)
                <li><strong>City</strong> <a href="{{ route('city.view',$city->id) }}">{{ $city->name }}</a></li>
            @endif
        </ul>
    @else
        No player found
    @endif
@endsection

