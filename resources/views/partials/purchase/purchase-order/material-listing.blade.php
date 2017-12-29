@foreach($purchaseRequestComponents as $purchaseRequestComponent)
    <tr id="{{$purchaseRequestComponent['material_request_component_slug']}}">
        <td style="text-align: center">
            <select class="form-control component-category">
                @foreach($purchaseRequestComponent['categories'] as $category)
                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                @endforeach
            </select>
        </td>
        <td style="text-align: center; width: 15%">
            <input type="hidden" value="{{$purchaseRequestComponent['vendor_id']}}" class="component-vendor">
            <input type="hidden" class="component-id" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][id]" value="{{$purchaseRequestComponent['purchase_request_component_id']}}">
            <input type="text" class="form-control component-name" value="{{$purchaseRequestComponent['name']}}" readonly>
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%;" class="form-control component-quantity" value="{{$purchaseRequestComponent['quantity']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][quantity]">
        </td>
        <td style="text-align: center">
            <select style="width: 90%" class="form-control component-unit" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][unit_id]">
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
                @endforeach" type="text" id="rate" style="width: 90%" class="form-control component-rate" value="{{$purchaseRequestComponent['rate']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][rate]">
        </td>
        <td style="text-align: center">
            <input type="text" style="width: 90%" class="form-control component-hsn-code"  value="{{$purchaseRequestComponent['hsn_code']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][hsn_code]">
        </td>
        <td style="text-align: center">
            <a style="width: 90%" class="btn btn-xs blue  component-tax-button" onclick="addTax(this)">Add Tax</a>
        </td>
        <td style="text-align: center">
            <input type="date" style="width: 90%" class="form-control component-delivery-date"  name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][expected_delivery_date]">
        </td>
        <td style="text-align: center;">
            <input type="file" multiple name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][vendor_quotation_images][]">
        </td>
        <td style="text-align: center;">
            <input type="file" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][client_approval_images][]" multiple>
        </td>
        <td style="text-align: center">
            <select class="table-group-action-input form-control input-inline input-small input-sm status-select" id="is_approve_{{$purchaseRequestComponent['purchase_request_component_id']}}_{{$purchaseRequestComponent['vendor_id']}}" name="purchase[{{$purchaseRequestComponent['vendor_id']}}][{{$purchaseRequestComponent['purchase_request_component_id']}}][status]">
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
        var elementId = $(this).attr('id');
        var componentId = elementId.match(/\d+/)[0];
        if(($(this).val() == "approve") && (type == "new-material")) {
            $('#myModal1 #purchaseRequestComponentId').val(componentId);
            var categoryId = $row.find(".component-category").val();
            $('#myModal1 option[value="'+categoryId+'"]').prop('selected', true);
            $('#myModal1 option[value="'+categoryId+'"]').prop('disabled', false);
            $('#myModal1 option:not([value="'+categoryId+'"])').each(function(){
                console.log($(this).attr('value'));
                $(this).prop('disabled', true);
            });
            $('#myModal1').modal('show');
            $("#vendor_id").val($row.find(".component-vendor").val());
            $('#name').val($row.find(".component-name").val());
            $('#rate_per_unit').val($row.find(".component-rate").val());
            $('#unit_id').val($row.find(".component-unit").val());
            $('#hsn_code').val($row.find(".component-hsn-code").val());
        }else if(($(this).val() == "approve") && (type == "new-asset")){
            $('#myModal2 #purchaseRequestComponentId').val(componentId);
            $('#myModal2').modal('show');
            $("#asset_vendor_id").val($row.find(".component-vendor").val());
            $('#assetName').val($row.find(".component-name").val());
            $('#asset_rate_per_unit').val($row.find(".component-rate").val());
            $('#asset_unit_id').val($row.find(".component-unit").val());
            $('#asset_hsn_code').val($row.find(".component-hsn-code").val());
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
    </script>