<tr>
    <th style="width: 25%"> Name </th>
    <th> Rate </th>
    <th> Unit </th>
    <th> Quantity </th>
    <th> Amount </th>
</tr>
@foreach($materialData as $data)
    <tr>
        <td>
            <label>
                {{$data['material']['name']}}
            </label>
        </td>
        <td>
            <input class="form-control" type="number" id="material_version_{{$data['material_version']['id']}}_rate" name="material_version[{{$data['material_version']['id']}}][rate_per_unit]" value="{{$data['material_version']['rate_per_unit']}}" onkeyup="changedQuantity({{$data['material_version']['id']}})" onchange="changedQuantity({{$data['material_version']['id']}})">
        </td>
        <td>
            <select class="form-control material_unit" id="material_version_{{$data['material_version']['id']}}_unit" name="material_version[{{$data['material_version']['id']}}][unit_id]" onchange="convertUnits({{$data['material_version']['id']}})">
                @foreach($units as $unit)
                    @if($unit['id'] == $data['material_version']['unit_id'])
                        <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                    @else
                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                    @endif
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control" id="material_version_{{$data['material_version']['id']}}_quantity" name="material_quantity[{{$data['material_version']['id']}}]" onkeyup="changedQuantity({{$data['material_version']['id']}})" onchange="changedQuantity({{$data['material_version']['id']}})" required>
        </td>
        <td>
            <input type="text" class="form-control material_amount" id="material_version_{{$data['material_version']['id']}}_amount" name="material_amount[{{$data['material_version']['id']}}]" required>
        </td>
    </tr>
@endforeach