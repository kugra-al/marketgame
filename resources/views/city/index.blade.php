@extends('layouts.app')

@section('title')
    City
@endsection

@section('content')
    @if(isset($cities) && $cities)
        {{ $cities->links() }}
        <table class="table task-table">
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Player</th>
                <th>Tick Created</th>
                <th>Age</th>
                <th>Location</th>
                <th>Influence</th>
                <th>Population</th>
                <th>Workers</th>
                <th>Army</th>
                <th>Nearest City</th>
            </thead>
            <tbody>
            @foreach($cities as $city)
                <tr>
                    <td>{{ $city->id }}</td>
                    <td><a href="{{ route('city.view', $city->id) }}">{{ $city->name }}</a></td>
                    <td>{{ $city->player_id }}</td>
                    <td>{{ $city->tick_created }}</td>
                    <td>{{ $city->getCityAge() }}</td>
                    <td>X: {{ $city->location->x }} Y: {{ $city->location->y }}</td>
                    <td>{{ $city->influence() }}</td>
                    <td>{{ $city->population }}</td>
                    <td>{{ $city->getWorkingPopulation() }}</td>
                    <td>
                        <ul>
                            <li>Size: {{ $city->getArmySize() }}</li>
                            @php($army = $city->getArmy())
                            <li>Attack: {{ $army['attack'] }}</li>
                            <li>Defense: {{ $army['defense'] }}</li>
                        </ul>
                    </td>
                    <td>
                        @php($nearest = $city->getNearestCity())
                        @if($nearest)
                            {{ $nearest->name }} - {{ $nearest->distance }}
                        @endif
                    </td>
                </tr> 
            @endforeach
            </tbody>
        </table>
    @else
        No city found
    @endif
   
@endsection