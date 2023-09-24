<form id="craftRecipeForm" action="{{ route('city.craft.item') }}" method="POST">
    @csrf
    <div class="form-row">
        <label>Workers</label>
        <input type="text" name="workers-num" value="1">
        <input class="form-range" type="range" name="workers" min="1" value="1" onChange="$(this).parent().find('[name=workers-num]').val($(this).val())">
    </div>
    <div class="form-row">
        <label>Qty</label>
        <input class="form-control" type="number" name="qty" value="10">
    </div>
    <input type="hidden" name="city_building_id">
    <input type="hidden" name="item_recipe_id">
    <input type="hidden" name="craft_id">
</form>
