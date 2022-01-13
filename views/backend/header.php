<?php
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>

    <base href="<?= base_url() ?>"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= Translate::sprint("Dashboard") ?> | <?= APP_NAME ?></title>

    <link rel="icon" href="<?= base_url("views/skin/backend/images/favicon.ico") ?>" type="image/x-icon">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/bootstrap/css/bootstrap.min.css") ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/select2/select2.min.css") ?>">
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/dist/css/AdminLTE.css") ?>">
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/datatables/dataTables.bootstrap.css") ?>">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/datepicker/datepicker3.css") ?>">
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/daterangepicker/daterangepicker-bs3.css") ?>">
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/iCheck/all.css") ?>">


    <link rel="stylesheet" href="<?= base_url("views/skin/backend/dist/css/skins/skin-light.css") ?>">

    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet"
          href="<?= base_url("views/skin/backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css") ?>">


    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/datatables/dataTables.bootstrap.css") ?>">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/minified/themes/default.min.css") ?>"
          type="text/css" media="all"/>


    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/datatables/jquery.dataTables.min.css") ?>"
          type="text/css" media="all"/>


    <link rel="stylesheet" href="//cdn.materialdesignicons.com/5.0.45/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url("views/skin/backend/plugins/colorpicker/bootstrap-colorpicker.css") ?>">
    <link rel="stylesheet" href="<?= base_url("views/skin/backend/custom_skin/style.css") ?>">


    <?php if (Translate::getDir() == "rtl"): ?>
        <link rel="stylesheet" href="<?= base_url("views/skin/backend/custom_skin/rtl.css") ?>">
    <?php endif; ?>


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112054244-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', '<?=DASHBOARD_ANALYTICS?>');
    </script>


    <script src="<?= base_url("views/skin/backend/plugins/jQuery/jQuery-2.1.4.min.js") ?>"></script>
    <?php TemplateManager::loadCssLibs() ?>

    <style>

        .skin-blue .main-header .logo {
            color: <?=DASHBOARD_COLOR?> !important;
        }

        .btn-primary {
            background-color: <?=DASHBOARD_COLOR?>;
            border-color: <?=DASHBOARD_COLOR?>;
            border: 1px solid <?=DASHBOARD_COLOR?>;
        }

        .bg-primary {
            background-color: <?=DASHBOARD_COLOR?>;
        }

        .btn-primary:hover {
            border: 1px solid <?=DASHBOARD_COLOR?> !important;
        }

        .skin-blue .sidebar-menu > li:hover > a, .skin-blue .sidebar-menu > li.active > a {
            border-left-color: <?=DASHBOARD_COLOR?>;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.hover {
            background-color: <?=DASHBOARD_COLOR?> !important;
            /*border: 1px solid #eeeeee !important;*/
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
            background-color: <?=DASHBOARD_COLOR?> !important;
            border-color: <?=DASHBOARD_COLOR?> !important;
        }

        a {
            color: <?=DASHBOARD_COLOR?>;
        }

        .skin-blue .main-header .navbar .sidebar-toggle {
            color: <?=DASHBOARD_COLOR?>;
        }

        .skin-blue .main-header .navbar .sidebar-toggle:hover {
            background-color: <?=DASHBOARD_COLOR?>;
        }

        .image-uploaded #delete {
            background-color: <?=DASHBOARD_COLOR?>;
        }

        #progress {
            border: 1px solid<?=DASHBOARD_COLOR?>;
        }

        #progress .percent {
            background: <?=DASHBOARD_COLOR?>;
        }

        .direct-chat-primary .right .direct-chat-text {
            background: <?=DASHBOARD_COLOR?>;
            border-color: <?=DASHBOARD_COLOR?>;
            color: #ffffff;
        }

        .nsup-btn {
            background: <?=DASHBOARD_COLOR?>;
        }

        .nsup-btn strong{
            color: #ffffff;
        }

        .full-width{
            width: 100%;
        }


    </style>
    <?php TemplateManager::loadScriptsLibs() ?>


</head>

<body class="hold-transition skin-blue sidebar-mini skin-custom-sf" dir="<?= Translate::getDir() ?>">
<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <?php if ($this->session->userdata('agent') == "mobile") { ?>

        <?php } else { ?>
            <a href="<?= admin_url("") ?>" class="logo">

                <span class="logo-lg"> <b style="text-transform: uppercase"><?= strtoupper(APP_NAME) ?></b></span>
                <span class="logo-mini"></span>

            </a>
        <?php } ?>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only"><?= Translate::sprint("Toggle navigation", "") ?></span>
            </a>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">


                    <?php CMS_Display::render('subscription_status_v1')?>
                    <?php CMS_Display::render('campaigns_pending_list_v1')?>

                    <!-- Control Sidebar Toggle Button -->
                    <?php CMS_Display::render('dropdown_v1')?>
                    <?php CMS_Display::render('language_dropdown_v1')?>

                    <?php CMS_Display::render('user_v1');?>



                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar user panel (optional) -->
            <!-- Sidebar Menu -->
            <?php $this->load->view("backend/sidebar"); ?>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>


