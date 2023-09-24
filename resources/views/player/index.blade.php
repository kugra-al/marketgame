@extends('layouts.app')

@section('title')
    Player List
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

    @if(Auth::user())
        @php($player = Auth::user()->player)
        @if (!$player)
            <a href="/player/create">Create player</a>
        @endif
    @endif
    @if(isset($players) && sizeof($players))
        <table class="table task-table" id="players">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    @role('admin')<th>User</th>@endrole
                    <th>City</th>
                    
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($players as $player)
                <tr player-id="{{ $player->id }}" class="player">
                    <td>{{ $player->id }}</td>
                    <td><a href="/player/{{ $player->id }}">{{ $player->name }}</a></td>
                    @role('admin')<td><a href="{{ route('user.show', $player->user->id) }}">{{ $player->user->email }}</a></td>@endrole
                    <td><a href="/city/{{ $player->city->id }}">{{ $player->city->name }}</a></td>
                    <td>
                        <form action="{{ route('player.destroy', $player->id) }}" method="POST" onsubmit="return confirmForm(this)">
                            <input type="hidden" name="id" value="{{ $player->id }}">
                            <button class="btn btn-danger">Delete</button>
                            @csrf
                            @method('DELETE')
                        </form>
                </tr>
            @endforeach
        </table>
    @else
        There are no players
    @endif         
@endsection

@section('on-load')
    
@endsection