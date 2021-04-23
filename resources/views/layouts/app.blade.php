<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex nofollow">
  <meta name="referrer" content="no-referrer-when-downgrade">
  <style>
    .hvjs .script--disabled {
      display: none;
    }
  </style>

  <script type="text/javascript">
    document.getElementsByTagName('html')[0].className += 'hvjs';
  </script>

  <link rel="preconnect" href="https://www.googletagmanager.com">
  <link rel="preconnect" href="https://p.typekit.net">
  <link rel="preconnect" href="https://use.typekit.net">

  @meta

  <link rel="apple-touch-icon" sizes="57x57" href="/icons/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/icons/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/icons/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/icons/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/icons/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/icons/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/icons/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/icons/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/icons/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/icons/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
  <link rel="manifest" href="/icons/manifest.json" crossorigin="use-credentials">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/icons/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <link rel="stylesheet" href="/css/app.css">
  <link rel="stylesheet" href="https://use.typekit.net/onc8trv.css">

  <script type="text/javascript">
    window.INITIAL_STATE = "{!! addslashes(json_encode($state)) !!}";
  </script>
</head>

<body>
  <div id="app" tabindex="-1">
    @include('includes.header')

    <div class="container script--disabled">
      @yield('content')
    </div>

  </div>

  <script src="{{ mix('js/manifest.js') }}"></script>
  <script src="{{ mix('js/vendor.js') }}"></script>
  <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
