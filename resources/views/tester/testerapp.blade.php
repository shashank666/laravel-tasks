<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <link rel="icon" href="/opined.ico" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')
    <style>body { display: none; }</style>

    <link rel="stylesheet" href="/public_admin/assets/fonts/feather/feather.min.css">
    <link rel="stylesheet" href="/public_admin/assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/public_admin/assets/css/theme.min.css" id="stylesheetLight">
    <link rel="stylesheet" href="/public_admin/assets/css/theme-dark.min.css" id="stylesheetDark">

    @stack('styles')

    <script src="/public_admin/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/public_admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public_admin/assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.8.2/js/all.js"></script>

    @stack('scripts')

  </head>
  <body>

    @include('admin.partials.theme-setting')
    @include('admin.partials.modal_search')
    @include('admin.partials.modal_notifications')

    @include('admin.partials.sidebar')
    @include('admin.partials.navbar')

    <div class="main-content">
    @include('admin.partials.navbar_main')
    @yield('content')
    </div>
    <script src="/public_admin/assets/js/theme.js"></script>

  </body>
</html>
