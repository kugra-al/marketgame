@extends('layouts.admin')

@section('title')
    Admin Items
@endsection
@section('content')
    @if(isset($items) && $items)
        <table class="table task-table">
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Weight</th>
                <th>Cost</th>
                <th>Tradeable</th>
                <th>Recipes Used In</th>
                <th>Recipes</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($items as $i)
                    <tr>
                        <td><a href="{{ route('admin.item.show', $i->id) }}">{{ $i->id }}</a></td>
                        <td><a href="{{ route('admin.item.show', $i->id) }}">{{ $i->name }}</a></td>
                        <td>{{ $i->slug }}</td>
                        <td>{{ $i->weight }}</td>
                        <td>{{ $i->base_cost }}</td>
                        <td>{{ $i->tradeable ? "yes " : "-" }}</td>
                        <td>
                            @if($i->recipesUsedWith)
                                @foreach($i->recipesUsedWith as $r)
                                    {{ $r->recipe->item->name }}
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if($i->recipes)
                                @foreach($i->recipes as $r)
                                    <h5>Recipe: {{ $r->id }}</h5>
                                    <ul>
                                    @foreach($r->items as $i)
                                        <li>{{ $i->item->name }} - {{ $i->qty }}</li>
                                    @endforeach
                                    </ul>
                                @endforeach
                            @endif

                        </td>
                        <td><a class="btn btn-info" href="{{ route('admin.item.edit', $i->id) }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        No items found
    @endif
@endsection
