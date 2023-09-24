@extends('layouts.admin')

@section('title')
    Admin Game
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
    <div class="form-group">
        <form method="POST" action="/admin/game">
            @csrf
            <input type="hidden" name="processTick" value="true">
            <button type="submit" class="btn btn-warning" value="Process Next Tick">Process Next Tick</button>
        </form>     
    </div>

    <div class="form-group">
        <form method="POST" action="/admin/game" onsubmit="return confirmForm(this)">
            @csrf
            <div class="input-group">
                <div class="col-sm-3">
                    <input type="hidden" name="addGold" value="true">
                    <select name="player_id" class="form-control">
                        <option>Select player..</option>
                        @if (isset($players))
                            @foreach($players as $id=>$name)
                                <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-sm-3">
                    <select name="item_id" class="form-control">
                        <option>Select item..</option>
                        @if(isset($items))
                            @foreach($items as $id=>$name)
                                <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="text" name="amount" placeholder = "Enter Amount" class="form-control">
                </div>
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" value="Add Gold">Add Gold</button>
                </div>
            </div>
        </form>  
    </div>

    <div class="form-group">
        <form method="POST" action="/admin/game" onsubmit="return confirmForm(this)">
            @csrf
            <div class="col-sm-6">
                <input type="hidden" name="addNpcPlayer" value="true">
                <input type="text" class="form-control" name="qty" value="1">
            </div>
            <div class="col-sm-6">
                <button type="submit" class="btn btn-warning">Add NPC Player</button>
            </div>
        </form>     
    </div>

    <div class="form-group">
        <form method="POST" action="/admin/game" onsubmit="return confirmForm(this)">
            @csrf
            <input type="hidden" name="resetGame" value="true">
            <button type="submit" class="btn btn-warning" value="Process Next Tick">Reset Game</button>
        </form>     
    </div>

    <div class="form-group">
        <form method="POST" action="/admin/game" onsubmit="return confirmForm(this)">
            @csrf
            <input type="hidden" name="unusedLocation" value="true">
            <button type="submit" class="btn btn-warning" value="Process Next Tick">Get unused location</button>
        </form>     
    </div>

    <div class="form-group">
        <form method="POST" action="/admin/game" onsubmit="return confirmForm(this)">
            @csrf
            <div class="input-group">
                <div class="col-sm-3">
                    <select name="army_id" class="form-control">
                        <option>Select army..</option>
                        @if(isset($armies))
                            @foreach($armies as $id=>$name)
                                <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-sm-3">
                    <select name="caravan_id" class="form-control">
                        <option>Select caravan..</option>
                        @if(isset($caravans))
                            @foreach($caravans as $id=>$name)
                                <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-sm-3">
                    <label>Skip Apply result</label>
                    <input type="checkbox" name="skipApplyResults"  checked="checked">
                </div>
                <div class="col-sm-3">
                    <input type="hidden" name="testBattle" value="true">
                    <button type="submit" class="btn btn-warning" value="Process Next Tick">Calc battle result</button>
                </div>
            </div>
        </form>     
    </div>

    <div class="form-group">
        <form method="POST" action="/admin/game">
            @csrf
           
            <button type="submit" class="btn btn-warning" value="Process Next Tick">Diff Button</button>
        </form>       
    </div>
@endsection
