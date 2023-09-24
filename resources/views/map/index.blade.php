@extends('layouts.app')

@section('title')
    Map
@endsection

@section('css')
    #map{background:#d8d8d8;display:block;padding:0px;margin:0px;height:1000px;width:1000px}
    #contextMenu {
        display: none;
        position: absolute;
        width: 100px;
        background-color: white;
        box-shadow: 0 0 5px grey;
        border-radius: 3px;
      }
      #contextMenu .section { display: none; }

      
@endsection
@section('content')
    @if(isset($locations) && $locations)
        @php($player = Auth::user()->player)
        <div id="map-status"></div>
        <div id="map" height="1000" width="1000"></div>
        <div id="coordBox">&nbsp;</div>
        <h4>Locations</h4>
        <table class="table task-table">
            <thead>
                <th>City</th>
                <th>Location</th>
                <th></th>
            </thead>
            <tbody>
        @foreach($locations as $location)
            @if($location->city)
            <tr>
                <td>@if($location->city){{ $location->city->name }}@endif</td>
                <td>x: {{ $location->x }} y: {{ $location->y }}</td>
                <td>
                    @if($location->city && $location->city->id != $player->city->id)
                        <form action="{{ route('city.army.send') }}" method="POST">
                            @csrf
                            <input type="hidden" name="city_to" value="{{ $location->city->id }}">                        
                            <input type="hidden" name="city_from" value="{{ $player->city->id }}">
                            <button class="btn btn-danger" type="submit">Send Army</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
            </tbody>
        </table>
        <h4>Caravans</h4>
        <table class="table task-table">
            <thead>
                <th>ID</th>
                <th>From</th>
                <th>To</th>
                <th>Current</th>
                <th>Transports</th>
                <th>Items</th>
                <th>Progress</th>
                <th>Attacking armies</th>
                <th></th>
            </thead>
            <tbody>
        @foreach($mobiles->where('type',App\Models\Mobile::TYPE_CARAVAN) as $caravan)
            <tr>
                <td>{{ $caravan->id }}</td>
                <td>{{ $caravan->locationFrom->x }} {{ $caravan->locationFrom->y }}<br/>
                    {{ $caravan->locationFrom->city->name }}
                </td>
                <td>{{ $caravan->locationTo->x }} {{ $caravan->locationTo->y }}<br/>
                    {{ $caravan->locationTo->city->name }}
                </td>
                <td>{{ json_encode($caravan['current']) }}</td>
                <td>
                    <table class="table task-table">
                        <thead>
                            <th></th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                    @foreach($caravan->transports as $t)
                        <tr>
                            <td>{{ $t->transport->name }}</td>
                            <td>{{ $t->qty }}</td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                </td>

                <td>
                    <table class="table task-table">
                        <thead>
                            <th></th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                    @foreach($caravan->items as $i)
                        <tr>
                            <td>{{ $i->item->name }}</td>
                            <td>{{ $i->qty }}</td>
                            <td>{{ $i->type }}</td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                </td>
                <td>
                    <ul>
                        <li>% Complete: {{ $caravan->getPercentRouteComplete() }}</li>
                        <li>{{ $caravan->ticks_remaining }}/{{ $caravan->ticks }}</li>
                        <li>Returning?: {{ $caravan->returning }}</li>
                    </ul>
                </td>
                <td>@if($caravan->attackers)

                        @foreach($caravan->attackers as $army)
                            Army: {{ $army->id }}<br/>
                        @endforeach
                    @endif
                </td>
                <td>
                    
                </td>

            </tr>
        @endforeach
            </tbody>
        </table>

        <h4>Armies</h4>
        <table class="table task-table">
            <thead>
                
                <th>From</th>
                <th>To</th>
                <th>Current</th>
                <th>Troops</th>
                <th>Percent</th>
                <th>Ticks</th>
                <th>Returning</th>
                <th>State</th>
                <th>Target</th>
                <th>Transports</th>
                <th>Items</th>
                <th></th>
                <th></th>
            </thead>
            <tbody>

        @foreach($mobiles->where('type',App\Models\Mobile::TYPE_ARMY) as $army)
            <tr>
                
                <td>{{ $army->locationFrom->x }} {{ $army->locationFrom->y }}</td>
                <td>{{ $army->locationTo->x }} {{ $army->locationTo->y }}</td>
                <td>{{ json_encode($army->getcurrentPosition()) }}</td>
                <td>
                    <table class="table task-table">
                        <thead>
                            <th></th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                    @foreach($army->troops as $t)
                        <tr>
                            <td>{{ $t->troop->name }}</td>
                            <td>{{ $t->qty }}</td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                </td>                    
                <td>{{ $army->getPercentRouteComplete() }}</td>
                <td>{{ $army->ticks_remaining }}/{{ $army->ticks }}</td>
                <td>{{ $army->returning }}</td>
                <td>{{ $army->state }}</td>
                <td>
                    @if($army->target_mobile_id)
                        Mobile: {{ $army->target_mobile_id }}<br/>
                    @endif
                    
                </td>
                <td>
                    <table class="table task-table">
                        <thead>
                            <th></th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                    @foreach($army->transports as $t)
                        <tr>
                            <td>{{ $t->transport->name }}</td>
                            <td>{{ $t->qty }}</td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                </td>
                <td>
                    <table class="table task-table">
                        <thead>
                            <th></th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                    @foreach($army->items as $i)
                        <tr>
                            <td>{{ $i->item->name }}</td>
                            <td>{{ $i->qty }}</td>
                            <td>{{ $i->type }}</td>
                        </tr>
                    @endforeach
                        </tbody>
                    </table>
                </td>

                <td>

                </td>

                <td></td>
            </tr>
        @endforeach
            </tbody>
        </table>

    @else
        No cities found
    @endif
   
    <div id="contextMenu">
        <div id="city" class="section">
            <form action="{{ route('city.army.move') }}" method="POST">
                @csrf
                <input type="hidden" name="x">                        
                <input type="hidden" name="y">
                <button class="btn btn-danger" type="submit">Attack city</button>
            </form>
            <a class="cityUrl" href="/city">View City</a>
        </div>
        <div id="map" class="section">
            <form action="{{ route('city.army.move') }}" method="POST">
                @csrf
                <input type="hidden" name="x">                        
                <input type="hidden" name="y">
                <button class="btn btn-danger" type="submit">Move Army</button>
            </form>
        </div>
        <div id="mobile" class="section">
            <form action="{{ route('city.army.move') }}" method="POST">
                @csrf
                <input type="hidden" name="x">                        
                <input type="hidden" name="y">
                <input type="hidden" name="mobile_id">
                <button class="btn btn-danger" type="submit">Attack Mobile</button>
            </form>
        </div>
    </div>
    <script src="/js/konva.min.js"></script>
