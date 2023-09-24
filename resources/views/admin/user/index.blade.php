@extends('layouts.admin')

@section('title')
    Admin Users
@endsection
@section('content')
    @if(isset($users) && $users)
        <table class="table task-table">
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Player</th>
                <th>Created at</th>
                <th>Updated at</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td><a href="{{ route('user.show', $u->id) }}">{{ $u->id }}</a></td>
                        <td><a href="{{ route('user.show', $u->id) }}">{{ $u->name }}</a></td>
                        <td><a href="{{ route('user.show', $u->id) }}">{{ $u->email }}</a></td>
                        <td>
                            @php($player = $u->player)
                            @if($player)
                                <a href="{{ route('player.show', $u->id) }}">{{ $player->name }}</a>
                            @endif
                        </td>
                        <td>{{ $u->created_at }}</td>
                        <td>{{ $u->updated_at }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        No users found
    @endif
@endsection
