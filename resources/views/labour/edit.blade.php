@extends('layout.master')
@section('title','Constro | Edit Labour')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/css/app.css" rel="stylesheet" type="text/css" />
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
                                    <h1>Create Labour</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/labour/manage">Manage Labour</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Labour</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="editLabour" class="form-horizontal" method="post" action="/labour/edit/{{$labour['id']}}">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name" value="{{$labour['name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Contact number:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="contact_no" name="mobile" value="{{$labour['mobile']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Gender :</label>
                                                            <span>*</span>
                                                        </div>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <div class="col-md-6 mt-radio-inline">
                                                            @if($labour['gender'] != null)
                                                                @if($labour['gender'] == 'f')
                                                                    <label class="mt-radio" style="margin-left: 13px">
                                                                        <input type="radio" name="gender" id="female" value="f" checked=""> Female
                                                                        <span></span>
                                                                    </label>
                                                                    <label class="mt-radio">
                                                                        <input type="radio" name="gender" id="male" value="m"> Male
                                                                        <span></span>
                                                                    </label>
                                                                @else
                                                                    <label class="mt-radio" style="margin-left: 13px">
                                                                        <input type="radio" name="gender" id="female" value="f" > Female
                                                                        <span></span>
                                                                    </label>
                                                                    <label class="mt-radio">
                                                                        <input type="radio" name="gender" id="male" value="m" checked=""> Male
                                                                        <span></span>
                                                                    </label>
                                                                @endif
                                                            @else
                                                                <label class="mt-radio" style="margin-left: 13px">
                                                                    <input type="radio" name="gender" id="female" value="f"> Female
                                                                    <span></span>
                                                                </label>
                                                                <label class="mt-radio">
                                                                    <input type="radio" name="gender" id="male" value="m"> Male
                                                                    <span></span>
                                                                </label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Profile Picture :</label>
                                                            <span>*</span>
                                                        </div>                                                        &nbsp;&nbsp;&nbsp;
                                                        <div class="col-md-6">
                                                            <input id="imageupload" type="file" class="btn blue" multiple />
                                                            <br />
                                                            <div class="row">
                                                                <div id="preview-image" class="row">
                                                                    @if($profileImagePath != null)
                                                                        <div class="col-md-4">
                                                                            <img src="{{$profileImagePath}}" class="thumbimage" />
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Employee Type:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="employee_type" name="employee_type" value="{{$labour->employeeType->name}}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Labour ID:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{$labour['employee_id']}}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Per day Wages</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="per_day_wage" name="per_day_wages" value="{{$labour['per_day_wages']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Select Project Site</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select id="project_site" class="form-control" name="project_site_id">
                                                                @if($labour['project_site_id'] == null)
                                                                    <option value="-1">Select Project Site</option>
                                                                @endif
                                                                @foreach($projectSites as $projectSite)
                                                                    @if($labour['project_site_id'] == $projectSite['id'])
                                                                        <option value = "{!! $projectSite['id'] !!}" selected>{!! $projectSite['name'] !!}</option>
                                                                    @else
                                                                        <option value = "{!! $projectSite['id'] !!}">{!! $projectSite['name'] !!}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Address:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="address" name="address" value="{{$labour['address']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Pan Card:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="pan_card" name="pan_card" value="{{$labour['pan_card']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Aadhaar Card:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="aadhaar_card" name="aadhaar_card" value="{{$labour['aadhaar_card']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Designation:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="designation" name="designation" value="{{$labour['designation']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Joining Date:</label>
                                                            <span>*</span>
                                                        </div>
                                                        @if($labour['joining_date'] != null)
                                                            <div class="col-md-6 date date-picker">
                                                                <input type="text"  name="joining_date" value="{{date('m/d/Y',strtotime($labour['joining_date']))}}" id="joining_date" readonly>
                                                                <button class="btn btn-sm default" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="col-md-4 date date-picker">
                                                                <input type="text" name="joining_date" id="joining_date">
                                                                <button class="btn btn-sm default" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Termination Date:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6 date date-picker">
                                                            @if($labour['termination_date'] != null)
                                                                <input type="text"  name="termination_date" value="{{date('m/d/Y',strtotime($labour['termination_date']))}}" id="termination_date" readonly>
                                                                <button class="btn btn-sm default" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            @else
                                                                <input type="text" name="termination_date" id="termination_date">
                                                                <button class="btn btn-sm default" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Email:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="email" name="email" value="{{$labour['email']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Bank Account Number:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{$labour['bank_account_number']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Bank Name:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{$labour['bank_name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Branch Id:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="branch_id" name="branch_id" value="{{$labour['branch_id']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Account Holder Name:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{$labour['account_holder_name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Branch Name:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="branch_name" name="branch_name" value="{{$labour['branch_name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">IFS Code:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="ifs_code" name="ifs_code" value="{{$labour['ifs_code']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">

                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                        </div>
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
@endsection
@section('javascript')
    <script src="/assets/custom/labour/labour.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            EditLabour.init();

            $("#imageupload").on('change', function () {
                var countFiles = $(this)[0].files.length;
                var imgPath = $(this)[0].value;
                var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                var image_holder = $("#preview-image");
                image_holder.empty();
                if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                    if (typeof (FileReader) != "undefined") {
                        for (var i = 0; i < countFiles; i++) {
                            var reader = new FileReader()
                            reader.onload = function (e) {
                                var imagePreview = '<div class="col-md-4"><input type="hidden" name="profile_image" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
                                image_holder.html(imagePreview);
                            };
                            image_holder.show();
                            reader.readAsDataURL($(this)[0].files[i]);
                            $("#grnImageUplaodButton").show();
                        }
                    } else {
                        alert("It doesn't supports");
                    }
                } else {
                    alert("Select Only images");
                }
            });
        });
    </script>
@endsection
