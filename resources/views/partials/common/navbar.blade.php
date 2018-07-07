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
                            <option value="{{$projectSite->project_site_id}}" selected>{{$projectSite->project_name}}</option>
                        @else
                            <option value="{{$projectSite->project_site_id}}">{{$projectSite->project_name}}</option>
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
                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin')
                                <li>
                                    <a href="/user/change-password">
                                        <i class="icon-key"></i> Change Password </a>
                                </li>
                            @endif
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
        <div class="container" style="width: 100%">
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
                                        <?php $hasCategoryPermission = \App\Helper\ACLHelper::checkModuleAcl('category'); ?>
                                        @if($hasCategoryPermission)
                                            <li aria-haspopup="true">
                                                <a href="/category/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-sitemap"></i> Category
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasMaterialPermission = \App\Helper\ACLHelper::checkModuleAcl('material'); ?>
                                        @if($hasMaterialPermission)
                                            <li aria-haspopup="true">
                                                <a href="/material/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-bars"></i> Material
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasProductPermission = \App\Helper\ACLHelper::checkModuleAcl('product'); ?>
                                        @if($hasProductPermission)
                                            <li aria-haspopup="true">
                                                <a href="/product/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-dropbox"></i> Product
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasProfitMarginPermission = \App\Helper\ACLHelper::checkModuleAcl('profit-margin'); ?>
                                        @if($hasProfitMarginPermission)
                                            <li aria-haspopup="true">
                                                <a href="/profit-margin/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-dollar"></i> Profit Margin
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasUnitsPermission = \App\Helper\ACLHelper::checkModuleAcl('units'); ?>
                                        @if($hasUnitsPermission)
                                            <li aria-haspopup="true">
                                                <a href="/units/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-balance-scale"></i> Units
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasSummaryPermission = \App\Helper\ACLHelper::checkModuleAcl('summary'); ?>
                                        @if($hasSummaryPermission)
                                            <li aria-haspopup="true">
                                                <a href="/summary/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-book"></i> Summary
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasTaxPermission = \App\Helper\ACLHelper::checkModuleAcl('tax'); ?>
                                        @if($hasTaxPermission)
                                            <li aria-haspopup="true">
                                                <a href="/tax/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-money"></i> Tax
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasManageExtraItemsPermission = \App\Helper\ACLHelper::checkModuleAcl('manage-extra-items'); ?>
                                        @if($hasManageExtraItemsPermission)
                                            <li aria-haspopup="true">
                                                <a href="/extra-item/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-plus"></i> Extra-Item
                                                </a>
                                            </li>
                                        @endif

                                        <?php $hasAssetManagementPermission = \App\Helper\ACLHelper::checkModuleAcl('asset-management'); ?>
                                        @if($hasAssetManagementPermission)
                                            <li aria-haspopup="true">
                                                <a href="/asset/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-bars"></i> Asset
                                                </a>
                                            </li>
                                        @endif
                                        @if(($user->roles[0]->role->slug == 'superadmin'))
                                            <li aria-haspopup="true">
                                                <a href="/address/manage" class="nav-link nav-toggle ">
                                                    <i class="fa fa-plus"></i> Address
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>

                            <?php $hasManageUserPermission = \App\Helper\ACLHelper::checkModuleAcl('manage-user'); ?>
                            @if($hasManageUserPermission)
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
                                        <?php $hasManageClientPermission = \App\Helper\ACLHelper::checkModuleAcl('manage-client'); ?>
                                        @if($hasManageClientPermission)
                                                <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                                    <a href="/client/manage">
                                                        <i class="fa fa-users"></i> Manage Client
                                                        <span class="arrow"></span>
                                                    </a>
                                                </li>
                                            @endif

                                        <?php $hasManageSitesPermission = \App\Helper\ACLHelper::checkModuleAcl('manage-sites'); ?>
                                        @if($hasManageSitesPermission)
                                                <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                                    <a href="/project/manage">
                                                        <i class="fa fa-cubes"></i> Manage Project
                                                        <span class="arrow"></span>
                                                    </a>
                                                </li>
                                        @endif
                                    </ul>
                                </li>
                                <?php $hasManageBankPermission = \App\Helper\ACLHelper::checkModuleAcl('manage-bank'); ?>
                                @if($hasManageBankPermission)
                                    <li  aria-haspopup="true">
                                        <a href="/bank/manage">
                                            <i class="fa fa-building"></i> Manage Bank
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <?php $hasQuotationPermission = \App\Helper\ACLHelper::checkModuleAcl('quotation'); ?>
                    @if($hasQuotationPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                            <a href="/quotation/manage/status#2" id="quotationNav"> Quotations
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
                            <?php $hasMaterialRequestPermission = \App\Helper\ACLHelper::checkModuleAcl('material-request');?>
                            @if($hasMaterialRequestPermission)
                                    <li aria-haspopup="true">
                                        <a href="/purchase/material-request/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-clipboard"></i> Material Request
                                            @if(($materialRequestNotificationCount) > 0)
                                                <span class="badge badge-success"><b>{{$materialRequestNotificationCount}}</b></span>
                                            @endif
                                        </a>
                                    </li>
                            @endif

                            <?php $hasPurchaseRequestPermission = \App\Helper\ACLHelper::checkModuleAcl('purchase-request');?>
                            @if($hasPurchaseRequestPermission)
                                <li aria-haspopup="true">
                                    <a href="/purchase/purchase-request/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-clipboard"></i> Purchase Request
                                        @if(($purchaseRequestNotificationCount) > 0)
                                            <span class="badge badge-success"><b>{{$purchaseRequestNotificationCount}}</b></span>
                                        @endif
                                    </a>
                                </li>
                            @endif


                            <?php $hasPurchaseOrderRequestPermission = \App\Helper\ACLHelper::checkModuleAcl('purchase-order-request');?>
                            @if($hasPurchaseOrderRequestPermission)
                                <li aria-haspopup="true">
                                    <a href="/purchase/purchase-order-request/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-clipboard"></i> Purchase Order Request
                                        @if(($purchaseOrderRequestNotificationCount) > 0)
                                            <span class="badge badge-success"><b>{{$purchaseOrderRequestNotificationCount}}</b></span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            <?php $hasPurchaseOrderPermission = \App\Helper\ACLHelper::checkModuleAcl('purchase-order');?>
                            @if($hasPurchaseOrderPermission)
                                <li aria-haspopup="true">
                                    <a href="/purchase/purchase-order/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-shopping-cart"></i> Purchase Order
                                        @if($purchaseOrderNotificationCount > 0)
                                            <span class="badge badge-success"><b>{{$purchaseOrderNotificationCount}}</b></span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            <?php $hasPurchaseOrderBillPermission = \App\Helper\ACLHelper::checkModuleAcl('purchase-bill-entry');?>
                            @if($hasPurchaseOrderBillPermission)
                                <li aria-haspopup="true">
                                    <a href="/purchase/pending-po-bills/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-calculator"></i> Pending PO Bills
                                    </a>
                                </li>
                            @endif

                            <?php $hasPurchaseOrderBillPermission = \App\Helper\ACLHelper::checkModuleAcl('purchase-bill-entry');?>
                            @if($hasPurchaseOrderBillPermission)
                                <li aria-haspopup="true">
                                    <a href="/purchase/purchase-order-bill/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-calculator"></i> Purchase Order Billing
                                    </a>
                                </li>
                            @endif

                            @if($hasPurchaseOrderPermission)
                            <li aria-haspopup="true">
                                <a href="/purchase/vendor-mail/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-envelope"></i> Vendor Mails
                                </a>
                            </li>
                            @endif
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
                                <?php $hasInventoryInOutTransferPermission = \App\Helper\ACLHelper::checkModuleAcl('inventory-in-out-transfer');?>
                                @if($hasInventoryInOutTransferPermission)
                                    <li aria-haspopup="true">
                                        <a href="/inventory/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-home"></i> Store Keeper
                                        </a>
                                    </li>
                                @endif

                                <?php $hasComponentTransferPermission = \App\Helper\ACLHelper::checkModuleAcl('component-transfer');?>
                                @if($hasComponentTransferPermission)
                                    <li aria-haspopup="true">
                                        <a href="/inventory/transfer/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-arrow-right"></i> Site Transfer
                                            @if(($inventorySiteTransferNotificationCount) > 0)
                                                <span class="badge badge-success">{!! $inventorySiteTransferNotificationCount !!}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif

                                <?php $hasSiteTransferPermission = \App\Helper\ACLHelper::checkModuleAcl('component-transfer-bill-entry')?>
                                @if($hasSiteTransferPermission == true)
                                    <li aria-haspopup="true">
                                        <a href="/inventory/transfer/billing/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-sitemap"></i> Site Transfer Billing
                                        </a>
                                    </li>
                                @endif

                                <?php $hasAssetMaintenancePermission = \App\Helper\ACLHelper::checkModuleAcl('asset-maintainance')?>
                                @if($hasAssetMaintenancePermission)
                                    <li aria-haspopup="true">
                                        <a href="/asset/maintenance/request/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-square"></i> Asset Maintenance
                                        </a>
                                    </li>
                                @endif

                                <?php $hasAssetMaintenanceApprovalPermission = \App\Helper\ACLHelper::checkModuleAcl('asset-maintenance-approval')?>
                                @if($hasAssetMaintenanceApprovalPermission)
                                    <li aria-haspopup="true">
                                        <a href="/asset/maintenance/request/approval/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-check-square"></i> Asset Maintenance Approval
                                        </a>
                                    </li>
                                @endif

                                <?php $hasAssetMaintenanceBillingPermission = \App\Helper\ACLHelper::checkModuleAcl('asset-maintenance-billing')?>
                                @if($hasAssetMaintenanceBillingPermission)
                                    <li aria-haspopup="true">
                                        <a href="/asset/maintenance/request/bill/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-calculator"></i> Asset Maintenance Billing
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <?php $hasChecklistPermission = \App\Helper\ACLHelper::checkModuleAcl('checklist');?>
                    @if($hasChecklistPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> Checklist
                            <span class="arrow"></span>
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <?php $hasChecklistCategoryPermission = \App\Helper\ACLHelper::checkModuleAcl('checklist-category');?>
                            @if($hasChecklistCategoryPermission)
                                <li aria-haspopup="true">
                                    <a href="/checklist/category-management/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-list-alt"></i> Category Management
                                    </a>
                                </li>
                            @endif

                            <?php $hasChecklistStructurePermission = \App\Helper\ACLHelper::checkModuleAcl('checklist-structure');?>
                            @if($hasChecklistStructurePermission)
                                <li aria-haspopup="true">
                                    <a href="/checklist/structure/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-sitemap"></i> Checklist Structure
                                    </a>
                                </li>
                            @endif

                            <?php $hasChecklistSiteAssignmentPermission = \App\Helper\ACLHelper::checkModuleAcl('checklist-structure-site-assignment');?>
                            @if($hasChecklistSiteAssignmentPermission)
                                <li aria-haspopup="true">
                                    <a href="/checklist/site-assignment/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-arrow-right"></i> Project Site Assignment
                                    </a>
                                </li>
                            @endif

                            <!--<li aria-haspopup="true">
                                <a href="/checklist/user-assignment/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> User Checklist Management
                                </a>
                            </li>-->
                        </ul>
                    </li>
                    @endif

                    <?php $hasDrawingPermission = \App\Helper\ACLHelper::checkModuleAcl('drawing');?>
                    @if($hasDrawingPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                            <a> Drawing
                                <span class="arrow"></span>
                            </a>
                            <ul class="dropdown-menu pull-left">
                                <?php $hasDrawingCategoryPermission = \App\Helper\ACLHelper::checkModuleAcl('drawing-category');?>
                                @if($hasDrawingCategoryPermission)
                                    <li aria-haspopup="true">
                                        <a href="/drawing/category-management/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-list-alt"></i> Category Management
                                        </a>
                                    </li>
                                @endif

                                <?php $hasAddDrawingPermission = \App\Helper\ACLHelper::checkModuleAcl('add-drawing');?>
                                @if($hasAddDrawingPermission)
                                    <li aria-haspopup="true">
                                        <a href="/drawing/images/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-plus"></i> Add Image
                                        </a>
                                    </li>
                                @endif

                                <?php $hasManageDrawingPermission = \App\Helper\ACLHelper::checkModuleAcl('manage-drawing');?>
                                @if($hasManageDrawingPermission)
                                    <li aria-haspopup="true">
                                        <a href="/drawing/images/manage-drawings" class="nav-link nav-toggle ">
                                            <i class="fa fa-edit"></i> Manage Drawings
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <?php $hasPeticashPermission = \App\Helper\ACLHelper::checkModuleAcl('peticash');?>
                    @if($hasPeticashPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a> Peticash
                            @if(($peticashSalaryRequestApprovalNotificationCount) > 0)
                                <span class="badge badge-success">{!! $peticashSalaryRequestApprovalNotificationCount !!}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu pull-left">
                            <?php $hasMasterPeticashPermission = \App\Helper\ACLHelper::checkModuleAcl('master-peticash-account');?>
                            @if($hasMasterPeticashPermission)
                                <li aria-haspopup="true">
                                    <a href="/peticash/master-peticash-account/manage" class="nav-link nav-toggle ">
                                        <i class="fa fa-money"></i> Master Peticash Account
                                    </a>
                                </li>
                            @endif

                            <?php $hasSitewisePeticashPermission = \App\Helper\ACLHelper::checkModuleAcl('sitewise-peticash-account');?>
                            @if($hasSitewisePeticashPermission)
                            <li aria-haspopup="true">
                                <a href="/peticash/sitewise-peticash-account/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-sitemap"></i> Sitewise Peticash Account
                                </a>
                            </li>
                            @endif

                            <?php $hasPeticashManagementPermission = \App\Helper\ACLHelper::checkModuleAcl('salary-request-handler');?>
                            @if($hasPeticashManagementPermission)
                                <li aria-haspopup="true">
                                    <a href="/peticash/peticash-approval-request/manage-salary-list" class="nav-link nav-toggle ">
                                        <i class="fa fa-check"></i> Peticash Salary Request Approval
                                        @if(($peticashSalaryRequestApprovalNotificationCount) > 0)
                                            <span class="badge badge-success">{!! $peticashSalaryRequestApprovalNotificationCount !!}</span>
                                        @endif
                                    </a>
                                </li>

                                <li aria-haspopup="true">
                                    <a href="/peticash/salary-request/create" class="nav-link nav-toggle ">
                                        <i class="fa fa-sitemap"></i> Peticash Salary Request
                                    </a>
                                </li>
                            @endif

                            <?php $hasPeticashManagementPermission = \App\Helper\ACLHelper::checkModuleAcl('peticash-management');?>
                            @if($hasPeticashManagementPermission)
                                <li aria-haspopup="true" class="dropdown-submenu">
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

                    <?php $hasSubcontractorPermission = \App\Helper\ACLHelper::checkModuleAcl('subcontractor');?>
                    @if($hasSubcontractorPermission)
                        <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                            <a href="/subcontractor/subcontractor-structure/manage"> Subcontractor
                                <span class="arrow"></span>
                            </a>
                        </li>
                    @endif

                    <?php $hasGeneralAwarenessPermission = \App\Helper\ACLHelper::checkModuleAcl('general-awareness');?>
                    @if($hasGeneralAwarenessPermission)
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
                    @endif

                    <?php $hasDPRPermission = \App\Helper\ACLHelper::checkModuleAcl('dpr');?>
                    @if($hasDPRPermission)
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
                    @endif

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
