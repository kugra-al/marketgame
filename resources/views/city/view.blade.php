@extends('layouts.app')

@section('title')
    @if(isset($city))
    	{{ $city->name }}
    @else
    	City
    @endif
@endsection

@section('css')
    #buildingTable tr.level-0 { background: #b5efb5; }
    .card-building .card-header:first-child { background: #ffd0b4; }
    .level { max-height: 120px; }
    .level-1 { background:#87ea87;}
    .level-2 { background: #87eac1; }
    .level-3 { background: #87d3ea; }
    .level-max { background: #e4ea87; border: 1px solid #c50b0b;}
    .level-max td { border-top: 1px solid #c50b0b; border-bottom: 1px solid #c50b0b;}
    .upgrading { background: #ebf5c5 !important;}
    .level-text { font-weight: bold; font-size: 32px;}
    .rewardSection .card { background: gold; }
    .buildingRecipes .card { background: #c3c3ff; }
    .buildingRecipes .card .list-group-item { background: #d3d3ff}
@endsection

@section('content')
    @if(isset($city) && $city)
        @php($nearest = $city->getNearestCity())
        @if($nearest)
            <h5>Nearest City</h5>
            {{ $nearest->name }} - {{ $nearest->distance }}
        @endif
        @php($myCity = false)
        @if(Auth::user() && Auth::user()->player)
            @php($myCity = Auth::user()->player->id === $city->player_id)
        @endif
        @php($nextLevel = $city->level + 1)
        <div class="row @if($city->upgrade_ticks_remaining) upgrading @endif">
            <div class="col-sm-4">
            	<ul>
                    <li><strong>Level</strong> <span class="level">{{ $city->level }}</span></li>
            		<li><strong>Name</strong> {{ $city->name }}</li>
            		<li><strong>Population</strong> {{ $city->population }} (<span class="resource resource-workers">{{ $city->getWorkingPopulation() }} working)</span></li>
            		<li><strong>Owner</strong> <a href="{{ route('player.show',$city->player_id) }}">{{ $city->player->name }}</a></li>
            		<li><strong>Position</strong> X: {{ $city->location->x }} Y: {{ $city->location->y }}</li>
                    <li><strong>Level</strong> {{ $city->level }}</li>
                    @if($city->upgrade_ticks_remaining)<li><span class="event event-ticks">{{ $city->upgrade_ticks_remaining }} ticks until upgrade</span></li>@endif
            	</ul>
                @if($myCity && !$city->upgrade_ticks_remaining && $nextLevel)
                    <form action="{{ route('city.upgrade',['id'=>$city->id]) }}" method="POST" onsubmit="return confirmForm(this)">
                        @csrf
                        <button class="btn btn-success">Upgrade</button>
                    </form>
                @endif
                @if($nextLevel)
    
                    <h4>Cost</h4>
                    <ul>

                    @foreach($city->costs as $cost)
                        <li><span class="item item-{{ $cost->item->name }}">{{ $cost->item->name }} = {{ $cost->qty }}</span></li>
                    @endforeach
                    </ul>
                @else
                    Max level
                @endif
            </div>
            <div class="col-sm-8">
                <h3>Inventory</h3>
                @php($items = $city->items)
                <table id="items" city-id="{{ $city->id }}" player-id="{{ $city->player->id }}" class="table task-table">
                	<thead>
                		<th>Item</th>
                		<th>Qty</th>
                	</thead>
                	<tbody>
                @foreach($items as $i)
                	<tr>
                		<td class="item item-{{ $i->item->slug }}">{{ $i->item->name }}</td>
                		<td data-city-id="{{ $city->id }}" data-item-id="{{ $i->item_id }}" class="{{ $i->item->slug }}">{{ $i->qty }}</td>
                	</tr>
                @endforeach
                	</tbody>
                </table>
            </div>
        </div>
        <div class="row">
        @if($city->player_id)
            <div class="row">
                <h5>Transports</h5>
                @php($cityTransports = $city->transports)
                @if(sizeof($cityTransports))
                    <table class="table task-table">
                        <thead>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Total capacity</th>
                        </thead>
                        <tbody>
                    @foreach($cityTransports as $transport)
                        <tr>
                            <td>{{ $transport->transport->name }}</td>
                            <td data-city-id="{{ $city->id }}" data-transport-id="{{ $transport->transport_id }}">{{ $transport->qty }}</td>
                            <td>{{ $transport->qty*$transport->transport->capacity }}</td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                @else
                    You have no transports
                @endif
            </div>
            <div class="row">
                <h5>Troops</h5>
                @php($cityTroops = $city->troops)
                @if(sizeof($cityTroops))
                    <table class="table task-table">
                        <thead>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Combat</th>
                        </thead>
                        <tbody>
                    @foreach($cityTroops as $troop)
                        <tr>
                            <td>{{ $troop->troop->name }}</td>
                            <td data-city-id="{{ $city->id }}" data-troop-id="{{ $troop->troop_id }}">{{ $troop->qty }}</td>
                            <td>
                                <ul>
                                    <li>attack: {{ $troop->troop->attack }}</li>
                                    <li>defense: {{ $troop->troop->defense }}</li>
                                    <li>ranged: {{ $troop->troop->ranged }}</li>
                            </td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                @else
                    You have no troops
                @endif
            </div>

            <div class="container mt-2">
                <div class="card">
                    <div class="card-header">
                        Buildings
                    </div>
                    <div class="card-body">
                    	@php($buildings = $city->buildings)
                        @if($myCity)
                            <form action="{{ route('city.building.create') }}" method="POST" onsubmit="var i = this.querySelector('select');return confirmForm(this,'Are you sure you want to build this building? It will cost '+i.options[i.selectedIndex].getAttribute('building-cost'))">
                                @csrf
                                <div class="form-group form-row">
                                    <label class="col-sm-3" for="id">New Building</label>
                                    <input type="hidden" name="city_id" value="{{ $city->id }}">
                                    <select name="id" class="col-sm-6 form-control">
                                        <option>Select building..</option>
                                        @foreach($baseBuildings as $b)
                                            <option value="{{ $b->id }}" building-cost="{{ serialize($b->costs) }}">{{ $b->name }}</option>
                                        @endforeach
                                    </select>                    
                                    <button class="col-sm-3 btn btn-success">Build</button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                    	@if($buildings)
                    		@foreach($buildings as $building)

                                @php($level = $building->level->level)
                                @php($rewards = $building->getRewards())

                                @php($levelClass = "level-".$level)
                                <div class="col-sm-6 mt-2">
                        			<div class="card h-100 card-building">
                                        <div class="card-header">
                                            {{ $building->building->name }}
                                        </div>
                                        <div class="card-body level {{ $levelClass }} @if($building->upgrade_ticks_remaining) upgrading @endif">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label>Level</label> <span class="level-text">{{$level}}</span>
                                                    @if($myCity && !$building->upgrade_ticks_remaining)
                                                        <form method="POST" action="{{ route('city.building.upgrade') }}" onsubmit="return confirmForm(this)">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $building->id }}">
                                                            <input type="hidden" name="action" value="upgrade">
                                                            <button class="btn btn-success">Upgrade</button>
                                                        </form>
                                                    @endif
                                            
                                                    @if($building->upgrade_ticks_remaining)
                                                        <li class="list-group-item">
                                                        (upgrading - <span class="event event-ticks">{{ $building->upgrade_ticks_remaining}} ticks left</span>)
                                                        </li>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Workers</label> <span class="workers">{{ $building->current_workers }}</span>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="card-body">
                                            @if($rewards && $rewards->count())
                                                <div class="row mt-2 rewardSection">
                                                    <div class="card-title">Rewards per tick</div>
                                                    @foreach($rewards as $reward)
                                                        @if($reward->item)
                                                        <div class="col-sm-4">
                                                            <div class="card">
                                                                <div class="card-header">{{ $reward->item->name }}</div>
                                                                <div class="card-body">
                                                                    {{ $reward->qty }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($building->level->recipes && $building->level->recipes->count())
                                            <div class="row mt-2 buildingRecipes">
                                                <div class="card-title">Crafting</div>
                                            
                                                @foreach($building->level->recipes as $recipe)
                                                    <div class="col-sm-4">
                                                        <div class="card h-100">
                                                            <div class="card-header">
                                                                1 {{ $recipe->recipe->item->name }} every {{ $recipe->recipe->ticks }} ticks
                                                            </div>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item">Requires:</li>
                                                                @foreach($recipe->recipe->items as $i)
                                                                    <li class="list-group-item recipe-item">{{ $i->qty }} {{ $i->item->name }}</li>
                                                                @endforeach
                                                                <li class="list-group-item">
                                                                    <button class="btn btn-success">Craft</button>
                                                                </li>
                                                            </ul>
                                                            <form action="{{ route('city.craft.item') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="city_building_id" value="{{ $building->id }}">
                                                                <input type="hidden" name="item_recipe_id" value="{{ $recipe->recipe->id }}">
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @endif
                                            @if($building->crafts && $building->crafts->count())
                                            <div class="row mt-2 buildingCrafts">
                                                <div class="card-title">Crafting Progress</div>
                                                <table class="table task-table">
                                                    <thead>
                                                        <th>Item</th>
                                                        <th>Ticks</th>
                                                        <th>Qty</th>
                                                        <th>Workers</th>
                                                        <th></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($building->crafts as $craft)

                                                            <tr>
                                                                <td>{{ $craft->recipe->item->name }}</td>
                                                                <td>{{ $craft->ticks_remaining }}/{{ $craft->ticks }}</td>
                                                                <td>{{ $craft->qty }}</td>
                                                                <td>{{ $craft->workers }}</td>
                                                                <td>
                                                                    <form action="{{ route('city.craft.edit') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="id" value="{{ $craft->id }}">
                                                                        <input type="hidden" name="workers" value="{{ $craft->workers }}">
                                                                        <input type="hidden" name="qty" value="{{ $craft->qty }}">
                                                                        <button class="btn btn-info" type="submit">E</button>
                                                                    </form>
                                                                </td>
                                                                <td>
                                                                    <form action="{{ route('city.craft.delete') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="id" value="{{ $craft->id }}">
                                                                        <button class="btn btn-danger" type="submit">X</button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                
                                            </div>
                                            @endif



                                         
                                            @if($building->level->troops && $building->level->troops->count())
                                                <div class="row mt-2">
                                                    <div class="card-title">Train troop</div>
                                                    @foreach($building->level->troops as $troop)
                                                        <div class="col-sm-6">
                                                            <div class="card">
                                                                <div class="card-header">{{ $troop->troop->name }}</div>
                                                                <div class="card-body">
                                                                    <div class="card-title">Cost</div>
                                                                    <div class="row">
                                                                        @foreach($troop->cost as $cost)
                                                                            <div class="col-sm-12">
                                                                                <div class="card">
                                                                                    <div class="card-header">{{ $cost->item->name }}</div>
                                                                                    <div class="card-body">
                                                                                        {{ $cost->qty }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="row">
                                                                        <form action="{{ route('city.buy.troop') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="city_building_id" value="{{ $building->id }}">
                                                                            <input type="hidden" name="troop_id" value="{{ $troop->id }}">
                                                                            <button class="btn btn-success">Hire</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($building->level->transports && $building->level->transports->count())
                                                <div class="row mt-2">
                                                    <div class="card-title">Train transport</div>
                                                    @foreach($building->level->transports as $transport)
                                                        <div class="col-sm-6">
                                                            <div class="card">
                                                                <div class="card-header">{{ $transport->transport->name }}</div>
                                                                <div class="card-body">
                                                                    <div class="card-title">Cost</div>
                                                                    <div class="row">
                                                                        @foreach($transport->cost as $cost)
                                                                            <div class="col-sm-12">
                                                                                <div class="card">
                                                                                    <div class="card-header">{{ $cost->item->name }}</div>
                                                                                    <div class="card-body">
                                                                                        {{ $cost->qty }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="row">
                                                                        <form action="{{ route('city.buy.transport') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="city_building_id" value="{{ $building->id }}">
                                                                            <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                                                            <button class="btn btn-success">Hire</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>        
                    		@endforeach
                    	@endif
                    </div>
                </div>
            </div>

            


            <div class="container mt-2">
                <div class="card">
                    <div class="card-header">City Log</div>
                    <div class="card-body">
                        <table class="table task-table">
                            <thead>
                                <th>Msg</th>
                                <th>Type</th>
                                <th>status</th>
                            </thead>
                            <tbody>
                        	@foreach($city->statuses as $status)
                                @php($class = '')
                                @if($status->status == "start")
                                    @php($class = 'warning')
                                @endif
                                @if($status->status == "complete")
                                    @php($class = 'success')
                                @endif
                                @if($status->status == "update")
                                    @php($class = "info")
                                    @continue
                                @endif
                                <tr @if($class) class="table-{{ $class }}" @endif>
                                    <td>{{ $status->msg }}</td>
                                    <td>{{ $status->type }}</td>
                                    <td>{{ $status->status }}</td>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        <div style="border:1px solid red; color: red">Updates are hidden</div>
                    </div>
                </div>
            </div>

            <div class="container mt-2">
                <div class="card">
                    <div class="card-header">Caravans</div>
                    <div class="card-body">
                        <table class="table task-table">
                            <thead>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>City</th>
                                <th>Transport</th>
                                <th>Items</th>
                            </thead>
                            <tbody>
                        @foreach($city->caravans as $c)
                                <tr>
                                    <td>outgoing</td>
                                    <td>{{ $c->locationFrom->id }}</td>
                                    <td>{{ $c->locationTo->id }}</td>
                                    <td>{{ $c->city->name }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                        @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
        @endif
    @else
        No city found
    @endif

    <div style="display:none">
        @include('city.craft.form')
    </div>
@endsection

@section('on-load')
    let items = document.querySelector("table#items");
    let cityID = items.getAttribute('city-id');

    function updateCity(data) {
        console.log(data);
        console.log("city update");
    }

    window.Echo.channel('cities.'+cityID)
        .listen('.city-update', (e) =>
        {
            $.when(api.get('/city/data/'+cityID)).done(function(data){
                if (data && data.items) {
                    console.log(data.items);
                    $.each(data.items,function(id,item){
                        console.log(item);
                        flashUpdate("[data-city-id='"+data.id+"'][data-item-id='"+item.item_id+"']", item.qty);
                    })
                }
            });

            //console.log(e);
            //if (e.city && e.city.items) {
            //    console.log(e.city.items);
            //    $.each(e.city.items,function(id,qty){
            //        flashUpdate("[data-city-id='"+e.city.id+"'][data-item-id='"+id+"']", qty);
            //    })
            //}
        });
  
    $('.buildingRecipes .card button').click(function(e){
        var recipeItems = $(this).parents('.card').find('.recipe-item');
        var form = $(this).parents('.card').find('form');
        recipeItems = $.map(recipeItems,function(r){return r.innerText}).join(", ");

        var workers = $(this).parents('.card-building').find('.workers').text();
        var buildingId = $(form).find('[name=city_building_id]').val();
        var recipeId = $(form).find('[name=item_recipe_id]').val();
        var craftForm = $('#craftRecipeForm').clone();
        $(craftForm).find('[name=city_building_id]').val(buildingId);
        $(craftForm).find('[name=item_recipe_id]').val(recipeId);
        $(craftForm).find('[name=workers]').attr('max',workers);
        console.log(workers);
            showModal({
                title: "Are you sure you want to craft the thing?",
               // text: "Are you sure you want to craft this? It will cost:<br> "+recipeItems,
               //text: d.form,
               html: craftForm,
                func: function(){ $(craftForm).submit(); console.log('ok'); }
            });  
    });

    $('.buildingCrafts .btn-info').click(function(e){
        var craftForm = $('#craftRecipeForm').clone();
        e.preventDefault();
        var form = $(this).parents('form');
        $(craftForm).attr('action',$(form).attr('action'));
        $(craftForm).find('[name=craft_id]').val($(form).find('[name=id]').val());

        var buildingWorkers = $(this).parents('.card-building').find('.workers').text();
        var currentWorkers = $(form).find('[name=workers]').val();
        var currentQty = $(form).find('[name=qty]').val();
        $(craftForm).find('[name=workers]').attr('max',buildingWorkers);
        $(craftForm).find('[name=workers]').val(currentWorkers);
        $(craftForm).find('[name=qty]').val(currentQty);
        console.log(craftForm);
            showModal({
                title: "Are you sure you want to edit the craft?",
               // text: "Are you sure you want to edit this craft",
               html: craftForm,
                func: function(){ $(craftForm).submit(); console.log('ok'); }
            }); 
    });
@endsection