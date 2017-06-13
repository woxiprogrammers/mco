<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/6/17
 * Time: 6:08 PM
 */
?>
<fieldset>
    <legend> Edit Profit Margins </legend>
    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col" style="width:450px !important"> <u> Profit Margins<i class="fa fa-arrow-right"></i> </u> <br> Products <i class="fa fa-arrow-down"></i></th>
                    @foreach($profitMargins as $profitMargin)
                    <th scope="col"> {{$profitMargin['name']}} </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($productProfitMargins as $id => $data)
                    <tr>
                        <td> {{$data['products']}} </td>
                        @foreach($data['profit_margin'] as $profitMargin)
                        <td> <input class="form-control" type="number" step="any" name="profit_margins[{{$id}}][{{$profitMargin['id']}}]" value="{{$profitMargin['percentage']}}"></td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <div class="col-md-2 col-md-offset-2">
            <a class="btn btn-primary" onclick="backToMaterials()" href="javascript:void(0);">
                Back
            </a>
        </div>
        <div class="col-md-3 col-md-offset-4">
            <a class="btn btn-success" id="next2" href="javascript:void(0);">
                Submit
            </a>
        </div>
    </div>

</fieldset>
