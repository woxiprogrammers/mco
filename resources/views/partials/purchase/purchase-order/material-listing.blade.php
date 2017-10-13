@foreach($purchaseRequestComponents as $purchaseRequestComponent)
    <tr>
        <td style="text-align: center; width: 15%">
            <input type="hidden" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][id]" value="{{$purchaseRequestComponent['purchase_request_component_id']}}">
            {{$purchaseRequestComponent['name']}}
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%;" class="form-control" value="{{$purchaseRequestComponent['quantity']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][quantity]">
        </td>
        <td style="text-align: center">
            <select style="width: 90%" class="form-control" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][unit_id]">
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
            <input type="text" style="width: 90%" class="form-control" value="{{$purchaseRequestComponent['rate']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][rate]">
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%" class="form-control"  value="{{$purchaseRequestComponent['hsn_code']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][hsn_code]">
        </td>
        <td style="text-align: center;">
            <input type="file" multiple name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][vendor_quotation_images][]">
        </td>
        <td style="text-align: center;">
            <input type="file" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][client_approval_images][]" multiple>
        </td>
        <td style="text-align: center">
            <select class="table-group-action-input form-control input-inline input-small input-sm" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][status]">
                <option value="">Select...</option>
                <option value="approve">Approve</option>
                <option value="disapprove">Disapprove</option>
            </select>
        </td>
    </tr>
@endforeach