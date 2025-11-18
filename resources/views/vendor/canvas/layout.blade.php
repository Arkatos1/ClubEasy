<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} ― Canvas</title>

    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css', 'vendor/canvas') }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Karla&family=Merriweather:wght@400;700&display=swap">

    @if(\Canvas\Canvas::enabledDarkMode($jsVars['user']['dark_mode']))
        <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.8.0/build/styles/sunburst.min.css">
    @else
        <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.8.0/build/styles/github.min.css">
    @endif

    <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.8.0/build/highlight.min.js"></script>
</head>
<body class="mb-5"
    @if(\Canvas\Canvas::enabledDarkMode($jsVars['user']['dark_mode'])) data-theme="dark" @endif
    @if(\Canvas\Canvas::usingRightToLeftLanguage($jsVars['user']['locale'])) data-lang="rtl" @endif
>

@if(!\Canvas\Canvas::assetsUpToDate())
    <div class="alert alert-danger border-0 text-center rounded-0 mb-0">
        {{ trans('canvas::app.assets_are_not_up_to_date') }}
        {{ trans('canvas::app.to_update_run') }}<br/><code>php artisan canvas:publish</code>
    </div>
@endif

<div id="canvas">
    <router-view></router-view>
</div>

<script>
    window.Canvas = @json($jsVars);
</script>

<script type="text/javascript" src="{{ mix('js/app.js', 'vendor/canvas') }}"></script>

<script>
// Add the back button next to Canvas logo and keep it there during SPA navigation
function addBackButton() {
    const canvasLogo = document.querySelector('.navbar-brand');
    if (canvasLogo && !document.querySelector('.back-to-app-btn')) {
        // Create the back button
        const backButton = document.createElement('a');
        backButton.href = '{{ url("/administration") }}';
        backButton.className = 'back-to-app-btn btn btn-outline-primary btn-sm ml-3';
        backButton.innerHTML = '← Zpátky do hlavní aplikace';

        // Insert the button right after the Canvas logo
        canvasLogo.parentNode.insertBefore(backButton, canvasLogo.nextSibling);
        return true;
    }
    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    addBackButton();

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const navbarBrand = document.querySelector('.navbar-brand');
                const existingButton = document.querySelector('.back-to-app-btn');

                if (navbarBrand && !existingButton) {
                    addBackButton();
                }
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    if (window.Canvas && window.Canvas.app) {
        const originalPush = window.history.pushState;
        window.history.pushState = function() {
            originalPush.apply(this, arguments);
            setTimeout(addBackButton, 100);
        };

        const originalReplace = window.history.replaceState;
        window.history.replaceState = function() {
            originalReplace.apply(this, arguments);
            setTimeout(addBackButton, 100);
        };
    }

    setInterval(function() {
        const navbarBrand = document.querySelector('.navbar-brand');
        const existingButton = document.querySelector('.back-to-app-btn');

        if (navbarBrand && !existingButton) {
            addBackButton();
        }
    }, 1000);
});
</script>
</body>
</html>
