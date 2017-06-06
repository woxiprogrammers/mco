<?php
/**
 * Created by Ameya Joshi.
 * Date: 6/6/17
 * Time: 7:24 PM
 */
?>



<tr>
    <td>
        <div class="form-group">
            <select class="form-control quotation-product-table quotation-category" id="category_select_{{$rowIndex}}" name="category_id[]">
                @foreach($categories as $category)
                <option value="{{$category['id']}}"> {{$category['name']}} </option>
                @endforeach
            </select>
        </div>
    </td>
    <td>
        <div class="form-group">
            <select class="form-control quotation-product-table" name="product_id[]" id="product_select_{{$rowIndex}}" disabled>

            </select>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input name="product_rate[]" class="form-control quotation-product-table" id="product_rate_{{$rowIndex}}" type="text" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input name="product_rate[]" class="form-control quotation-product-table" id="product_unit_{{$rowIndex}}" type="text" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="text" class="form-control quotation-product-table" name="product_quantity[]" id="product_quantity_{{$rowIndex}}" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="text" name="product_amount[]" class="form-control quotation-product-table" id="product_amount_{{$rowIndex}}" readonly>
        </div>
    </td>
    <td>
        <a>
            View Materials
        </a>
    </td>
</tr>

<script>
$(document).ready(function(){
    var category_id = $("#category_select_{{$rowIndex}}").val();
    getProducts(category_id,{{$rowIndex}});
    var selectedProduct = $("#product_select_{{$rowIndex}}").val();
    getProductDetails(selectedProduct, {{$rowIndex}});
    $(".quotation-category").change(function(){
        var category_id = $(this).val();
        var categoryIdField = $(this).attr('id');
        var idArray = categoryIdField.split('_');
        var rowNumber = idArray[2];
        debugger;
        getProducts(category_id, rowNumber);
        var selectedProduct = $("#product_select_"+rowNumber).val();
        getProductDetails(selectedProduct, rowNumber);
    });
});
</script>