<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:10 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Create Quotation')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <div class="page-wrapper" xmlns="http://www.w3.org/1999/html">
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
                                    <h1>Create Quotation</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/quotation/manage">Manage Quotations</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Quotation</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-11">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="productRowCount" value="1">
                                            <form role="form" id="QuotationCreateForm" class="form-horizontal">
                                                {!! csrf_field() !!}
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="GeneralTab">
                                                        <fieldset>
                                                            <legend>Project</legend>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Client Name</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="client_id" name="client_id">
                                                                        <option value=""> -- Select Client -- </option>
                                                                        @foreach($clients as $client)
                                                                            <option value="{{$client['id']}}"> {{$client['company']}} </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Project Name</label>
                                                                <div class="col-md-6">
                                                                    <input class="form-control" id="project" name="project">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Project Site Name</label>
                                                                <div class="col-md-6">
                                                                    <input class="form-control" id="project_site" name="project_site">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-1 col-md-offset-1">

                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend> Products <a class="btn btn-success btn-md col-md-offset-9" id="addProduct">Add Product</a></legend>
                                                            <table class="table table-bordered" id="productTable" style="overflow: scroll">
                                                                <tr>
                                                                    <th style="width: 18%"> Category </th>
                                                                    <th style="width: 18%"> Product </th>
                                                                    <th style="width: 25%"> Description</th>
                                                                    <th style="width: 10%"> Rate</th>
                                                                    <th style="width: 8%"> unit</th>
                                                                    <th style="width: 10%"> Quantity </th>
                                                                    <th  style="width: 10%"> Amount </th>
                                                                    <th> Action </th>
                                                                </tr>
                                                                <tr id="Row1">
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <select class="form-control quotation-product-table quotation-category" id="categorySelect1" name="category_id[]">
                                                                                @foreach($categories as $category)
                                                                                    <option value="{{$category['id']}}"> {{$category['name']}} </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <select class="form-control quotation-product-table quotation-product" name="product_id[]" id="productSelect1" disabled>

                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input name="product_description[]" class="form-control quotation-product-table" onclick="replaceEditor(1)" id="productDescription1" type="text" readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input name="product_rate[]" type="number" step="any" class="form-control quotation-product-table" id="productRate1" type="text" onchange="calculateAmount(1)" onkeyup="calculateAmount(1)" readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input name="product_rate[]" class="form-control quotation-product-table" id="productUnit1" type="text" readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number" step="any" class="form-control quotation-product-table" name="product_quantity[]" id="productQuantity1" onchange="calculateAmount(1)" onkeyup="calculateAmount(1)" readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="text" name="product_amount[]" class="form-control quotation-product-table" id="productAmount1" readonly>
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
                                                                                    <a href="javascript:void(0);" onclick="removeRow(1)">
                                                                                        Remove
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <div class="col-md-3 col-md-offset-5">
                                                                <a class="btn btn-wide btn-primary" id="next1">
                                                                    Edit Material Cost
                                                                </a>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="tab-pane fade in" id="MaterialsTab">

                                                    </div>
                                                    <div class="tab-pane fade in" id="ProfitMarginsTab">


                                                    </div>
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
</div>
@endsection
@section('javascript')
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/custom/admin/quotation/quotation.js"></script>
<script src="/assets/custom/admin/quotation/validations.js"></script>
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>

<script>
    $(document).ready(function(){
        CreateQuotation.init();

        /*var citiList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/material/auto-suggest/%QUERY",
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            id:data.id,
                            name:data.name,
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        citiList.initialize();
        $('.typeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: citiList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [

                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
            },
        }).on('typeahead:selected', function (obj, datum) {
            var POData = $.parseJSON(JSON.stringify(datum));
            POData.name = POData.name.replace(/\&/g,'%26');
            $("#name").val(POData.name);
            $("#QuotationCreateForm").append($("<input>", {'id': "project_id",
                'type': 'hidden',
                'value': POData.id,
                'name': "project_id"
            }));
        })
            .on('typeahead:open', function (obj, datum) {

            });*/
    });
</script>
@endsection
