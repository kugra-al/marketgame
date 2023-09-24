@extends('layouts.admin')

@section('title')
    Admin Buildings
@endsection
@section('content')
    @if(isset($buildings) && $buildings)
        <table class="table task-table">
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Level</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($buildings as $b)
                    <tr>
                        <td><a href="{{ route('admin.building.show', $b->id) }}">{{ $b->id }}</a></td>
                        <td><a href="{{ route('admin.building.show', $b->id) }}">{{ $b->name }}</a></td>
                        <td>
                            <table class="table task-table">
                                <thead>
                                    <th>ID</th>
                                    <th>Level</th>
                                    <th>Ticks</th>
                                    <th>Workers</th>
                                    <th>Costs</th>
                                    <th>Rewards</th>
                                    <th>Transports</th>
                                    <th>Troops</th>
                                    <th>Recipes</th>
                                    <th></th>
                                </thead>
                                <tbody>
                                @foreach($b->levels as $level)
                                    <tr>
                                        <td>{{ $level->id }}</td>
                                        <td>{{ $level->level }}</td>
                                        <td>{{ $level->ticks }}</td>
                                        <td>{{ $level->workers }}</td>
                                        <td>
                                            @if($level->cost && $level->cost->count())
                                                <table class="table task-table">
                                                    <thead>
                                                        <th>Item</th>
                                                        <th>Qty</th>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($level->cost as $cost)
                                                        <tr>
                                                            <td>{{ $cost->item->name }}</td>
                                                            <td>{{ $cost->qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                        <td>
                                            @if($level->reward && $level->reward->count())
                                                <table class="table task-table">
                                                    <thead>
                                                        <th>Item</th>
                                                        <th>Qty</th>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($level->reward as $reward)
                                                        <tr>
                                                            <td>{{ $reward->item->name }}</td>
                                                            <td>{{ $reward->qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                        <td>
                                            @if($level->transports && $level->transports->count())
                                                <table class="table task-table">
                                                    <thead>
                                                        <th>Item</th>
                                                        <th>Cost</th>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($level->transports as $transport)
                                                        <tr>
                                                            <td>{{ $transport->transport->name }}</td>
                                                            <td>
                                                                <table class="table task-table">
                                                                    <thead>
                                                                        <th>Item</th>
                                                                        <th>Qty</th>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($transport->cost as $cost)
                                                                        <tr>
                                                                            <td>{{ $cost->item->name }}</td>
                                                                            <td>{{ $cost->qty }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                        <td>
                                            @if($level->troops && $level->troops->count())
                                                <table class="table task-table">
                                                    <thead>
                                                        <th>Item</th>
                                                        <th>Cost</th>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($level->troops as $troop)
                                                        <tr>
                                                            <td>{{ $troop->troop->name }}</td>
                                                            <td>
                                                                <table class="table task-table">
                                                                    <thead>
                                                                        <th>Item</th>
                                                                        <th>Qty</th>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($troop->cost as $cost)
                                                                        <tr>
                                                                            <td>{{ $cost->item->name }}</td>
                                                                            <td>{{ $cost->qty }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                        <td>
                                            @if($level->recipes && $level->recipes->count())
                                                <table class="table task-table">
                                                    <thead>
                                                        <th>Item</th>
                                                        <th>Cost</th>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($level->recipes as $recipe)
                                                        
                                                        <tr>
                                                            <td>{{ $recipe->recipe->item->name }}</td>
                                                            <td>
                                                                <table class="table task-table">
                                                                    <thead>
                                                                        <th>Item</th>
                                                                        <th>Qty</th>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($recipe->recipe->items as $item)
                                                                        <tr>
                                                                            <td>{{ $item->item->name }}</td>
                                                                            <td>{{ $item->qty }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                        <td><a class="btn btn-info" href="{{ route('admin.building.edit', $b->id) }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        No buildings found
    @endif
@endsection
