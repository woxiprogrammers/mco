@foreach($purchaseRequestComponents as $purchaseRequestComponent)
    <tr>
        <td style="text-align: center">
            {{$purchaseRequestComponent['name']}}
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%;" class="form-control" value="{{$purchaseRequestComponent['quantity']}}">
        </td>
        <td style="text-align: center">
            <select style="width: 90%" class="form-control">
                @foreach($unitInfo as $unit)
                    @if($purchaseRequestComponent['unit_id'] == $unit['id'])
                        <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                    @else
                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                    @endif
                @endforeach
            </select>
        </td>
        <td style="text-align: center">
            {{$purchaseRequestComponent['vendor']}}
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%" class="form-control" value="{{$purchaseRequestComponent['rate']}}">
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%" class="form-control"  value="{{$purchaseRequestComponent['hsn_code']}}">
        </td>
        <td style="text-align: center; width: 5%;">
            <input type="file" multiple>
        </td>
        <td style="text-align: center; width: 5%">
            <input type="file" multiple style="width: 50%">
        </td>
        <td style="text-align: center">
            <select class="table-group-action-input form-control input-inline input-small input-sm">
                <option value="">Select...</option>
                <option value="Cancel">Approve</option>
                <option value="Cancel">Disapprove</option>
            </select>
        </td>
    </tr>
@endforeach