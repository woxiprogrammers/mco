<div class="page-header">
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="javascript:void(0)">
                    <img src="/assets/global/img/logo.jpg" alt="logo" class="logo-default">
                </a>
            </div>
            <!-- END LOGO -->
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
            <div class="hor-menu  ">
                <ul class="nav navbar-nav">
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a href="/"> Dashboard
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li aria-haspopup="true" class="menu-dropdown mega-menu-dropdown">
                        <a href="javascript:;"> Structure
                        </a>

                        <ul class="dropdown-menu pull-left">
                            <li aria-haspopup="true"  class="dropdown-submenu ">
                                <a href="javascript:void(0);" class="nav-link nav-toggle ">
                                    <i class="fa fa-building-o"></i> Manage Structure
                                </a>
                                <ul class="dropdown-menu pull-left">
                                    <li aria-haspopup="true">
                                        <a href="/category/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-sitemap"></i> Category
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/material/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-bars"></i> Material
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/product/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-dropbox"></i> Product
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/profit-margin/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-dollar"></i> Profit Margin
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/units/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-balance-scale"></i> Units
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/summary/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-book"></i> Summary
                                        </a>
                                    </li>
                                    <li aria-haspopup="true">
                                        <a href="/tax/manage" class="nav-link nav-toggle ">
                                            <i class="fa fa-money"></i> Tax
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li aria-haspopup="true">
                                <a href="/user/manage" class="nav-link nav-toggle ">
                                    <i class="fa fa-users"></i> Manage Users
                                </a>
                            </li>
                            <li aria-haspopup="true"  class="dropdown-submenu ">
                                <a href="javascript:void(0);" class="nav-link nav-toggle ">
                                    <i class="fa fa-folder"></i> Manage Sites
                                </a>
                                <ul class="dropdown-menu pull-left">
                                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                        <a href="/client/manage">
                                            <i class="fa fa-users"></i> Manage Client
                                            <span class="arrow"></span>
                                        </a>
                                    </li>
                                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                                        <a href="/project/manage">
                                            <i class="fa fa-cubes"></i> Manage Project
                                            <span class="arrow"></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a href="/quotation/manage"> Quotations
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown">
                        <a href="/bill/manage"> Bills
                            <span class="arrow"></span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- END MEGA MENU -->
        </div>
    </div>
    <!-- END HEADER MENU -->
    </div>
