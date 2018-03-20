@section('navBar')
<div class="page-header">
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="/">
                    <img src="/assets/global/img/logo.jpg" alt="logo" class="logo-default">
                </a>
            </div>
            <!-- END LOGO -->
            <div class="col-md-3 col-md-offset-2 form-group globalSiteSelect">
                <select class="bs-select form-control" data-style="btn-info" data-width="100%" id="globalProjectSite">
                    @foreach($globalProjectSites as $projectSite)
                        @if($projectSite->project_site_id == $selectGlobalProjectSite)
                            <option value="{{$projectSite->project_site_id}}" selected>{{$projectSite->project_name}} - {{$projectSite->project_site_name}}</option>
                        @else
                            <option value="{{$projectSite->project_site_id}}">{{$projectSite->project_name}} - {{$projectSite->project_site_name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler"></a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-hoverable" class after "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                    <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->
                    <!-- END NOTIFICATION DROPDOWN -->
                    <!-- BEGIN TODO DROPDOWN -->
                    <!-- END TODO DROPDOWN -->
                    <li class="droddown dropdown-separator">
                        <span class="separator"></span>
                    </li>
                    <!-- BEGIN INBOX DROPDOWN -->
                    <!-- END INBOX DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img alt="" class="img-circle" src="/assets/layouts/layout3/img/no-user.jpg">
                            <span class="username username-hide-mobile">{{ Auth::user()->first_name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="/logout">
                                    <i class="icon-key"></i> Log Out </a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                    <!-- BEGIN QUICK SIDEBAR TOGGLER -->

                    <!-- END QUICK SIDEBAR TOGGLER -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
    </div>
    <!-- END HEADER TOP -->
    <!-- BEGIN HEADER MENU -->
    <div class="page-header-menu">
        <div class="container">
            <!-- BEGIN HEADER SEARCH BOX -->
            <!-- END HEADER SEARCH BOX -->
            <!-- BEGIN MEGA MENU -->
            <!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
            <!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
            <div class="hor-menu">
                <ul class="nav navbar-nav">
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a href="/dashboard"> Dashboard
                            <span class="arrow"></span>
                        </a>
                    </li>

                    <?php $hasStructurePermission = \App\Helper\ACLHelper::checkModuleAcl('structure'); ?>
                    @if($hasStructurePermission)
                        <li aria-haspopup="true" class="menu-dropdown mega-menu-dropdown">
                            <a href="javascript:;"> Structure
                            </a>
                            <ul class="dropdown-menu pull-left">
                                <li aria-haspopup="true"  class="dropdown-submenu ">
                                    <a href="javascript:void(0);" class="nav-link nav-toggle ">
                                        <i class="fa fa-building-o"></i> Manage Structure
                                    </a>
                                    <ul class="dropdown-menu pull-left">
                                        @if($user->hasPermissionTo('view-category'))
                                            <li aria-haspopup="true">
                                                <a href="/category/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-sitemap"></i> Category
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-material'))
                                            <li aria-haspopup="true">
                                                <a href="/material/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-bars"></i> Material
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-product'))
                                            <li aria-haspopup="true">
                                                <a href="/product/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-dropbox"></i> Product
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-profit-margin'))
                                            <li aria-haspopup="true">
                                                <a href="/profit-margin/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-dollar"></i> Profit Margin
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-units'))
                                            <li aria-haspopup="true">
                                                <a href="/units/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-balance-scale"></i> Units
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-summary'))
                                            <li aria-haspopup="true">
                                                <a href="/summary/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-book"></i> Summary
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-tax'))
                                            <li aria-haspopup="true">
                                                <a href="/tax/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-money"></i> Tax
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-manage-extra-items'))
                                            <li aria-haspopup="true">
                                                <a href="/extra-item/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-money"></i> Extra-Item
                                                </a>
                                            </li>
                                        @endif
                                            <li aria-haspopup="true">
                                                <a href="/asset/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-bars"></i> Asset
                                                </a>
                                            </li>
                                    </ul>
                                </li>
                                @if($user->hasPermissionTo('view-manage-user'))
                                    <li aria-haspopup="true" class="dropdown-submenu ">
                                        <a href="javascript:void(0);" class="nav-link nav-toggle ">
                                            <i class="fa fa-users"></i> Manage Users
                                        </a>
                                        <ul class="dropdown-menu pull-left">
                                            <li aria-haspopup="true">
                                                <a href="/vendors/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-sitemap"></i> Manage Vendors
                                                </a>
                                            </li>
                                            <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                                <a href="/role/manage">
                                                    <i class="fa fa-users"></i> Manage Roles
                                                    <span class="arrow"></span>
                                                </a>
                                            </li>
                                            <li aria-haspopup="true">
                                                <a href="/user/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-sitemap"></i> Manage Users
                                                </a>
                                            </li>
                                            <li aria-haspopup="true">
                                                <a href="/labour/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-sitemap"></i> Manage Employee
                                                </a>
                                            </li>
                                            <li aria-haspopup="true">
                                                <a href="/subcontractor/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-sitemap"></i> Manage Subcontractor
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                <li aria-haspopup="true"  class="dropdown-submenu ">
                                    <a href="javascript:void(0);" class="nav-link nav-toggle ">
                                        <i class="fa fa-folder"></i> Manage Sites
                                    </a>
                                    <ul class="dropdown-menu pull-left">
                                        @if($user->hasPermissionTo('view-manage-client'))
                                            <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                                <a href="/client/manage">
                                                    <i class="fa fa-users"></i> Manage Client
                                                    <span class="arrow"></span>
                                                </a>
                                            </li>
                                        @endif
                                        @if($user->hasPermissionTo('view-manage-sites'))
                                            <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                                <a href="/project/manage">
                                                    <i class="fa fa-cubes"></i> Manage Project
                                                    <span class="arrow"></span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>

                                <li  aria-haspopup="true">
                                    <a href="/bank/manage">
                                        <i class="fa fa-folder"></i> Manage Bank
                                    </a>
                                </li>
                                </ul>
                            </li>
                    @endif
                    <?php $hasQuotationPermission = \App\Helper\ACLHelper::checkModuleAcl('quotation'); ?>
                    @if($hasQuotationPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                            <a href="/quotation/manage/status#2"> Quotations
                                <span class="arrow"></span>
                            </a>
                        </li>
                    @endif
                    <?php $hasBillPermission = \App\Helper\ACLHelper::checkModuleAcl('bill'); ?>
                    @if($hasBillPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                            <a href="/bill/manage/project-site"> Bills
                                <span class="arrow"></span>
                            </a>
                        </li>
                    @endif
                    <?php $hasPurchasePermission = \App\Helper\ACLHelper::checkModuleAcl('purchase');?>
                    @if($hasPurchasePermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        @if(($purchaseRequestNotificationCount + $materialRequestNotificationCount + $purchaseOrderRequestNotificationCount + $purchaseOrderNotificationCount) > 0)
                            <a> Purchase
                                <span class="badge badge-success">{!! $purchaseRequestNotificationCount + $materialRequestNotificationCount + $purchaseOrderRequestNotificationCount + $purchaseOrderNotificationCount!!}</span>
                            </a>
                        @else
                            <a> Purchase

                            </a>
                        @endif
                        <ul class="dropdown-menu pull-left">
                            <li aria-haspopup="true">
                                <a href="/purchase/material-request/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Material Request
                                    @if(($materialRequestNotificationCount) > 0)
                                        <span class="badge badge-success"><b>{{$materialRequestNotificationCount}}</b></span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/purchase/purchase-request/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Purchase Request
                                    @if(($purchaseRequestNotificationCount) > 0)
                                        <span class="badge badge-success"><b>{{$purchaseRequestNotificationCount}}</b></span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/purchase/purchase-order-request/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Purchase Order Request
                                    @if(($purchaseOrderRequestNotificationCount) > 0)
                                        <span class="badge badge-success"><b>{{$purchaseOrderRequestNotificationCount}}</b></span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/purchase/purchase-order/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Purchase Order
                                    @if($purchaseOrderNotificationCount > 0)
                                        <span class="badge badge-success"><b>{{$purchaseOrderNotificationCount}}</b></span>
                                    @endif
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/purchase/purchase-order-bill/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Purchase Order Billing
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/purchase/vendor-mail/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Vendor Mails
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <?php $hasInventoryPermission = \App\Helper\ACLHelper::checkModuleAcl('inventory');?>
                    @if($hasInventoryPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                            <a> Inventory
                                @if(($inventorySiteTransferNotificationCount) > 0)
                                    <span class="badge badge-success">{!! $inventorySiteTransferNotificationCount !!}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu pull-left">
                                <li aria-haspopup="true">
                                    <a href="/inventory/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-sitemap"></i> Store Keeper
                                    </a>
                                </li>
                                <li aria-haspopup="true">
                                    <a href="/inventory/transfer/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-sitemap"></i> Site Transfer
                                        @if(($inventorySiteTransferNotificationCount) > 0)
                                            <span class="badge badge-success">{!! $inventorySiteTransferNotificationCount !!}</span>
                                        @endif
                                    </a>
                                </li>
                                <?php $hasSiteTransferPermission = \App\Helper\ACLHelper::checkModuleAcl('component-transfer-bill-entry')?>
                                @if($hasSiteTransferPermission == true)
                                    <li aria-haspopup="true">
                                        <a href="/inventory/transfer/billing/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-sitemap"></i> Site Transfer Billing
                                        </a>
                                    </li>
                                @endif
                                <li aria-haspopup="true">
                                    <a href="/asset/maintenance/request/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-sitemap"></i> Asset Maintenance
                                    </a>
                                </li>
                                <li aria-haspopup="true">
                                    <a href="/asset/maintenance/request/approval/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-sitemap"></i> Asset Maintenance Approval
                                    </a>
                                </li>
                                <li aria-haspopup="true">
                                    <a href="/asset/maintenance/request/bill/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-bars"></i> Asset Maintenance Billing
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> Checklist
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <li aria-haspopup="true">
                                <a href="/checklist/category-management/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Category Management
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/checklist/structure/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Checklist Structure
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/checklist/site-assignment/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Project Site Assignment
                                </a>
                            </li>
                            <!--<li aria-haspopup="true">
                                <a href="/checklist/user-assignment/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> User Checklist Management
                                </a>
                            </li>-->
                        </ul>
                    </li>
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> Drawing
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <li aria-haspopup="true">
                                <a href="/drawing/category-management/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Category Management
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/drawing/images/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Add Image
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/drawing/images/manage-drawings" class="nav-link nav-toggle ">
                                    <i class="fa fa-bars"></i> Manage Drawings
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php $hasPeticashPermission = \App\Helper\ACLHelper::checkModuleAcl('peticash');?>
                    @if($hasPeticashPermission  || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> Peticash
                            @if(($peticashSalaryRequestApprovalNotificationCount) > 0)
                                <span class="badge badge-success">{!! $peticashSalaryRequestApprovalNotificationCount !!}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu pull-left">
                            @if($user->hasPermissionTo('view-master-account') || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                            <li aria-haspopup="true">
                                <a href="/peticash/master-peticash-account/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-money"></i> Master Peticash Account
                                </a>
                            </li>
                            @endif
                            @if($user->hasPermissionTo('view-sitewise-account') || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                            <li aria-haspopup="true">
                                <a href="/peticash/sitewise-peticash-account/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Sitewise Peticash Account
                                </a>
                            </li>
                            @endif
                            @if($user->hasPermissionTo('approve-peticash-management')  || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                                <li aria-haspopup="true">
                                    <a href="/peticash/peticash-approval-request/manage-salary-list" class="nav-link nav-toggle ">
                                        <i class="fa fa-check"></i> Peticash Salary Request Approval
                                        @if(($peticashSalaryRequestApprovalNotificationCount) > 0)
                                            <span class="badge badge-success">{!! $peticashSalaryRequestApprovalNotificationCount !!}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                            <li aria-haspopup="true">
                                <a href="/peticash/salary-request/create" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Peticash Salary Request
                                </a>
                            </li>
                            @if($user->hasPermissionTo('view-peticash-management')  || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                                <li aria-haspopup="true" class="dropdown-submenu">
                               <!-- <a href="/peticash/peticash-management/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-database"></i> Peticash Management
                                </a>-->
                                <a href="javascript:void(0);" class="nav-link nav-toggle ">
                                    <i class="fa fa-database"></i> Peticash Management
                                </a>
                                <ul class="dropdown-menu pull-left">
                                    <li aria-haspopup="true">
                                        <a href="/peticash/peticash-management/purchase/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-sitemap"></i> Purchase
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/peticash/peticash-management/salary/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-bars"></i> Salary
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a href="/subcontractor/subcontractor-structure/manage"> Subcontractor
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> General Awareness
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <li aria-haspopup="true">
                                <a href="/awareness/category-management/main-category-manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Category Management
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/awareness/file-management/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-file" aria-hidden="true"></i> File Management
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> DPR
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <li aria-haspopup="true">
                                <a href="/dpr/category_manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Category Management
                                </a>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/dpr/manage_dpr" class="nav-link nav-toggle ">
                                    <i class="fa fa-file" aria-hidden="true"></i> DPR Management
                                </a>
                            </li>
                        </ul>
                    </li>
                    @if(($user->roles[0]->role->slug == 'superadmin'))
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a href="/reports"> Reports
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
        </div>
            <!-- END MEGA MENU -->
    </li>
</ul>
</div>
</div>
    <!-- END HEADER MENU -->
</div>
</div>
@endsection
