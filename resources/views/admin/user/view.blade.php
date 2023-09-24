@extends('layouts.admin')

@section('title')
    Admin Users
@endsection
@section('content')
    @if(isset($user) && $user)
        <ul>
            <li>{{ $user->id }}</li>
            <li>{{ $user->name }}</li>
            <li>{{ $user->email }}</li>
            <li>{{ $user->player }}</li>
        </ul>
    @else
        User not found
    @endif
@endsection
