@if($data['purchase_create_permission'] == true)
    <div class="form-group row">
        <div class="col-md-3" style="text-align: right">
            <label for="purchase_order_amount_limit" class="control-label">Purchase Order Amount Limit</label>
            <span>*</span>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" id="purchase_order_amount_limit" name="purchase_order_amount_limit">
        </div>
    </div>
@endif

@if($data['peticash_management_permission'] == true)
    <div class="form-group row">
        <div class="col-md-3" style="text-align: right">
            <label for="purchase_peticash_amount_limit" class="control-label">Purchase Peticash Amount Limit</label>
            <span>*</span>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" id="purchase_peticash_amount_limit" name="purchase_peticash_amount_limit">
        </div>
    </div>
@endif