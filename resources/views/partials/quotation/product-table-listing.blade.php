<?php
/**
 * Created by Ameya Joshi.
 * Date: 6/6/17
 * Time: 7:24 PM
 */
?>



<tr id="Row{{$rowIndex}}">
    <td>
        <div class="form-group">
            <select class="form-control quotation-product-table quotation-category" id="categorySelect{{$rowIndex}}" name="category_id[]">
                @foreach($categories as $category)
                <option value="{{$category['id']}}"> {{$category['name']}} </option>
                @endforeach
            </select>
        </div>
    </td>
    <td>
        <div class="form-group">
            <select class="form-control quotation-product-table quotation-product" name="product_id[]" id="productSelect{{$rowIndex}}" disabled>

            </select>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input name="product_description[]" class="form-control quotation-product-table" onfocus="replaceEditor({{$rowIndex}})" id="productDescription{{$rowIndex}}" type="text" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input name="product_rate[]" class="form-control quotation-product-table" id="productRate{{$rowIndex}}" type="text" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input class="form-control quotation-product-table" id="productUnit{{$rowIndex}}" type="text" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="number" step="any" class="form-control quotation-product-table quotation-product-quantity" name="product_quantity[]" id="productQuantity{{$rowIndex}}" onchange="calculateAmount({{$rowIndex}})" onkeyup="calculateAmount({{$rowIndex}})" readonly>
        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="text" name="product_amount[]" class="form-control quotation-product-table" id="productAmount{{$rowIndex}}" readonly>
        </div>
    </td>
    <td>
        <table>
            <tr style="border-bottom: 1px solid black">
                <td>
                    <a href="javascript:void(0);">
                        View
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="javascript:void(0);" onclick="removeRow({{$rowIndex}})">
                        Remove
                    </a>
                </td>
            </tr>
        </table>
    </td>
</tr>

<script>
$(document).ready(function(){
    var category_id = $("#categorySelect{{$rowIndex}}").val();
    getProducts(category_id,{{$rowIndex}});
    var selectedProduct = $("#productSelect{{$rowIndex}}").val();
    getProductDetails(selectedProduct, {{$rowIndex}});

    $(".quotation-category").change(function(){
        var category_id = $(this).val();
        var categoryIdField = $(this).attr('id');
        var rowNumber = categoryIdField.match(/\d+/)[0];
        getProducts(category_id, rowNumber);
        var selectedProduct = $("#productSelect"+rowNumber).val();
        getProductDetails(selectedProduct, rowNumber);
    });

    $(".quotation-product").on('change',function(){
        var productId = $(this).val();
        var productRowId = $(this).attr('id');
        var rowNumber = productRowId.match(/\d+/)[0];
        getProductDetails(productId,rowNumber);
    });
});
</script>