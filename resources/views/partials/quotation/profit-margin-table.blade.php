<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/6/17
 * Time: 6:08 PM
 */
?>

<fieldset style="margin-top: 2%">
    <legend> Edit Profit Margins </legend>
    <div class="table-scrollable profit-margin-table">
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
                        @foreach($profitMargins as $profitMargin)
                            @if(array_key_exists($profitMargin['id'],$data['profit_margin']))
                                <td>
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="profit_margins[{{$id}}][{{$profitMargin['id']}}]" value="{{$data['profit_margin'][$profitMargin['id']]}}" style="width:85%; margin-left: 6%">
                                    </div>
                                </td>
                            @else
                                <td>
                                    <div class="form-group">
                                         <input class="form-control" type="number" step="any" name="profit_margins[{{$id}}][{{$profitMargin['id']}}]" value="0" style="width:85%; margin-left: 6%">
                                    </div>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</fieldset>
