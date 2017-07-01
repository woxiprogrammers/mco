<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/6/17
 * Time: 12:47 PM
 */
?>
<fieldset>
    <legend> Edit Materials </legend>
    <table class="table table-bordered" id="quotationMaterialTable">
        <tr>
            <th style="width: 13%">
                Is Client supplied?
            </th>
            <th>
                Material Name
            </th>
            <th>
                Rate per Unit
            </th>
            <th>
                Unit
            </th>
        </tr>
        @foreach($materials as $material)
            <tr>
                <input type="hidden" class="material-id" name="material_id[]" value="{{$material['id']}}">
                <td>
                    @if(array_key_exists('is_client_supplied',$material))
                        @if($material['is_client_supplied'] == true)
                            <input type="checkbox" name="clientSuppliedMaterial[]" value="{{$material['id']}}" checked>
                        @else
                            <input type="checkbox" name="clientSuppliedMaterial[]" value="{{$material['id']}}">
                        @endif
                    @else
                        @if(in_array($material['id'],$clientSuppliedMaterial))
                            <input type="checkbox" name="clientSuppliedMaterial[]" value="{{$material['id']}}" checked>
                        @else
                            <input type="checkbox" name="clientSuppliedMaterial[]" value="{{$material['id']}}">
                        @endif
                    @endif

                </td>
                <td>
                    {{$material['name']}}
                </td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control material-table-input quotation-material-rate" name="material_rate[{{$material['id']}}]" value="{{$material['rate_per_unit']}}" id="materialRate{{$material['id']}}" step="any">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <select name="material_unit[{{$material['id']}}]" id="materialUnit{{$material['id']}}" class="form-control material-table-input" onchange="convertUnit({{$material['id']}},{{$material['unit_id']}})">
                            @foreach($units as $unit)
                                @if($unit['id'] == $material['unit_id'])
                                    <option value="{{$unit['id']}}" selected> {{$unit['name']}}</option>
                                @else
                                    <option value="{{$unit['id']}}"> {{$unit['name']}} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
    <div>
        <div class="col-md-2 col-md-offset-2">
            <a class="btn btn-primary" onclick="backToGeneral()" href="javascript:void(0);">
                Back
            </a>
        </div>
        <div class="col-md-3 col-md-offset-4">
            <a class="btn btn-primary" id="next2" href="javascript:void(0);" onclick="showProfitMargins()">
                Update Profit Margins
            </a>
        </div>
    </div>
</fieldset>