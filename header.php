<?php
session_start();
if(!isset($_SESSION['userid']) && intval($_SESSION['userid'])==0){
	header('Location: logout.php');
}

include_once 'config.ini.php';
?>
<!DOCTYPE html>
<html>

<!-- Mirrored from adminlte.io/themes/v3/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 15 Dec 2019 11:48:05 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>2ndIncome | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="../../../code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="dist/css/jquery.dataTables.min.css">
    
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.css">
    <!-- Google Font: Source Sans Pro -->
    <script src="plugins/jquery/jquery.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed text-sm">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="home.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Contact</a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <div class="form-inline ml-4">
            
                <div id="global-search-results">
				<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown">
				<div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" id="global-search" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
				</div>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-lg ">
                    
                </div>
            </li>
			</ul>
                </div>
            
        </div>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-comments"></i>
                    <span class="badge badge-danger navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Brad Diesel
                                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">Call me whenever you can...</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    John Pierce
                                    <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">I got your message bro</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Nora Silvester
                                    <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">The subject goes here</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                </div>
            </li>
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="home.php" class="brand-link">
            <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                 style="opacity: .8">
            <span class="brand-text font-weight-light">2nd Income</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">Ramesh N</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="lenders.php" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Lenders
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="borrowers.php" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Borrowers
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="interest.php" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Interest
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settlement.php" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Settlement
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="transactions.php" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Transactions
                            </p>
                        </a>
                    </li>
					<li class="nav-item">
                        <a href="uncleared_returns.php" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Uncleared Returns
                            </p>
                        </a>
                    </li>
					<li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Reports
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="reports_lender_borrower_loans.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
					<p>Loans</p>
                </a>
              </li>
                <li class="nav-item">
                    <a href="report_settlement.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Settlement</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="report_lender_transactions.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Lender Transactions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="report_interest_free_loans.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Interest Free Loans</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="report_interest_collected.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Interest / Collected</p>
                    </a>
                </li>
                </li> <li class="nav-item">
                    <a href="report_consolidate_report.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Consolidate Summary</p>
                    </a>
                </li>
				<li class="nav-item">
                    <a href="report_lender_interest_analysis.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Lender Interest Analysis</p>
                    </a>
                </li>
				<li class="nav-item">
                    <a href="report_interest_rate_wise_loans.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>ROI Wise Loans</p>
                    </a>
                </li>
				<li class="nav-item">
                    <a href="report_unsettled_lender_interest.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Unsettled lender interest</p>
                    </a>
                </li>
				<li class="nav-item">
                    <a href="report_long_unsettled_loans.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Unsettled Long Loans</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="report_oneyear_settled_interest.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Past 12 months Interest</p>
                    </a>
                </li> <li class="nav-item">
                    <a href="report_transactions.php" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Transaction Summary</p>
                    </a>
                
                

            </ul>
          </li>
					
                </ul>
            </nav>
			<div style="position:absolute; bottom : 0px">
			
                        <a href="logout.php" class="nav-link">
                            <i class="nav-icon fas fa-th" style="font-size: 1.1rem; padding:5px"></i>
							<span style="transition: margin-left .3s linear, opacity .3s ease, visibility .3s ease;">
                                Logout
							</span>
                        </a>
            
			</div>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">

        </div>
        <!-- /.content-header -->