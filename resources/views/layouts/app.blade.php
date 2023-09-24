<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/yeti.min.css') }}" rel="stylesheet">
    <style>
        @yield('css')
    </style>
</head>
<body>
    @php($user = false)
    @php($player = false)
    @if(Auth::user())
        @php($user = Auth::user())
        @php($player = $user->player)
    @endif
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark" id="navbar-main">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Players
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                @if($player)
                                    <a class="dropdown-item" href="{{ route('player.show',['player'=>$player->id]) }}">
                                        {{ __('My Player') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif
                                <a class="dropdown-item" href="{{ route('player.index') }}">
                                    {{ __('Player List') }}
                                </a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Cities
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                @if($player)
                                    @php($city = $player->city)
                                    @if($city)
                                        <a class="dropdown-item" href="{{ route('city.view',['id'=>$city->id]) }}">
                                            {{ __('My City') }}
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                @endif
                                <a class="dropdown-item" href="{{ route('city.index') }}">
                                    {{ __('City List') }}
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('map') }}">{{ __('Map') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('market') }}">{{ __('Market') }}</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        @role('admin')
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Admin
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/admin/game">
                                        {{ __('Game Control') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('user.index') }}">
                                        {{ __('Users') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('admin.item.index') }}">
                                        {{ __('Items') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('admin.building.index') }}">
                                        {{ __('Buildings') }}
                                    </a>
                                </div>
                            </li>
                        @endrole

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ $user->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @if($user)
            <nav class="navbar navbar-expand-sm navbar-dark bg-primary" id="navbar-player">
                <ul class="navbar-nav ml-auto">
                    @if($player && $city)

                        <li class="nav-item" id="player-{{ $player->id }}">
                            <div class="navbar-text"><strong>City:</strong> [<span class="name"><a href="/city/{{ $city->id }}">{{ $city->name }}</a></span>]</div>

                        @if($city)
                            @php($gold = $city->getItemByName('gold'))
                            @if($gold)
                                <div class="navbar-text"><strong>Gold:</strong> [<span class="gold" data-city-id="{{ $city->id }}" data-item-id="1">{{ $gold->qty }}</span>]</div>
                            @endif
                        @endif
                        </li>
                    @endif
                    @php($game = \App\Models\Game::getLastTickDataForActiveGame())
                    <li class="nav-item" id="last-tick">
                        <div class="navbar-text"><strong>Last Tick:</strong> <span class="tick">@if(isset($game['tick'])){{ $game['tick'] }}@endif</span> (<span class="time time-updates">@if(isset($game['timestamp'])){{ $game['timestamp'] }}@endif</span>)</div>
                    </li>
                </ul>
            </nav>
        @endif
        <main class="py-4">

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">@yield('title')</div>

                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if (session('warning'))
                                    <div class="alert alert-warning" role="alert">
                                        {{ session('warning') }}
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-error" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if (session('info'))
                                    <div class="alert alert-info" role="alert">
                                        {{ session('info') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
    <footer class="navbar mt-auto py-3 bg-light">
      <div class="navbar-nav ml-auto">
        <div class="navbar-text">Current Server Time: {{ \Carbon\Carbon::now() }}</div>
      </div>
    </footer>
    <div class="modal" tabindex="-1" role="dialog" id="mainModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    
    <style>
        @keyframes yellowfade {
          from {
            background: yellow;
          }
          to {
            background: transparent;
          }
        }

        .update-highlight {
          animation: yellowfade 1s;
        }
        .item::before, .event::before, .resource::before {
            width: 10px;
            height: 10px;
            background: purple;
            content: '';  
            display: inline-block;
        }
        .item.item-gold::before {
            background: gold;
        }
        .item.item-wood::before {
            background: brown;
        }
        .item.item-iron::before {
            background: #a19d94;
        }
        .item.item-grain::before {
            background: #CCB9A8;
        }
        .event.event-ticks::before {
            background: grey;
        }
        .resource.resource-workers::before {
            
        }

        .card-hover{
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 6px 10px rgba(0,0,0,.08), 0 0 6px rgba(0,0,0,.05);
            transition: .3s transform cubic-bezier(.155,1.105,.295,1.12),.3s box-shadow,.3s -webkit-transform cubic-bezier(.155,1.105,.295,1.12);
            
            cursor: pointer;
        }

        .card-hover:hover{
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06);
        }
    </style>
    <script>
        function mgDebug(data) {
            console.log(data);
        }

        function flashUpdate(selector, text) {
console.log(selector);
            var selectors;
            if (typeof selector == "string")
                selectors = document.querySelectorAll(selector);
            else
                selectors = [selector];

            if (selectors) {
                $.each(selectors,function(i,selector){

                    let oldText = selector.innerText;
                    selector.innerText = text;
                    if (oldText != text) {
                        selector.classList.add("update-highlight");
                        setTimeout(function(){selector.classList.remove("update-highlight")},1000);
                    }
                })
                
            }
        }

        function executeFunctionByName(functionName, context /*, args */) {
            var args = Array.prototype.slice.call(arguments, 2);
            var namespaces = functionName.split(".");
            var func = namespaces.pop();
            for (var i = 0; i < namespaces.length; i++) {
                context = context[namespaces[i]];
            }
            return context[func].apply(context, args);
        }

        function showModal(options = {}) {
            var title = options.title;
            var text = options.text;
            var html = options.html;
            var func = options.func;
            var buttons = options.buttons;
            let modal = $('#mainModal');
            $(modal).find('.modal-body').html("");
            if (text && text.length)
                $(modal).find('.modal-body').text(text);
            if (html && html.length)
                $(modal).find('.modal-body').append(html);
            if (func) {
                $(modal).find('.modal-footer .btn-primary').click(function(e){
                    func();
                });
            }
            modal.modal();
        }

        function confirmForm(form, message = "Are you sure?", title = 'Confirm action', validate = false, func = false, buttons = false) {
            console.log(form);
            if (validate) {
                let test = executeFunctionByName(validate,window,form);
                if (!test) {
                    alert("error no validate");
                    return false;
                }
            }
            if (!func)
                func = function(){form.submit()};
            showModal({ 
                title: 'Confirm action', 
                text: message, 
                func: func
            });
            return false;
        }

        

        function updateTime(i) {
            let time = i.innerText;

            var date = new Date((time || "").replace(/-/g, "/").replace(/[TZ]/g, " ")),
                diff = (((new Date()).getTime() - date.getTime()) / 1000),
                day_diff = Math.floor(diff / 86400);

            if (isNaN(day_diff) || day_diff < 0 || day_diff >= 31) return;

            return day_diff == 0 && (
            diff < 60 && "just now" || diff < 120 && "1 minute ago" || diff < 3600 && Math.floor(diff / 60) + " minutes ago" 
                || diff < 7200 && "1 hour ago" || diff < 86400 && Math.floor(diff / 3600) + " hours ago") 
                || day_diff == 1 && "Yesterday" || day_diff < 7 && day_diff + " days ago" 
                || day_diff < 31 && Math.ceil(day_diff / 7) + " weeks ago";
        }

        @if(isset($city) && $city)
            var MyCity = {!! json_encode($city) !!};

            MyCity.update = function() {
//                flashUpdate("[data-city-id='"+e.city.id+"'][data-item-id='1']", e.city.items[1]);
                console.log('update');
            }
        @endif

        const api = {
            get: function(url) {
                return $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',

                    success: function( data ) {
                        console.log(data);
                        return data;
                        
                    }       
                });
            }
        }

        @yield('javascript')
        


        document.addEventListener("DOMContentLoaded", function(){
            let c = window.Echo.channel('game-updates')
                .listen('.tick-update', (e) =>
                {
                    if (e.tick) 
                        flashUpdate('#last-tick .tick', e.tick);
                    if (e.timestamp)
                        flashUpdate('#last-tick .time', e.timestamp);
                    mgDebug(e);
                });
                console.log(c);
            @if($user && $player)
                let c2 = window.Echo.channel('cities.{{$city->id}}')
                    .listen('.city-update', (e) =>
                    {
                        console.log(e);
                        if (e.city && e.city.items[1])
                            flashUpdate("#navbar-player [data-city-id='"+e.city.id+"'][data-item-id='1']", e.city.items[1]);
                    });
                console.log(c2);
            @endif

            let timeUpdates = document.querySelectorAll('.time-updates');
            if (timeUpdates.length) {
                console.log(timeUpdates);
                timeUpdates.forEach(function(i){
                   // i.innerText = updateTime(i);
                });
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            @yield('on-load')
            
        });


    </script>
</body>
</html>
