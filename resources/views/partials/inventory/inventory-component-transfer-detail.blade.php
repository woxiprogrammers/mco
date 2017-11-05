<?php
/**
 * Created by Ameya Joshi.
 * Date: 30/10/17
 * Time: 11:35 AM
 */
?>
@if(isset($inventoryComponentTransfer->grn) && $inventoryComponentTransfer->grn != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                GRN  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$inventoryComponentTransfer->grn}}
            </label>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Status  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            @if(strcasecmp( 'IN',$inventoryComponentTransfer->transferType->type))
                IN - From {{$inventoryComponentTransfer->transferType->name}}
            @else
                OUT - To {{$inventoryComponentTransfer->transferType->name}}
            @endif
        </label>
    </div>
</div>
@if(isset($inventoryComponentTransfer->quantity) && $inventoryComponentTransfer->quantity != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Quantity  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$inventoryComponentTransfer->quantity}}
            </label>
        </div>
    </div>
@endif
@if(isset($inventoryComponentTransfer->unit_id) && $inventoryComponentTransfer->unit_id != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Unit  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$inventoryComponentTransfer->unit->name}}
            </label>
        </div>
    </div>
@endif
@if(isset($inventoryComponentTransfer->remark) && $inventoryComponentTransfer->remark != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Remark  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->remark}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->source_name))
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Source Name  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->source_name}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->bill_number) && $inventoryComponentTransfer->bill_number != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Bill number  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->bill_number}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->bill_amount) && $inventoryComponentTransfer->bill_amount != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Bill amount  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->bill_amount}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->vehicle_number) && $inventoryComponentTransfer->vehicle_number != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Vehicle number  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->vehicle_number}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->in_time) && $inventoryComponentTransfer->in_time != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            In Time  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->in_time}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->out_time) && $inventoryComponentTransfer->out_time != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Out Time  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->out_time}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->payment_type_id) && $inventoryComponentTransfer->payment_type_id != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Payment Method  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->payment->name}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->date) && $inventoryComponentTransfer->date != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Date  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {!! date('d M Y',strtotime($inventoryComponentTransfer->date)) !!}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->next_maintenance_hour) && $inventoryComponentTransfer->next_maintenance_hour != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Next Maintenance Hour  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->next_maintenance_hour}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->comment_data) && $inventoryComponentTransfer->comment_data != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Comment  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->comment_data}}
        </label>
    </div>
</div>
@endif
@if(isset($inventoryComponentTransfer->user_id) && $inventoryComponentTransfer->user_id != '')
<div class="row">
    <div class="col-md-5 col-md-offset-1">
        <label class="control-label pull-right">
            Reference User  :
        </label>
    </div>
    <div class="col-md-6">
        <label class="control-label pull-left">
            {{$inventoryComponentTransfer->user->first_name}}  {{$inventoryComponentTransfer->user->last_name}}
        </label>
    </div>
</div>
@endif
@if(count($inventoryComponentTransferImages) > 0)
    <div class="row">
        <div class="col-md-3 col-md-offset-2">
            <label class="control-label pull-right">
                <b>Images:</b>
            </label>
        </div>
    </div>
    @foreach($inventoryComponentTransferImages as $imagePath)
        <div class="row">
            <div class="col-md-3 col-md-offset-4">
                <span class="pull-right">
                    <img src="{{$imagePath}}" alt="Inventory Component Transfer Images">
                </span>
            </div>
        </div>
    @endforeach
@endif