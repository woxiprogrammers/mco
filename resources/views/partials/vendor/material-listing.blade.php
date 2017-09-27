<tr>
    <th style="width: 25%"> Name </th>
    <th> Unit </th>
    <th> Rate </th>
    <th> Quantity </th>
    <th> Amount </th>
</tr>
@foreach($materialData as $data)
    <tr>
        <td>
            <label>
                {{$data['material']['name']}}
                <input type="hidden" name="product_material_id[]" value="{{$data['material']['id']}}" class="product-material-id">
            </label>
        </td>
        <td>
            <div class="form-group">
                <select class="form-control material_unit material-table-input" id="material_{{$data['material']['id']}}_unit" name="material[{{$data['material']['id']}}][unit_id]" onchange="convertUnits({{$data['material']['id']}})">
                    @foreach($units as $unit)
                        @if($unit['id'] == $data['material']['unit_id'])
                            <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                        @else
                            <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <div class="form-group">
                <input class="form-control material-table-input" step="any" type="number" id="material_{{$data['material']['id']}}_rate" name="material[{{$data['material']['id']}}][rate_per_unit]" value="{{$data['material']['rate_per_unit']}}" onkeyup="changedQuantity({{$data['material']['id']}})" onchange="changedQuantity({{$data['material']['id']}})">
            </div>
        </td>
        <td>
            <div class="form-group">
                <input type="number" step="any" class="form-control material-table-input" id="material_{{$data['material']['id']}}_quantity" name="material_quantity[{{$data['material']['id']}}]" value="{{$data['material']['quantity']}}" onkeyup="changedQuantity({{$data['material']['id']}})" onchange="changedQuantity({{$data['material']['id']}})" required>
            </div>
        </td>
        <td>
            <div class="form-group">
                <input type="text" class="form-control material_amount material-table-input" id="material_{{$data['material']['id']}}_amount" name="material_amount[{{$data['material']['id']}}]" required readonly>
            </div>
        </td>
    </tr>
@endforeach