@endsection
<script>

@section('javascript')

    // init values
    var width = 1000;
    var height = 1000;

    var colors = {
        map: '#82A775',
        city: {
            influence: '#D1BE9D',
            dot: '#82A775'
        },
        caravan: {
            line: {
                to: '#64513B',
                from: '#D1BE9D'
            }
        },
        army: {
            line: {
                to: '#B05F66',
                from: 'grey'
            }
        },
        mobile: {
            ticks: {
                base: '#dfffc9',
                first: '#82A775',
                last: '#3B727C',
                current: '#000000',
                previous: '#FFFFFF'
            }
        }
    };
    var locations = {!! json_encode($locations) !!};
    var selection = null;
    var selectedLine = null;
    var mobiles = {!! json_encode($mobiles) !!};
    var map;


    function getSlope(a, b) {
        return Math.atan2(b[0] - b[1], a[0] - a[1]) * 180 / Math.PI;


    }

    // redraw the map
    // this needs rewriting to properly clear and redraw the map
    function redrawMap(items) {
        // init map and stage
        if (!map) {
          map = new Konva.Stage({
            container: 'map',
            width: width,
            height: height,
            //draggable: true
          });
        } else {
            // don't really work
            map.getStage().destroyChildren();
        }
        // init the layers

        var locationLayer = new Konva.Layer();
        var mobileLayer = new Konva.Layer();
        var textLayer = new Konva.Layer();


        var mapLayer = new Konva.Layer();
        var mapPlane = new Konva.Rect({
            x: 0,
            y: 0,
            width: width,
            height: height,
            fill: colors.map
        });
        mapLayer.add(mapPlane);
        map.add(mapLayer);

        var locations = items.locations;
        var mobiles = items.mobiles;

        // add the locations layer
        locations.forEach(function(v){
            //console.log(v);
            var circle = new Konva.Circle({
                x: v.x,
                y: map.width()-v.y,
                radius: 5,
                fill: colors.city.dot,
                stroke: 'black',
                strokeWidth: 2,
            });
            if (v.city) {
                circle.data = v;
                if (v.city.influence) {
                    var influenceCircle = new Konva.Circle({
                        x: v.x,
                        y: map.width()-v.y,
                        radius: v.city.influence,
                        fill: colors.city.influence,
                        stroke: 'black',
                        strokeWidth: 2,
                        opacity: 0.3
                    });
                    influenceCircle.data = v;
                    locationLayer.add(influenceCircle);
                }
                var text = new Konva.Text({
                    x: v.x,
                    y: (map.width()-v.y)+15,
                    text: v.city.name,
                    fontSize: 20,
                    fontFamily: 'Calibri',
                    fill: 'black',
                  });
                textLayer.add(text);
            }

            
            locationLayer.add(circle);
           // circle.zIndex(0);
        });



        // add mobiles layer
        mobiles.forEach(function(v){
            //console.log(v);
            var type;
            var color;
            if (v.type == {{ App\Models\Mobile::TYPE_CARAVAN }}) {
                color = colors.caravan.line.to;
                if (v.returning)
                    color = colors.caravan.line.from;
                type = "caravan";
            } 
            if (v.type == {{ App\Models\Mobile::TYPE_ARMY }}) {
                color = colors.army.line.to;
                if (v.returning)
                    color = colors.army.line.from;
                type = "army";
            } 
            var mobileGroup = new Konva.Group({
                type: type,
                mobile_id: v.id
            });


            var line = new Konva.Line({
                points: [
                    v.location_from.x,map.width()-v.location_from.y,
                    v.location_to.x,map.width()-v.location_to.y],
                stroke: color,
                strokeWidth: 10,
                lineCap: 'round',
                lineJoin: 'round',
                name: 'line'
            });
            //line.data = {mobile:{id:v.id}};

            var ticks = new Konva.Group({
                name: 'ticks'
            });
            $(v.positions).each(function(i,value){
                i = i+1;
                color = colors.mobile.ticks.base;
                size = 5;
                name = "";

                if (!v.returning && i < (v.ticks-v.ticks_remaining)) {
                    color = colors.mobile.ticks.previous;
                    size = 3;
                }
                if (!v.returning && i == (v.ticks-v.ticks_remaining)) {
                    color = colors.mobile.ticks.current;
                    size = 7;
                    name = "current";
                }
                if (v.returning && v.ticks_remaining < i) {
                    color = colors.mobile.ticks.previous;
                    size = 3;
                }
                if (v.returning && i == v.ticks_remaining) {
                    color = colors.mobile.ticks.current;
                    name = "current";
                    size = 7;
                }
                if (i == v.positions.length) {
                    color = colors.mobile.ticks.last;
                    name = "current";
                }
                if (i == 1) {
                    color = colors.mobile.ticks.first;
                    name = "current";
                }
                var tickPosition = new Konva.Circle({
                    x: value[0],
                    y: map.width()-value[1],
                    radius: size,
                    fill: color,
                    stroke: 'black',
                    strokeWidth: 1,
                    name: name,
                    type: type,
                    data: {mobile:{id:v.id},x:value[0],y:map.width()-value[1]}
                });

                ticks.add(tickPosition);
            });
            mobileGroup.add(ticks);
            mobileGroup.add(line);

            mobileLayer.add(mobileGroup);
            
            line.zIndex(0);
        });
        
        // add mouseover/mouseout events for layers
        [locationLayer, mobileLayer].forEach((layer) => {

            layer.on('mouseover', function(evt){
                // var shape = evt.target;
                // //console.log(shape.getParent());
                // if (shape.getClassName() == "Circle") {
                // // if (shape.data) {
                //     shape.baseColor = shape.stroke();
                //     shape.baseOpacity = shape.opacity();
                //     shape.baseFill = shape.fill();
                //     shape.opacity(1);
                //     shape.stroke('yellow');
                //     shape.fill('yellow');
                //     shape.draw();
                // }
            });
            layer.on('mouseout', function(evt){
            //     var shape = evt.target;
            //     if (shape.getClassName() == "Circle") {
            //         shape.stroke(shape.baseColor);
            //         shape.opacity(shape.baseOpacity);
            //         shape.fill(shape.baseFill);
            //         shape.draw();
            // //        console.log(evt.target.baseColor);
            //     }
            });
        });

        mobileLayer.on('mouseover', function(evt) {
            var shape = evt.target;
            var parent;
            if (shape.getClassName() == "Line") {
                parent = shape.parent;
            } else {
                parent = shape.parent.parent;
            }

            var line = parent.find('.line')[0];

            line.baseStroke = line.stroke();
            line.baseFill = shape.fill();
            line.baseStrokeWidth = shape.attrs.strokeWidth;
            line.stroke('yellow');
            line.strokeWidth(15);
            line.draw();
            line.zIndex(0);
            console.log(line);
        });

        mobileLayer.on('mouseout', function(evt) {
            var shape = evt.target;
            var parent;
            if (shape.getClassName() == "Line") {
                parent = shape.parent;
            } else {
                parent = shape.parent.parent;
            }

            var line = parent.find('.line')[0];
            if (line == selectedLine)
                return;
            line.stroke(line.baseStroke);
            line.strokeWidth(line.baseStrokeWidth);
            line.draw();
            line.zIndex(0);
        });

        // get the position of this event
        function getPosition(e) {
          var posx = 0;
          var posy = 0;

          if (!e) var e = window.event;

          if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
          } else if (e.clientX || e.clientY) {
            posx = e.clientX + document.body.scrollLeft + 
                               document.documentElement.scrollLeft;
            posy = e.clientY + document.body.scrollTop + 
                               document.documentElement.scrollTop;
          }

          return {
            x: posx,
            y: posy
          }
        }

        // show menu depending on type
        var menuNode = document.getElementById('contextMenu');
        function showMenu(type, event, data) {

            menuNode.querySelectorAll('.section').forEach(function(v){
                v.style = "display:none";
            });
            menuNode.style.display = 'initial';
            var section = menuNode.querySelector('#'+type);
            section.style.display = 'initial';
            var containerRect = map.container().getBoundingClientRect();
            var pos = getPosition(event.evt);


            menuNode.style.top =  map.getPointerPosition().y + 50 + 'px';
            menuNode.style.left = map.getPointerPosition().x + 40 + 'px';
            if (type == 'city' && data && data.city) {
                console.log('#'+type+" .btn-danger");
                if (data.city.player_id == {{ $player->id }}) {
                    console.log(data.city);
                    menuNode.querySelector('#'+type+" .btn-danger").innerText = "Return to city";
                } else {
                     menuNode.querySelector('#'+type+" .btn-danger").innerText = "Attack city";
                }
                section.querySelector('form input[name=x]').value = data.city.location.x;
                section.querySelector('form input[name=y]').value = data.city.location.y;
                    
                
            }
            console.log(data);
            if (type == 'map' && data && data.x && data.y) {
                section.querySelector('form input[name=x]').value = data.x;
                section.querySelector('form input[name=y]').value = data.y;           
            }

            if (type == 'mobile' && data && data.x && data.y && data.mobile) {
                section.querySelector('form input[name=x]').value = data.x;                      
                section.querySelector('form input[name=y]').value = data.y;   
                section.querySelector('form input[name=mobile_id]').value = data.mobile.id;     
                section.querySelector('form button[type=submit]').innerText = "Attack "+data.type;   
            }
        }

        // get map position of this event (x,y starts bottom left)
        function getMapPosition(e) {
            var x = e.offsetX ? (e.offsetX) : e.pageX - map.offsetLeft;
            var y = e.offsetY ? (e.offsetY) : e.pageY - map.offsetTop;
            y = width-y;
            return {
                x: x,
                y: y
            }
        }

        // set the map position on mousemove
        document.getElementById('map').addEventListener("mousemove", function(e) {
            var pos = getMapPosition(e);
            var x = pos.x;
            var y = pos.y;
            coordBox.innerText = "x:"+x+",y:"+y+" "+"x:"+x+",y:"+(width-y)+" ";
        });
        
        // add context menu for location layer
        locationLayer.on('contextmenu', function(evt){
            evt.evt.preventDefault();
            var shape = evt.target;
            var data = {
                city:{
                    id:shape.data.city.id,
                    player_id:shape.data.city.player_id,
                    location:{
                        x: shape.data.city.location.x, 
                        y: shape.data.city.location.y
                    }
                }
            };
          
            showMenu('city',event,data);
            evt.cancelBubble = true;
        });

        // add context menu for caravan layer
        mobileLayer.on('contextmenu', function(evt){
            evt.evt.preventDefault();
            var target = evt.target;
            if (target.data) {
                var pos = getMapPosition(evt.evt);
                var data = {
                    mobile:{
                        id:target.data.mobile.id,
                    },
                    x: target.data.x,
                    y: map.width()-target.data.y
                };
                console.log(target);
                showMenu('mobile',event,data);
                evt.cancelBubble = true;
            }
        });

        // add context menu for caravan layer
        mobileLayer.on('click', function(evt){
            evt.evt.preventDefault();
            var target = evt.target;
            var mobileGroup;
            var currentTick;
            var line;
            var ticks;
            //console.log(target);

            // If click is on the line, default to the current tick
            if (target.getClassName() == "Line") {
                currentTick = target.parent.find('.ticks')[0].find('.current')[0];
                //console.log(currentTick);
                target = currentTick;
            } 

            mobileGroup = target.parent.parent;
            //console.log(mobileGroup);

            if (target && target.getClassName() == "Circle") {
                ticks = mobileGroup.find('.ticks')[0];
                line = mobileGroup.find('.line')[0];
                firstTick = ticks[0];
                lastTick = ticks[ticks.length-1];
                if (!currentTick)
                    currentTick = ticks.find('.current')[0]; 
                // reset the selection colors
                if (selection) {
                    //console.log(selection.getClassName());
                    selection.fill(selection.baseFill);
                    selectedLine.stroke(selectedLine.baseStroke);
                    selection.draw();
                    selectedLine.draw();
                }

                if (!target.baseFill)
                    target.baseFill = target.fill();
                if (!line.baseStroke)
                    line.baseStroke = line.stroke();
                line.stroke('black');
                if (target == firstTick)
                    target.fill('blue');
                else
                    target.fill('pink');
                target.draw();
                selection = target;
                selectedLine = line;
console.log(target);
                var data = {
                    mobile:{
                        id: mobileGroup.attrs.mobile_id,
                    },
                    x: target.attrs.x,
                    y: map.width()-target.attrs.y,
                    type: mobileGroup.attrs.type
                };
                console.log(target);
                showMenu('mobile',event,data);
            } 
            evt.cancelBubble = true;
        });


        // add context menu for rest of map
        map.on('contextmenu', function(evt) {
            evt.evt.preventDefault();
            var pos = getMapPosition(evt.evt);
            var data = {x:pos.x,y:pos.y};
            showMenu('map',event,data);
        })

        // click event for map. Clear any context menus
        window.addEventListener('click', (e) => {
            // hide menu
           // if (e.target != menuNode)
           //     menuNode.style.display = 'none';
          });

        // add all the layers to the map
        map.add(locationLayer);

        map.add(mobileLayer);
        
        map.add(textLayer);

    }

    // var updateTimer = setInterval(function(){
    //     console.log('checking for updates');
    // },5000);
@endsection

@section('on-load')
    redrawMap({locations:locations,mobiles:mobiles});

    let m = window.Echo.channel('map-updates')
    .listen('.map-update', (e) =>
    {
        $.ajax({
            url: '{{ route('map.update') }}',
            type: 'POST',
            dataType: 'json',

            success: function( data ) {
                console.log('send map update request');
                redrawMap({locations:data.locations,mobiles:data.mobiles});
                console.log(data);
            }       
        })

        console.log('map update');
        console.log(e);
        mgDebug(e);
    });


    $('#contextMenu form').submit(function(e){
        e.preventDefault();
//console.log($(this).attr('action'));
//return;
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function( data ) {
  //              var out = data.msg;
                flashUpdate('#map-status',data.msg);

                console.log(data);
            }       
        })
    });
@endsection
</script>