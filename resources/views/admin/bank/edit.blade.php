@extends('layout.master')
@section('title','Constro | Create Bank')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->
                                <div class="page-title">
                                    <h1>Edit Bank</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/bank/manage">Manage Bank</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Bank</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <input type="hidden" id="bank_id" value="{{$bank['id']}}">
                                            <ul class="nav nav-tabs nav-tabs-lg">
                                                <li class="active">
                                                    <a href="#editBank" data-toggle="tab"> Edit Bank </a>
                                                </li>
                                                <li>
                                                    <a href="#transactionBankTab" data-toggle="tab"> Transactions </a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="editBank">
                                                    <form role="form" id="create-bank" class="form-horizontal" method="post" action="/bank/edit/{{$bank['id']}}">
                                                        {!! csrf_field() !!}
                                                        <input name="_method" value="put" type="hidden">
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label">Bank Name</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{$bank['bank_name']}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label">Account Number</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="account_number" name="account_number" value="{{$bank['account_number']}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label">IFS Code</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="ifs_code" name="ifs_code" value="{{$bank['ifs_code']}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label">Branch ID</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="branch_id" name="branch_id" value="{{$bank['branch_id']}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label">Branch Name</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="branch_name" name="branch_name" value="{{$bank['branch_name']}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-manage-bank'))
                                                            <div class="form-actions noborder row">
                                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                                    <button type="submit" class="btn red" id="submit" style="padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </form>
                                                </div>
                                                <div class="tab-pane fade in" id="transactionBankTab">
                                                    <div class="col-md-12 table-actions-wrapper">
                                                        <div class="col-md-2" style="text-align: right">
                                                            <label for="name" class="control-label">Total Amount : </label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control" id="total_amount" value="{{$bank['total_amount']}}" readonly>
                                                        </div>
                                                        <div class="col-md-2" style="text-align: right">
                                                            <label for="name" class="control-label">Balance Amount : </label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control" id="balance_amount" value="{{$bank['balance_amount']}}" readonly>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <a class="btn yellow pull-right" href="javascript:void(0);" id="transactionButton">
                                                                    <i class="fa fa-plus"></i>Transaction
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-scrollable">
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="bankTransactionTable">
                                                            <thead>
                                                                <tr>
                                                                    <th > Date </th>
                                                                    <th > Status </th>
                                                                    <th > User </th>
                                                                    <th > Amount </th>
                                                                    <th > Payment Method </th>
                                                                    <th > Reference Number </th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th> </th>
                                                                    <th> </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_name" id="search_name"> </th>
                                                                    <th> </th>
                                                                    <th> </th>
                                                                    <th>
                                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="transactionModal" role="dialog">
                                <div class="modal-dialog transaction-modal">
                                    <!-- Modal content-->
                                    <div class="modal-content" style="overflow: scroll !important;">
                                        <div class="modal-header">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4" style="font-size: 18px"> Bank Transaction</div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <form id="transactionForm" action="/bank/transaction/create/{{$bank['id']}}" method="POST">
                                                {!! csrf_field() !!}
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label class="control-label pull-right"> Amount :</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input class="form-control" name="amount" >
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label class="control-label pull-right"> Payment Type :</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="paymentType" name="payment_type_id">
                                                            @foreach($paymentModes as $paymentMode)
                                                                <option value="{{$paymentMode['id']}}">{{$paymentMode['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-md-4">
                                                        <label class="control-label pull-right">Date : </label>
                                                    </div>
                                                    <div class="col-md-6 date date-picker" data-date-end-date="0d">
                                                        <input type="text" style="width: 70%" id="date" name="date" />
                                                        <button class="btn btn-sm default" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label class="control-label pull-right"> Reference No :</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input class="form-control" name="reference_number" id="reference_no">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label class="control-label pull-right"> Remark :</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input class="form-control" name="remark" id="remark">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-set red pull-right">
                                                        <i class="fa fa-check" style="font-size: large"></i>
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
<script src="/assets/custom/admin/bank/validation.js" type="application/javascript"></script>
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

<script src="/assets/custom/admin/bank/transaction-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        CreateBank.init();
        $('#date').attr("readonly", "readonly");
        var date = new Date();
        $('#date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
        $("#transactionButton").on('click',function(){
            $("#transactionModal").modal('show');
        });
        $('#bankTransactionTable').DataTable();
        $("input[name='search_name']").on('keyup',function(){
            $(".filter-submit").trigger('click');
        });
    });
</script>

@endsection
