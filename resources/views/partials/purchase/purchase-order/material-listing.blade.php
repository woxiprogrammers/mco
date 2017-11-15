@foreach($purchaseRequestComponents as $purchaseRequestComponent)
    <tr id="{{$purchaseRequestComponent['material_request_component_slug']}}">
        <td style="text-align: center; width: 15%" style="display: none">
            <input type="hidden" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][id]" value="{{$purchaseRequestComponent['purchase_request_component_id']}}">
        </td>
        <td>
            <input type="text" value="{{$purchaseRequestComponent['name']}}" readonly>
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
        <input type="hidden" id="material_request_component_slug" value="{{$purchaseRequestComponent['material_request_component_slug']}}">
        <td style="text-align: center">
            {{$purchaseRequestComponent['vendor']}}
        </td>
        <td style="text-align: center">
            <input data-toggle="tooltip" title="@foreach($purchaseRequestComponent['last_three_rates'] as $rate)
                    {{$rate['rate_per_unit']}},
                @endforeach" type="text" id="rate" style="width: 90%" class="form-control" value="{{$purchaseRequestComponent['rate']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][rate]">
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
            <select class="table-group-action-input form-control input-inline input-small input-sm status-select" id="is_approve_{{$purchaseRequestComponent['purchase_request_component_id']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][status]">
                <option value="">Select...</option>
                <option value="approve">Approve</option>
                <option value="disapprove">Disapprove</option>
            </select>
        </td>
    </tr>

@endforeach
<script>
    $('.status-select').change(function () {
        var type = $(this).closest('tr').attr('id');
        var $row = jQuery(this).closest('tr');
        var $columns = $row.find('td');
        var elementId = $(this).attr('id');
        var componentId = elementId.match(/\d+/)[0];
        if(($(this).val() == "approve") && (type == "new-material")) {

                $('#myModal1 #purchaseRequestComponentId').val(componentId);
                $('#myModal1').modal('show');
            var values = [];
            jQuery.each($columns, function (i, item) {
                values.push($(item.innerHTML).val());
            });
            $('#vendor_id').val(values[0]);
            $('#name').val(values[1]);
            $('#rate_per_unit').val(values[5]);
            $('#unit_id').val(values[3]);
            $('#hsn_code').val(values[6]);
        }else if(($(this).val() == "approve") && (type == "new-asset")){
            $('#myModal2 #purchaseRequestComponentId').val(componentId);
            $('#myModal2').modal('show');
            var values = [];
            jQuery.each($columns, function (i, item) {
                values.push($(item.innerHTML).val());
            });
            $('#asset_vendor_id').val(values[0]);
            $('#assetName').val(values[1]);
            $('#asset_rate_per_unit').val(values[5]);
            $('#asset_unit_id').val(values[3]);
            $('#asset_hsn_code').val(values[6]);
        }

    })
    $('[data-toggle="tooltip"]').tooltip();
    </script>