<div>
    <div class="row">
        <div class="col-md-offset-2 col-md-3" style="text-align: right">
            <label for="name" class="control-label"> Subtotal </label>
        </div>
        <div class="col-md-4">
            <label for="name" class="control-label"> {{$transactionData['subtotal']}} </label>
        </div>
    </div>

    @foreach($transactionData['taxes'] as $tax)
        <div class="row">
            <div class="col-md-offset-2 col-md-3" style="text-align: right">
                <label for="name" class="control-label"> {{$tax['name']}} </label>
            </div>
            <div class="col-md-4">
                <label for="name" class="control-label"> {{$tax['amount']}} </label>
            </div>
        </div>
    @endforeach
    <div class="row">
        <div class="col-md-offset-2 col-md-3" style="text-align: right">
            <label for="name" class="control-label"> Total </label>
        </div>
        <div class="col-md-3">
            <label for="name" class="control-label"> {{$transactionData['total']}} </label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-2 col-md-3" style="text-align: right">
            <label for="name" class="control-label"> Remark </label>
        </div>
        <div class="col-md-8">
            {!! $transactionData['remark'] !!}
        </div>
    </div>
</div>
