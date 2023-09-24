@extends('layouts.app')

@section('title')
    Market
@endsection

@section('css')
    .market-orders { height: 500px; overflow-x: scroll; }
@endsection
@section('content')
    @if(isset($items))
        <div class="row">
            
            <h5>My Items</h5>

            @foreach(['sell','buy'] as $type)
                @php($otherType = 'buy')
                @if($type == 'buy')
                    @php($otherType = 'sell')
                @endif
                <div class="col-sm-6">

                    <div class="row">
                        <form class="{{ $type }}ItemForm" method="POST" action="{{ route('market.order') }}" onsubmit="return confirmForm(this,'Are you sure you want to {{ $type }} this item','{{ $type }} items')">
                            @csrf
                            <div class="form-row">
                                <div class="col-sm-3">
                                    <label>Item</label>
                                    <select name="item_id" class="form-control">
                                        @foreach($items as $i)
                                            @if($i->item->tradeable)
                                                <option value="{{ $i->item_id }}">{{ $i->item->name }} = {{ $i->qty }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Qty</label>
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <input type="hidden" name="item_id" value="{{ $i->item_id }}">
                                    <input name="qty" type="text" class="form-control" value="1">
                                </div>
                                <div class="col-sm-3">
                                    <label>Cost per Item</label>
                                    <input type="text" name="cost" class="form-control" value="{{ $i->item->base_cost }}">
                                </div>
                                <div class="col-sm-3">
                                    <label>&nbsp;</label>
                                    <button class="form-control btn btn-success">{{ $type }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                            
  
                    <div class="row"> 
                        @php($orderItems = $orders->where('type',$type))
                        @if(sizeof($orderItems))
                            <h5>{{ $type }} orders</h5>
                            <div class="market-orders">
                                <table class="table task-table">
                                    <thead>
                                        <th>id</th>
                                        <th>
                                            @if($type=="sell")
                                                City from
                                            @else
                                                City To
                                            @endif
                                        </th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Weight</th>
                                        <th>Cost</th>
                                        <th>Distance</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                    @php($playerCity = Auth::user()->player->city)
                                    @foreach($orderItems as $order)
                                        @php($myOrder = false)
                                        @if($type == "sell")
                                            @if($order->city_from->player->id == Auth::user()->player->id)
                                                @php($myOrder = true)
                                            @endif
                                        @elseif($type == "buy")
                                            @if($order->city_to->player->id == Auth::user()->player->id)
                                                @php($myOrder = true)
                                            @endif
                                        @endif
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>
                                                @if($type == "sell" && $order->city_from)<a href="{{ route('city.view', $order->city_id_from) }}">{{ $order->city_from->name }}</a>@endif
                                                @if($type == "buy" && $order->city_to)<a href="{{ route('city.view', $order->city_id_to) }}">{{ $order->city_to->name }}</a>@endif
                                            </td>
                                            <td>
                                                @php($item = $order->items->first())
                                                <span class="item item-{{ $item->item->slug }}">{{ $item->item->name }}</span>
                                            </td>
                                            <td>
                                                {{ $item->qty }}
                                            </td>
                                            <td>
                                                {{ $item->item->weight*$item->qty }}
                                            </td>
                                            <td>
                                                {{ $item->cost }}
                                            </td>
                                            <td>
                                                @if($playerCity && $type == "sell" && $order->city_from)
                                                    {{ $playerCity->calculateDistanceTo($order->city_from) }}
                                                @endif      
                                                @if($playerCity && $type == "buy" && $order->city_to)
                                                    {{ $playerCity->calculateDistanceTo($order->city_to) }}
                                                @endif                                       
                                            </td>
                                            <td>
                                                @if($myOrder)
                                                    <form method="POST" action="{{ route('market.order.delete', ['id'=>$order->id]) }}" onsubmit="return confirmForm(this,'Are you sure you want to cancel this order?')">
                                                        @csrf
                                                        <button class="btn btn-danger">Cancel</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('market.order.accept', ['id'=>$order->id]) }}" onsubmit="return confirmForm(this,'Are you sure you want to {{ $otherType }} this order?')">
                                                        @csrf
                                                        <button class="btn btn-success">{{ $otherType }}</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                No {{ $type }} orders found
                            @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <div class="row">
        
        @if($caravans->count())
            <h5>Caravans</h5>
            <table class="table task-table">
                <thead>
                    <th>ID</th>
                    <th>Location From</th>
                    <th>Location To</th>
                    <th>Distance</th>
                    <th>Ticks</th>
                    <th>City</th>
                    <th>Transports</th>
                    <th>Items</th>
                    <th>Returning</th>
                    <th></th>
                </thead>
                <tbody>
            @foreach($caravans as $caravan)
                <tr>
                    <td>{{ $caravan->id }}</td>
                    <td><a href="#">{{ $caravan->locationFrom->id }}</a></td>
                    <td><a href="#">{{ $caravan->locationTo->id }}</a></td>
                    <td>{{ $caravan->locationFrom->calculateDistanceTo($caravan->locationTo) }}</td>
                    <td>{{ $caravan->ticks_remaining }}/{{ $caravan->ticks }}</td>
                    <td><a href="{{ route('city.view', $caravan->city->id) }}">{{ $caravan->city->name }}</a></td>
                    <td>
                        @foreach($caravan->transports as $transport)
                            <li>{{ $transport->qty }} {{ Game::plural($transport->transport->name,$i->qty) }} ({{ $transport->transport->speed }} speed)</li>
                        @endforeach
                    </td>
                    <td>
                        @foreach($caravan->items as $i)
                            <li>{{ $i->qty }} {{ Game::plural($i->item->name,$i->qty) }}</li>
                        @endforeach
                    </td>
                    <td>{{ $caravan->returning }}</td>
                    <td></td>
                </tr>
            @endforeach
                </tbody>
            </table>
        @else
            No caravans found
        @endif
    </div>
@endsection

@section('on-load')

@endsection

@section('javascript')

@endsection
