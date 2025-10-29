
<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="google-adsense-account" content="ca-pub-9499062034028644">
    <meta name="_token" content="{!! csrf_token() !!}" />
    <meta name="_url" content="{!! URL::to('/') !!}" />
    <meta name="theme" content="{{ $theme }}">
    <title> {{ config('app.name') }}  @if(isset($title)) | {{ $title }} @endif </title>

    <link rel="shortcut icon" href="{{ asset('assets/system/images/app_logo.jpg') }}" type="image/x-icon">

    <link
      href="{{ asset('assets/system/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
      rel="stylesheet"
    />
    <link href="{{ asset('assets/system/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/plugins/toastr/css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/plugins/jquery-asColorPicker-master/css/asColorPicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/select2/dist/css/select2.min.css') }}" rel="stylesheet">    
    <link href="{{ asset('assets/system/datetime-picker/jquery.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/plugins/sweetalert/css/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/system/css/style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/system/css/override.css') }}" type="text/css" rel="stylesheet">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9499062034028644"
     crossorigin="anonymous"></script>
    
</head>

<body class="h-100">
    

    <div id="main-wrapper">

        <div class="nav-header">
            <div class="brand-logo">
                <a href="{{ route('home') }}">
                    <b class="logo-abbr"><img src="{{ asset('assets/system/images/app_logo.jpg') }}" alt=""> </b>
                    <span class="logo-compact"><img src="{{ asset('assets/system/images/app_logo.jpg') }}" alt=""></span>
                    <span class="brand-title">
                        <h4>{{ config('app.name') }}</h4>
                    </span>
                </a>
            </div>
        </div>

        <div class="header">    
            <div class="header-content clearfix">
                
                <div class="nav-control">
                    <div class="hamburger">
                        <span class="toggle-icon"><i class="icon-menu"></i></span>
                    </div>
                </div>

                <div class="header-right">
                    <ul class="clearfix">
                        
                        <li class="icons">
                            <a href="javascript:void(0)" data-action="toggle-theme">
                                @if ($theme == 'light')
                                    <i class="fa fa-moon text-secondary fa-action"></i>
                                @else
                                    <i class="fa fa-sun text-warning fa-action"></i>
                                @endif
                            </a>
                        </li>
                        <li class="icons dropdown">
                            <div class="user-img c-pointer position-relative"   data-toggle="dropdown">
                                <span class="activity"></span>
                                <img src="{{ asset('assets/system/images/acrtfm_logo.png') }}" height="40" width="40" alt="UI">
                            </div>
                            <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                                <div class="dropdown-content-body">
                                    <ul>
                                        <li>
                                            <a href="#">
                                                <i class="icon-envelope-open"></i> <span>Settings</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-user"></i> <span>Profile</span>
                                            </a>
                                        </li> 
                                        <li>
                                            &nbsp;
                                        </li>
                                        <li><a href="javascript:void(0)" data-action="logout"><i class="icon-key"></i> <span>Logout</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @include('partials.auth.sidebar')

        <div class="content-body">  
            <div class="row page-titles">
                <div class="col-md-6">
                    <h1 class="">{{ $title }}</h1>
                    <p class="module-description">{{ $description }}</p>
                </div>
                <div class="col-md-6">
                    @if(isset($right_panel))
                        {!! $right_panel !!}
                    @endif
                </div>
            </div>
        